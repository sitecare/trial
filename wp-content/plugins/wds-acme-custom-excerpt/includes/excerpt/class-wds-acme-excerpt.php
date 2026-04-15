<?php
/**
 * Acme Custom Excerpt
 *
 * Handles Custom Excerpt Related Methods and Data
 *
 * @package WDS Acme Custom Excerpt
 * @author Mike Grotton
 */


/**
 * Acme Custom Excerpt Class
 * 
 */

class WDS_Acme_Excerpt{

	/**
	 * Constructor for class
	 * @param $plugin main plugin object
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
		require_once( $this->plugin->path . 'includes/excerpt/template-tags.php' );
	}

	/**
	 * Initialize the hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'add_meta_boxes_post', array( $this, 'add_excerpt_meta_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_excerpt_box' ), 10, 2 );
	}

	/**
	 * Create input box for excerpt
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function add_excerpt_meta_box( $post ) {
		add_meta_box( 
			'custom-excerpt',
			__( 'Custom Excerpt', 'acme' ),
			array( $this, 'render_excerpt_box' ),
			'post',
			'normal',
			'default'
		);	
	}

	/**
	 * Add WYSIWYG input box for custom excerpt
	 * @param  $post object
	 * @return html
	 */
	public function render_excerpt_box( $post = false ) {

		wp_nonce_field( basename(__FILE__), "wds-acme-custom-excerpt-nonce" );
	
		if ( isset( $post->ID ) ) {
			$excerpt = get_post_meta( $post->ID, 'wds-acme-custom-excerpt', true ) ;
		} else {
			$excerpt = '';
		}
		
		wp_editor( htmlspecialchars_decode( $excerpt ), 'wds-acme-custom-excerpt', $settings = array( 'textarea_name' => 'wds-acme-custom-excerpt-input' ) );
	}

	/**
	 * Save excerpt
	 * @param  $post_id ID of post being edited
	 * @return void
	 */
	function save_excerpt_box( $post_id = false ) {    
		if ( $post_id ) {               
			if ( isset( $_POST['wds-acme-custom-excerpt-input'] ) )  {
				$excerpt =  htmlspecialchars( $_POST['wds-acme-custom-excerpt-input'] );
				update_post_meta( $post_id, 'wds-acme-custom-excerpt', $excerpt );
			}
		}
	}
}