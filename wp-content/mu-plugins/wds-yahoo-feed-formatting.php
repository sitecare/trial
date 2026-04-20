<?php
/**
 * Yahoo syndication formatting updates for site feeds.
 *
 * Applies shared feed formatting rules to the default RSS2 feed and the
 * custom `/alt` feed, while preserving existing media namespace output used
 * by current syndication integrations.
 */

/**
 * Determine whether the current request is one of the article syndication feeds.
 *
 * Limit these Yahoo-specific changes to the main RSS2 feed and the custom
 * `/alt` feed so other feed types do not get unexpected output changes.
 *
 * @return bool
 */
function wds_acme_is_target_syndication_feed() {
	if ( ! is_feed() ) {
		return false;
	}

	$feed = get_query_var( 'feed' );

	return empty( $feed ) || 'rss2' === $feed || 'alt' === $feed;
}

/**
 * Build a plain-text description for syndicated feeds.
 *
 * @param string $excerpt Existing excerpt content.
 * @return string
 */
function wds_acme_get_syndication_plain_text_summary( $excerpt = '' ) {
	$post = get_post();

	if ( ! $post instanceof WP_Post ) {
		return trim( wp_strip_all_tags( $excerpt ) );
	}

	$summary = get_post_meta( $post->ID, 'wds-acme-custom-excerpt', true );

	if ( empty( trim( wp_strip_all_tags( $summary ) ) ) ) {
		$summary = $post->post_excerpt;
	}

	if ( empty( trim( wp_strip_all_tags( $summary ) ) ) ) {
		$summary = wp_trim_words(
			wp_strip_all_tags( strip_shortcodes( $post->post_content ) ),
			55,
			'...'
		);
	}

	$summary = html_entity_decode( wp_strip_all_tags( $summary ), ENT_QUOTES, get_bloginfo( 'charset' ) );
	$summary = preg_replace( '/\s+/u', ' ', $summary );

	return trim( $summary );
}

/**
 * Filter RSS descriptions down to plain text only.
 *
 * @param string $excerpt Existing excerpt content.
 * @return string
 */
function wds_acme_filter_syndication_feed_excerpt( $excerpt ) {
	if ( ! wds_acme_is_target_syndication_feed() ) {
		return $excerpt;
	}

	return wds_acme_get_syndication_plain_text_summary( $excerpt );
}
add_filter( 'the_excerpt_rss', 'wds_acme_filter_syndication_feed_excerpt', 20 );

/**
 * Remove comments metadata from feed items.
 *
 * @param bool $open Whether comments are open.
 * @return bool
 */
function wds_acme_disable_comments_in_syndication_feeds( $open ) {
	if ( wds_acme_is_target_syndication_feed() ) {
		return false;
	}

	return $open;
}
add_filter( 'comments_open', 'wds_acme_disable_comments_in_syndication_feeds', 20 );

/**
 * Report zero comments in feed context so comment RSS tags are omitted.
 *
 * @param int $count Existing comment count.
 * @return int
 */
function wds_acme_zero_comment_count_in_syndication_feeds( $count ) {
	if ( wds_acme_is_target_syndication_feed() ) {
		return 0;
	}

	return $count;
}
add_filter( 'get_comments_number', 'wds_acme_zero_comment_count_in_syndication_feeds', 20 );

/**
 * Normalize content:encoded HTML for syndication feeds.
 *
 * @param string $content   Feed content.
 * @param string $feed_type Feed type.
 * @return string
 */
function wds_acme_filter_syndication_feed_content( $content, $feed_type ) {
	if ( ! wds_acme_is_target_syndication_feed() ) {
		return $content;
	}

	return wds_acme_get_syndication_feed_content( $content, get_post(), $feed_type );
}
add_filter( 'the_content_feed', 'wds_acme_filter_syndication_feed_content', 20, 2 );

/**
 * Prepare content:encoded HTML for feed consumers that reject responsive image
 * attributes and comments markup.
 *
 * @param string       $content   Feed content HTML.
 * @param WP_Post|null $post      Current post object.
 * @param string       $feed_type Feed type.
 * @return string
 */
function wds_acme_get_syndication_feed_content( $content, $post = null, $feed_type = 'rss2' ) {
	unset( $feed_type );

	$post = get_post( $post );

	if ( empty( $content ) || ! $post instanceof WP_Post || ! class_exists( 'DOMDocument' ) ) {
		return $content;
	}

	$document = new DOMDocument( '1.0', 'UTF-8' );
	$options  = 0;

	if ( defined( 'LIBXML_HTML_NOIMPLIED' ) ) {
		$options |= LIBXML_HTML_NOIMPLIED;
	}

	if ( defined( 'LIBXML_HTML_NODEFDTD' ) ) {
		$options |= LIBXML_HTML_NODEFDTD;
	}

	$previous_libxml_setting = libxml_use_internal_errors( true );
	$loaded = $document->loadHTML(
		'<?xml encoding="utf-8" ?><div id="wds-feed-root">' . $content . '</div>',
		$options
	);
	libxml_clear_errors();
	libxml_use_internal_errors( $previous_libxml_setting );

	if ( ! $loaded ) {
		return $content;
	}

	$root = $document->getElementById( 'wds-feed-root' );

	if ( ! $root ) {
		return $content;
	}

	$xpath = new DOMXPath( $document );

	wds_acme_remove_syndication_comment_nodes( $xpath );
	wds_acme_normalize_syndication_images( $document, $xpath );

	return wds_acme_get_syndication_feed_inner_html( $root );
}

