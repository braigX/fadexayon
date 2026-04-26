<?php
namespace CrazyElements\Core\Files\CSS;

use CrazyElements\Plugin;

use CrazyElements\PrestaHelper; if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor post preview CSS file.
 *
 * Elementor CSS file handler class is responsible for generating the post
 * preview CSS file.
 *
 * @since 1.0.0
 */
class Post_Preview extends Post {

	/**
	 * Preview ID.
	 *
	 * Holds the ID of the current post being previewed.
	 *
	 * @var int
	 */
	private $preview_id;
	private $post_type;

	/**
	 * Post preview CSS file constructor.
	 *
	 * Initializing the CSS file of the post preview. Set the post ID and the
	 * parent ID and initiate the stylesheet.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id Post ID.
	 */
	public function __construct( $post_id, $type ) {


		$this->preview_id = $post_id;
		$this->post_type = $type;
		parent::__construct( $post_id, $type );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function get_preview_id() {
		return $this->preview_id;
	}

	public function get_post_type(){
		return $this->post_type;
	}

	/**
	 * Get data.
	 *
	 * Retrieve raw post data from the database.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Post data.
	 */
	protected function get_data() {
		$document = Plugin::$instance->documents->get( $this->preview_id );
		return $document ? $document->get_elements_data(null, $this->preview_id) : [];
	}

	/**
	 * Get file handle ID.
	 *
	 * Retrieve the handle ID for the previewed post CSS file.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return string CSS file handle ID.
	 */
	protected function get_file_handle_id() {
		return 'elementor-template-' . $this->preview_id;
	}

	/**
	 * Get meta data.
	 *
	 * Retrieve the previewed post CSS file meta data.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $property Optional. Custom meta data property. Default is
	 *                         null.
	 *
	 * @return array Previewed post CSS file meta data.
	 */
	public function get_meta( $property = null ) {

		$css = $this->get_content();

		$meta = [
			'status' => self::CSS_STATUS_INLINE,
			'fonts' => $this->get_fonts(),
			'css' => $css,
		];

		if ( $property ) {
			return isset( $meta[ $property ] ) ? $meta[ $property ] : null;
		}

		return $meta;
	}
}
