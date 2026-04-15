<?php
/**
 * Updates for the syndication feed.
 */

/**
 * Class for adding information to the RSS feed.
 */
class WDS_Acme_RSS_Feed_Syndication_Updates {
	/**
	 * Holds a single instance of this class
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Returns a single instance of this class.
	 *
	 * @return WDS_RR_Redirect_Old_Race_Instances Instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	protected function __construct() {
		// Initialize hooks.
		$this->hooks();
	}

	/**
	 * Initialize any hooks.
	 *
	 * @return void
	 */
	protected function hooks() {
		// Add namespaces to the feed.
		add_action( 'rss2_ns', array( $this, 'add_namespaces' ), 10 );

		// Add images to the RSS feed.
		add_action( 'rss2_item', array( $this, 'add_images_to_feed' ), 10 );

		// Add dates to the RSS feed.
		// add_action( 'rss2_item', array( $this, 'add_dates_to_feed' ), 5 );
	}

	/**
	 * Add custom namespaces to the feed.
	 *
	 * @return  void
	 */
	public function add_namespaces() {
		?>
		xmlns:media="http://search.yahoo.com/mrss/"
		<?php
	}

	/**
	 * Parse images out of post_content and add them to the feed.
	 *
	 * @return void
	 */
	public function add_images_to_feed() {
		// Fetch images in the post.
		$images = $this->get_images( get_the_ID() );

		// Bail early if no images.
		if ( empty( $images ) ) {
			return;
		}

		// Loop through images and add to the feed.
		foreach ( (array) $images as $image ) :
			// Try to find the post_id for the image.
			$image_id = $this->get_attachment_id( $image );

			/**
			 * Getting the full sized image
			 */
			$image = wp_get_attachment_image_src( $image_id, 'full' );
			if ( isset( $image[0] ) && ! empty( $image[0] ) ) {
				$image = $image[0];
			} else {
				continue;
			}

			// Skip if no attachment ID is found.
			if ( empty( $image_id ) ) {
				continue;
			}

			// Syndication rights and licensor information.
			$has_syndication_rights = get_field( 'has_syndication_rights', $image_id );
			$licensor_id 			= get_field( 'licensor_id', $image_id );
			$licensor_name 			= get_field( 'licensor_name', $image_id );

			// Default back to having syndication rights if no licensor information is added.
			if ( empty( $licensor_id ) && empty( $licensor_name ) ) {
				$has_syndication_rights = true;
			}

			// Set the field value for syndication rights.
			$syndication_rights = $has_syndication_rights ? 1 : 0;
			?>
			<!--Use the Media Tag to add images-->
			<media:content url="<?php echo esc_url( $image ); ?>">
				<media:title><?php echo get_the_title( $image_id ); ?></media:title>

				<media:text><?php echo esc_html( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ); ?></media:text>
				
			</media:content>
			<?php
		endforeach;
	}

	/**
	 * Retrieves images from the post.
	 *
	 * @param  int $post_id The ID of the post to get images for.
	 * @return array        Array of images for the post.
	 */
	protected function get_images( $post_id ) {
		// Holds all images for the post.
		$images = array();

		// Fetch images from post_content.
		$content_images = $this->get_images_from_content( $post_id );

		// Get the featured images for the post.
		$featured_images = $this->get_featured_images_for_post( $post_id );

		// Combine all images for the post.
		$all_images = array_merge( $images, $content_images, $featured_images );

		// Returns only the unique images for the post.
		return array_unique( $all_images );
	}
	/**
	 * Gets the featued image for a post.
	 *
	 * @param int $post_id The ID of the post to retrieve images from post content for.
	 * @return array Images featured in the post.
	 */
	protected function get_featured_images_for_post( $post_id ) {
		// Holds the URLs for featured images in the post.
		$images = array();

		// Get the featured thumbnail.
		$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ) );

		if ( empty( $featured_image ) ) {
			return $images;
		}

		// Add featured image URL to the list.
		$images[] = isset( $featured_image[0] ) ? esc_url( $featured_image[0] ) : '';

		return $images;
	}

	/**
	 * Parses content and retrieves images.
	 *
	 * @param int $post_id The ID of the post to retrieve images from post content for.
	 * @return array Images in the post content.
	 */
	protected function get_images_from_content( $post_id ) {
		// Holds our response.
		$images = array();

		// Gets the post object.
		$post = get_post( $post_id, OBJECT, 'raw' );

		// Bail early if no post content.
		if ( empty( $post->post_content ) ) {
			return $images;
		}

		// Render shortcodes etc.
		$post_content = apply_filters( 'the_content', $post->post_content );

		// Bail early if no content.
		if ( empty( $post_content ) ) {
			return $images;
		}

		// Create the domDocument and search for any images.
		$doc = new DOMDocument;

		// Disable errors for html5 tags.
		libxml_use_internal_errors( true );

		// Load the content and find all the images.
		$doc->loadHTML( $post_content );
		$tags = $doc->getElementsByTagName( 'img' );

		// Replace src in html.
		foreach ( $tags as $tag ) {
			// Get the value for the src attribute.
			$src = $tag->getAttribute( 'src' );

			// Skip if src is blank.
			if ( empty( $src ) ) {
				continue;
			}

			$images[] = $src;
		}

		return $images;
	}

	/**
	 * Get an attachment ID given a URL.
	 *
	 * @param string $url The URL to use to find the attachment.
	 * @return int Attachment ID on success, 0 on failure
	 */
	protected function get_attachment_id( $url ) {
		// Default value.
		$attachment_id = 0;

		// Get the site's upload directory.
		$dir = wp_upload_dir();

		// Bail early if this is not in the uploads directory.
		if ( false === strpos( $url, $dir['baseurl'] . '/' ) ) {
			return $attachment_id;
		}

		// Get the file's basename.
		$file = basename( $url );

		// Set the query arguments.
		$query_args = array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
			'meta_query'  => array( // @codingStandardsIgnoreLine
				array(
					'value'   => $file,
					'compare' => 'LIKE',
					'key'     => '_wp_attachment_metadata',
				),
			)
		);

		// Run the query.
		$query = new WP_Query( $query_args );

		// Bail early if no posts.
		if ( ! $query->have_posts() ) {
			return $attachment_id;
		}

		// Loop through to find the correct post.
		foreach ( $query->posts as $post_id ) {
			// Get attachment meta data.
			$meta = wp_get_attachment_metadata( $post_id );

			// Get the original file.
			$original_file = basename( $meta['file'] );

			// Get cropped image sizes.
			$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );

			// Skip if not a match.
			if ( ! ( $original_file === $file ) && ! in_array( $file, $cropped_image_files, true ) ) {
				continue;
			}

			// Set the attachment ID.
			$attachment_id = $post_id;

			// We only need one.
			break;
		}

		return $attachment_id;
	}

	/**
	 * Add missing dates to the RSS feed.
	 *
	 * @return  void
	 */
	public function add_dates_to_feed() {
		$date_format = 'c';

		// Get the published date.
		$published_date = get_the_date( $date_format, get_the_ID() );

		// Get the modified date.
		$modified_date = get_the_modified_date( $date_format, get_the_ID() );
		?>
		<mi:dateTimeWritten><?php echo esc_attr( $published_date ); ?></mi:dateTimeWritten>
		<dc:modified><?php echo esc_attr( $modified_date ); ?></dc:modified>
		<?php
	}
}

// Initilize this class.
add_action( 'init', array( 'WDS_Acme_RSS_Feed_Syndication_Updates', 'get_instance' ) );