/**
 * Remove comment-related markup from feed content.
 *
 * @param DOMXPath $xpath Document XPath helper.
 * @return void
 */
function wds_acme_remove_syndication_comment_nodes( $xpath ) {
	$queries = array(
		'//comment()',
		"//*[@id='comments' or @id='comment_wrap' or @id='respond' or @id='disqus_thread']",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' comments ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' comment ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' comment-list ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' comment-wrapper ')]",
		"//*[contains(concat(' ', normalize-space(@class), ' '), ' disqus ')]",
	);

	foreach ( $queries as $query ) {
		$nodes = wds_acme_dom_node_list_to_array( $xpath->query( $query ) );

		foreach ( $nodes as $node ) {
			if ( $node->parentNode ) {
				$node->parentNode->removeChild( $node );
			}
		}
	}
}

/**
 * Convert feed images into figure/img/figcaption markup with a single src.
 *
 * @param DOMDocument $document Parsed feed document.
 * @param DOMXPath    $xpath    Document XPath helper.
 * @return void
 */
function wds_acme_normalize_syndication_images( $document, $xpath ) {
	$images = wds_acme_dom_node_list_to_array( $xpath->query( '//img' ) );

	foreach ( $images as $image ) {
		$src = $image->getAttribute( 'src' );

		if ( empty( $src ) ) {
			continue;
		}

		$attachment_id = wds_acme_get_syndication_attachment_id_from_image( $image, $src );
		$caption       = '';
		$credit        = '';

		if ( $attachment_id ) {
			$full_size_image = wp_get_attachment_image_url( $attachment_id, 'full' );

			if ( $full_size_image ) {
				$image->setAttribute( 'src', $full_size_image );
			}

			$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

			if ( empty( $alt ) ) {
				$alt = get_the_title( $attachment_id );
			}

			if ( ! empty( $alt ) ) {
				$image->setAttribute( 'alt', $alt );
			}

			$caption = wp_get_attachment_caption( $attachment_id );
			$credit  = wds_acme_get_syndication_image_credit( $attachment_id );
		}

		$image->removeAttribute( 'srcset' );
		$image->removeAttribute( 'sizes' );
		$image->removeAttribute( 'loading' );
		$image->removeAttribute( 'decoding' );

		$figure = wds_acme_ensure_syndication_figure_wrapper( $document, $image );

			if ( ! $figure ) {
				continue;
			}

			$figcaption_text = '';

			if ( wds_acme_should_add_syndication_figcaption( $caption, $credit ) ) {
				$figcaption_text = wds_acme_get_syndication_figcaption_text( $caption, $credit );
			}

			wds_acme_replace_syndication_figcaption( $document, $figure, $figcaption_text );
		}
	}

/**
 * Resolve an attachment ID from an image element or source URL.
 *
 * @param DOMElement $image Image node.
 * @param string     $src   Image source URL.
 * @return int
 */
function wds_acme_get_syndication_attachment_id_from_image( $image, $src ) {
	$class_name = $image->getAttribute( 'class' );

	if ( preg_match( '/wp-image-([0-9]+)/', $class_name, $matches ) ) {
		return absint( $matches[1] );
	}

	$upload_dir = wp_upload_dir();

	if ( empty( $upload_dir['baseurl'] ) || false === strpos( $src, $upload_dir['baseurl'] . '/' ) ) {
		return 0;
	}

	$file = basename( wp_parse_url( $src, PHP_URL_PATH ) );

	if ( empty( $file ) ) {
		return 0;
	}

	$query = new WP_Query(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'key'     => '_wp_attachment_metadata',
					'value'   => $file,
					'compare' => 'LIKE',
				),
			),
		)
	);

	if ( empty( $query->posts ) ) {
		return 0;
	}

	foreach ( $query->posts as $attachment_id ) {
		$metadata = wp_get_attachment_metadata( $attachment_id );

		if ( empty( $metadata['file'] ) ) {
			continue;
		}

		$original_file = basename( $metadata['file'] );
		$sized_files   = ! empty( $metadata['sizes'] ) ? wp_list_pluck( $metadata['sizes'], 'file' ) : array();

		if ( $original_file === $file || in_array( $file, $sized_files, true ) ) {
			return (int) $attachment_id;
		}
	}

	return 0;
}

