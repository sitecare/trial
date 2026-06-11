<?php
/**
 * Plugin Name: Luis Yahoo Sports Feed Compliant
 * Description: Formats the standard RSS2 and /alt feeds to meet Yahoo Sports syndication requirements while preserving existing MSN media:content output.
 * Version:     1.0.0
 * Author:      Luis
 */

/**
 * Brings both the standard RSS2 feed and the /alt feed into compliance with
 * Yahoo Sports syndication requirements, without touching the existing MSN
 * media:content output already handled by the syndication updates class.
 *
 * Three issues addressed:
 *   1. Images: remove srcset/sizes, resolve full-size URL, wrap in figure+figcaption.
 *   2. Description: strip all HTML so the <description> tag is plain text only.
 *   3. Comments: hide comment counts and links so Yahoo-unsupported tags are omitted.
 */

class Luis_Yahoo_Sports_Compliant {

	/** @var Luis_Yahoo_Sports_Compliant|null */
	private static $instance = null;

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
	}

	private function __construct() {
		// Fix 1 — reformat images inside content:encoded.
		add_filter( 'the_content_feed', array( $this, 'clean_feed_images' ), 20, 2 );

		// Fix 2 — plain-text description.
		add_filter( 'the_excerpt_rss', array( $this, 'strip_description_html' ), 20 );

		// Fix 3 — hide comment metadata from feed items.
		add_filter( 'comments_open',       array( $this, 'hide_comments_in_feed' ), 20 );
		add_filter( 'pings_open',          array( $this, 'hide_comments_in_feed' ), 20 );
		add_filter( 'get_comments_number', array( $this, 'hide_comment_count_in_feed' ), 20 );
	}

	// -------------------------------------------------------------------------
	// Scope guard — only touch the two syndication feeds
	// -------------------------------------------------------------------------

	/**
	 * Returns true only for the main RSS2 feed and the custom /alt feed.
	 * All other feed types (JSON, Atom, category feeds, etc.) are left alone.
	 *
	 * @return bool
	 */
	private function is_yahoo_feed() {
		if ( ! is_feed() ) {
			return false;
		}

		return in_array( get_query_var( 'feed' ), array( '', 'rss2', 'alt' ), true );
	}

	// -------------------------------------------------------------------------
	// Fix 1 — Reformat images in <content:encoded>
	// -------------------------------------------------------------------------

	/**
	 * Hooked to the_content_feed.
	 * Parses the HTML, rewrites every image, and returns the cleaned markup.
	 *
	 * @param string $content
	 * @param string $feed_type
	 * @return string
	 */
	public function clean_feed_images( $content, $feed_type ) {
		if ( ! $this->is_yahoo_feed() || empty( $content ) || ! class_exists( 'DOMDocument' ) ) {
			return $content;
		}

		$doc  = new DOMDocument( '1.0', 'UTF-8' );
		$prev = libxml_use_internal_errors( true );

		$ok = $doc->loadHTML( '<?xml encoding="utf-8"?><div id="lys-root">' . $content . '</div>' );
		libxml_clear_errors();
		libxml_use_internal_errors( $prev );

		if ( ! $ok ) {
			return $content;
		}

		$root = $doc->getElementById( 'lys-root' );
		if ( ! $root ) {
			return $content;
		}

		$this->rewrite_all_images( $doc );

		return $this->extract_inner_html( $root );
	}

	/**
	 * Walk every <img> in the document and apply the full set of fixes.
	 *
	 * getElementsByTagName returns a live list, so we snapshot it first to
	 * avoid skipping nodes when the DOM is mutated during iteration.
	 *
	 * @param DOMDocument $doc
	 * @return void
	 */
	private function rewrite_all_images( DOMDocument $doc ) {
		$snapshot = array();
		foreach ( $doc->getElementsByTagName( 'img' ) as $img ) {
			$snapshot[] = $img;
		}

		foreach ( $snapshot as $img ) {
			$src = $img->getAttribute( 'src' );
			if ( empty( $src ) ) {
				continue;
			}

			$att_id = $this->lookup_attachment( $img, $src );

			if ( $att_id > 0 ) {
				$this->upgrade_to_full_size( $img, $att_id );
			}

			// Remove every attribute Yahoo or strict feed validators reject.
			foreach ( array( 'srcset', 'sizes', 'loading', 'decoding' ) as $attr ) {
				$img->removeAttribute( $attr );
			}

			$figure = $this->ensure_figure( $doc, $img );
			if ( $figure && $att_id > 0 ) {
				$this->apply_figcaption( $doc, $figure, $att_id );
			}
		}
	}

	/**
	 * Swap the img src to the full-size URL and ensure a clean alt attribute.
	 *
	 * @param DOMElement $img
	 * @param int        $att_id
	 * @return void
	 */
	private function upgrade_to_full_size( DOMElement $img, $att_id ) {
		$full_url = wp_get_attachment_image_url( $att_id, 'full' );
		if ( $full_url ) {
			$img->setAttribute( 'src', $full_url );
		}

		$alt = get_post_meta( $att_id, '_wp_attachment_image_alt', true );
		if ( empty( $alt ) ) {
			$alt = get_the_title( $att_id );
		}
		if ( ! empty( $alt ) ) {
			$img->setAttribute( 'alt', esc_attr( $alt ) );
		}
	}

	/**
	 * Resolve the WordPress attachment ID for an image element.
	 *
	 * Strategy (fastest to slowest):
	 *  1. wp-image-{id} CSS class — WP core stamps this on every inserted image.
	 *  2. data-id / data-attachment-id attributes used by some page builders.
	 *  3. attachment_url_to_postid() against the URL, retrying after stripping
	 *     any responsive size suffix (e.g. -800x600) from the filename.
	 *
	 * @param DOMElement $img
	 * @param string     $src
	 * @return int 0 when no match is found.
	 */
	private function lookup_attachment( DOMElement $img, $src ) {
		// 1. Class-based lookup — no DB query needed.
		if ( preg_match( '/\bwp-image-(\d+)\b/', $img->getAttribute( 'class' ), $m ) ) {
			return (int) $m[1];
		}

		// 2. Explicit data attributes.
		foreach ( array( 'data-id', 'data-attachment-id' ) as $data_attr ) {
			$val = (int) $img->getAttribute( $data_attr );
			if ( $val > 0 ) {
				return $val;
			}
		}

		// 3. URL-based lookup.
		// attachment_url_to_postid() needs the original (unsized) URL, so strip
		// any -WxH size suffix WordPress appends to resized images.
		$original_src = preg_replace( '/-\d+x\d+(\.[a-zA-Z0-9]+)$/', '$1', $src );

		$id = attachment_url_to_postid( $original_src );
		if ( ! $id && $original_src !== $src ) {
			// The regex didn't change anything or the sized URL happens to be stored.
			$id = attachment_url_to_postid( $src );
		}

		return (int) $id;
	}

	/**
	 * Wrap the image (or its closest meaningful ancestor) in a <figure> element.
	 *
	 * Handles three cases:
	 *  - Image already inside a <figure>          → return it unchanged.
	 *  - Image inside an <a> link                 → wrap the <a> so the link is preserved.
	 *  - Image is the sole content of a <p> block → replace the <p> with the figure.
	 *
	 * @param DOMDocument $doc
	 * @param DOMElement  $img
	 * @return DOMElement|null  The figure element, or null if wrapping failed.
	 */
	private function ensure_figure( DOMDocument $doc, DOMElement $img ) {
		$parent = $img->parentNode;

		if ( $parent instanceof DOMElement && 'figure' === strtolower( $parent->nodeName ) ) {
			return $parent;
		}

		// Climb up through <a> then <p> to find what we should actually replace.
		$target = $img;

		if ( $parent instanceof DOMElement && 'a' === strtolower( $parent->nodeName ) ) {
			$target = $parent;
			$parent = $target->parentNode;
		}

		if (
			$parent instanceof DOMElement &&
			'p' === strtolower( $parent->nodeName ) &&
			$this->sole_child( $parent, $target )
		) {
			$target = $parent;
			$parent = $target->parentNode;
		}

		if ( ! $parent instanceof DOMElement ) {
			return null;
		}

		$figure = $doc->createElement( 'figure' );
		$parent->replaceChild( $figure, $target );
		$figure->appendChild( $target );

		return $figure;
	}

	/**
	 * Return true when $subject is the only non-whitespace child of $container.
	 *
	 * @param DOMElement $container
	 * @param DOMNode    $subject
	 * @return bool
	 */
	private function sole_child( DOMElement $container, DOMNode $subject ) {
		foreach ( $container->childNodes as $sibling ) {
			if ( $sibling->isSameNode( $subject ) ) {
				continue;
			}
			if ( XML_TEXT_NODE === $sibling->nodeType && '' === trim( $sibling->textContent ) ) {
				continue;
			}
			return false;
		}
		return true;
	}

	/**
	 * Clear existing figcaptions from a figure and add one with caption + credit.
	 *
	 * The two values are joined with " | " when both are present.
	 * Nothing is added when neither caption nor credit is available.
	 *
	 * @param DOMDocument $doc
	 * @param DOMElement  $figure
	 * @param int         $att_id
	 * @return void
	 */
	private function apply_figcaption( DOMDocument $doc, DOMElement $figure, $att_id ) {
		// Remove any figcaptions already in the markup before adding ours.
		$stale = array();
		foreach ( $figure->childNodes as $child ) {
			if ( $child instanceof DOMElement && 'figcaption' === strtolower( $child->nodeName ) ) {
				$stale[] = $child;
			}
		}
		foreach ( $stale as $node ) {
			$figure->removeChild( $node );
		}

		$caption = trim( wp_strip_all_tags( (string) wp_get_attachment_caption( $att_id ) ) );

		$credit = '';
		if ( function_exists( 'get_field' ) ) {
			$raw_credit = get_field( 'licensor_name', $att_id );
			if ( ! empty( $raw_credit ) && is_scalar( $raw_credit ) ) {
				$credit = trim( wp_strip_all_tags( (string) $raw_credit ) );
			}
		}

		// Build an array of non-empty parts and join them.
		$parts = array_filter( array(
			$caption,
			$credit !== '' ? 'Credit: ' . $credit : '',
		) );

		if ( empty( $parts ) ) {
			return;
		}

		$figcaption = $doc->createElement( 'figcaption' );
		$figcaption->appendChild( $doc->createTextNode( implode( ' | ', $parts ) ) );
		$figure->appendChild( $figcaption );
	}

	// -------------------------------------------------------------------------
	// Fix 2 — Strip HTML from <description>
	// -------------------------------------------------------------------------

	/**
	 * Hooked to the_excerpt_rss.
	 * Returns a plain-text string with no HTML, decoded entities, and normalised whitespace.
	 *
	 * @param string $excerpt
	 * @return string
	 */
	public function strip_description_html( $excerpt ) {
		if ( ! $this->is_yahoo_feed() ) {
			return $excerpt;
		}

		return $this->to_plain_text( $excerpt );
	}

	/**
	 * Convert any HTML excerpt to plain readable text.
	 *
	 * Order of operations:
	 *  1. Drop <img> tags up-front so no src URL leaks into the text.
	 *  2. Strip remaining HTML tags.
	 *  3. If nothing is left, fall back to the post's own excerpt or content.
	 *  4. Decode all HTML5 named entities (e.g. &mdash; → —, &nbsp; → space).
	 *  5. Collapse whitespace runs to a single space.
	 *
	 * @param string $raw
	 * @return string
	 */
	private function to_plain_text( $raw ) {
		$text = wp_strip_all_tags( preg_replace( '/<img[^>]*\/?>/i', '', $raw ) );

		if ( '' === trim( $text ) ) {
			$post = get_post();
			if ( $post instanceof WP_Post ) {
				$text = ! empty( $post->post_excerpt )
					? wp_strip_all_tags( $post->post_excerpt )
					: wp_strip_all_tags(
						wp_trim_words( strip_shortcodes( $post->post_content ), 55, '...' )
					);
			}
		}

		// Decode all HTML entities including named HTML5 ones (&mdash;, &hellip;, etc.).
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		// Collapse tabs, newlines, and multiple spaces down to a single space.
		return trim( preg_replace( '/[\s]+/', ' ', $text ) );
	}

	// -------------------------------------------------------------------------
	// Fix 3 — Suppress comment tags in feed items
	// -------------------------------------------------------------------------

	/**
	 * The standard RSS2 template outputs <slash:comments> and <wfw:commentRss>
	 * only when comments_open() OR get_comments_number() is truthy.
	 * Returning false/0 for both ensures those tags are never printed.
	 *
	 * @param bool $open
	 * @return bool
	 */
	public function hide_comments_in_feed( $open ) {
		return $this->is_yahoo_feed() ? false : $open;
	}

	/**
	 * @param int $count
	 * @return int
	 */
	public function hide_comment_count_in_feed( $count ) {
		return $this->is_yahoo_feed() ? 0 : $count;
	}

	// -------------------------------------------------------------------------
	// DOM utility
	// -------------------------------------------------------------------------

	/**
	 * Serialize the children of a DOM node to an HTML string.
	 * Used to extract only the inner content of the wrapper div.
	 *
	 * @param DOMNode $node
	 * @return string
	 */
	private function extract_inner_html( DOMNode $node ) {
		$outer = $node->ownerDocument->saveHTML( $node );

		// Find where the opening tag ends and where the closing tag begins,
		// then return only what sits between them — one saveHTML call instead
		// of one per child node.
		$content_start = strpos( $outer, '>' );
		$content_end   = strrpos( $outer, '</div>' );

		if ( false === $content_start || false === $content_end ) {
			return '';
		}

		return substr( $outer, $content_start + 1, $content_end - $content_start - 1 );
	}
}

add_action( 'init', array( 'Luis_Yahoo_Sports_Compliant', 'init' ) );