/**
 * Build caption text that includes both caption and credit when available.
 *
 * @param string $caption Attachment caption.
 * @param string $credit  Image credit.
 * @return string
 */
function wds_acme_get_syndication_figcaption_text( $caption, $credit ) {
	$caption = trim( wp_strip_all_tags( $caption ) );
	$credit  = trim( wp_strip_all_tags( $credit ) );
	$parts   = array();

	if ( ! empty( $caption ) ) {
		$parts[] = $caption;
	}

	if ( ! empty( $credit ) ) {
		$parts[] = 'Credit: ' . $credit;
	}

	return implode( ' | ', $parts );
}

/**
 * Retrieve the image credit from existing media metadata.
 *
 * @param int $attachment_id Attachment ID.
 * @return string
 */
function wds_acme_get_syndication_image_credit( $attachment_id ) {
	$fields = array(
		'licensor_name',
		'image_credit',
		'photo_credit',
	);

	foreach ( $fields as $field_name ) {
		$value = function_exists( 'get_field' ) ? get_field( $field_name, $attachment_id ) : '';

		if ( ! empty( $value ) ) {
			return is_scalar( $value ) ? (string) $value : '';
		}
	}

	return '';
}

/**
 * Determine whether a figure needs a figcaption in order to satisfy feed rules.
 *
 * @param string $caption Attachment caption.
 * @param string $credit  Image credit.
 * @return bool
 */
function wds_acme_should_add_syndication_figcaption( $caption, $credit ) {
	return '' !== trim( wp_strip_all_tags( $caption ) ) || '' !== trim( wp_strip_all_tags( $credit ) );
}

/**
 * Ensure an image is wrapped in a figure element without breaking linked images.
 *
 * @param DOMDocument $document Parsed feed document.
 * @param DOMElement $image Image node.
 * @return DOMElement|null
 * @throws \DOMException
 */
function wds_acme_ensure_syndication_figure_wrapper( $document, $image ) {
	$parent = $image->parentNode;

	if ( $parent instanceof DOMElement && 'figure' === strtolower( $parent->nodeName ) ) {
		return $parent;
	}

	$target = $image;

	if ( $parent instanceof DOMElement && 'a' === strtolower( $parent->nodeName ) ) {
		$target = $parent;
		$parent = $target->parentNode;
	}

	if ( $parent instanceof DOMElement && 'p' === strtolower( $parent->nodeName ) && wds_acme_node_contains_only_media_wrapper( $parent, $target ) ) {
		$target = $parent;
		$parent = $target->parentNode;
	}

	if ( ! $parent instanceof DOMElement ) {
		return null;
	}

	$figure = $document->createElement( 'figure' );
	$parent->replaceChild( $figure, $target );
	$figure->appendChild( $target );

	return $figure;
}

/**
 * Determine whether a paragraph only wraps a single media node plus whitespace.
 *
 * @param DOMNode $parent Parent node.
 * @param DOMNode $target Image or anchor node.
 * @return bool
 */
function wds_acme_node_contains_only_media_wrapper( $parent, $target ) {
	foreach ( $parent->childNodes as $child ) {
		if ( $child->isSameNode( $target ) ) {
			continue;
		}

		if ( XML_TEXT_NODE === $child->nodeType && '' === trim( $child->textContent ) ) {
			continue;
		}

		return false;
	}

	return true;
}

/**
 * Replace existing figcaptions with a single normalized one.
 *
 * @param DOMDocument $document Parsed feed document.
 * @param DOMElement $figure Figure wrapper.
 * @param string $contents Figcaption text.
 * @return void
 * @throws \DOMException
 */
function wds_acme_replace_syndication_figcaption( $document, $figure, $contents ) {
	$children = wds_acme_dom_node_list_to_array( $figure->childNodes );

	foreach ( $children as $child ) {
		if ( XML_ELEMENT_NODE === $child->nodeType && 'figcaption' === strtolower( $child->nodeName ) ) {
			$figure->removeChild( $child );
		}
	}

	if ( '' === $contents ) {
		return;
	}

	$figcaption = $document->createElement( 'figcaption' );
	$figcaption->appendChild( $document->createTextNode( $contents ) );
	$figure->appendChild( $figcaption );
}

/**
 * Convert a DOMNodeList to an array to safely mutate the document.
 *
 * @param DOMNodeList $node_list Node list.
 * @return array
 */
function wds_acme_dom_node_list_to_array( $node_list ) {
	$nodes = array();

	if ( ! $node_list instanceof DOMNodeList ) {
		return $nodes;
	}

	foreach ( $node_list as $node ) {
		$nodes[] = $node;
	}

	return $nodes;
}

/**
 * Get a node's inner HTML.
 *
 * @param DOMNode $node Parent node.
 * @return string
 */
function wds_acme_get_syndication_feed_inner_html( $node ) {
	$html = '';

	foreach ( $node->childNodes as $child ) {
		$html .= $node->ownerDocument->saveHTML( $child );
	}

	return $html;
}
