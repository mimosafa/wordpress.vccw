<?php
namespace DDBBD\WP\Types;

/**
 * WordPress Custom Content Types Registration Interface
 *
 * @access private
 *
 * @package    WordPress
 * @subpackage DDBBD
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Register {

	private static $types = [];

	/**
	 * Arguments for registration
	 *
	 * @var array
	 */
	private $post_types = [];
	private $taxonomies = [];
	private $endpoints  = [];

	/**
	 * Singleton
	 */
	private static function getInstance() {
		static $instance;
		return $instance ?: $instance = new self();
	}
	public function __clone() {}
	public function __wakeup() {}

	/**
	 * Constructor - Add actions/filters
	 *
	 * @access private
	 */
	private function __construct() {
		add_action( 'init', [ &$this, 'register_taxonomies' ], 1 );
		add_action( 'init', [ &$this, 'register_post_types' ], 1 );
		add_action( 'init', [ &$this, 'add_rewrite_endpoints' ], 1 );
		add_filter( 'query_vars', [ &$this, 'add_query_vars'] );
	}

	/**
	 * Add Post Type
	 *
	 * @access public
	 *
	 * @param  string $name
	 * @param  array  $args    Optional
	 * @param  array  $options Optional
	 * @return void
	 */
	public static function post_type( $name, $args = [] ) {
		$self = self::getInstance();
		$self->post_types[] = [ 'post_type' => $name, 'args' => $args ];
	}

	private static function isValidName() {}

	/**
	 * Add Taxonomy
	 *
	 * @access public
	 *
	 * @param  string        $name
	 * @param  string|array  $object_type Optional
	 * @param  array         $args        Optional
	 * @param  array         $options     Optional
	 * @return void
	 */
	public static function taxonomy( $name, $object_type, $args = [] ) {
		$self = self::getInstance();
		$self->taxonomies[] = [ 'taxonomy' => $name, 'object_type' => $object_type, 'args' => $args ];
	}

	/**
	 * Add Endpoint
	 *
	 * @access public
	 *
	 * @param  string $name
	 * @param  array  $args    Optional
	 * @param  array  $options Optional
	 * @return void
	 */
	public static function endpoint( $name, $args = [] ) {
		$self = self::getInstance();
		$self->endpoints[] = [ 'endpoint' => $name, 'args' => $args ];
	}

	/**
	 * @access private
	 */
	public function register_post_types() {
		if ( ! $this->post_types )
			return;
		static $thumbnail_supported;
		$thumbnail_supported = current_theme_supports( 'post-thumbnails' );
		foreach ( $this->post_types as $array ) {
			/**
			 * @var string $post_type
			 * @var array  $args
			 */
			extract( $array );
			if ( ! $post_type = filter_var( $post_type ) )
				continue;
			$args = is_array( $args ) ? $args : [];
			if ( ! isset( $args['label'] ) || ! filter_var( $args['label'] ) ) {
				if ( ! isset( $args['labels'] ) || ! isset( $args['labels']['name'] ) || ! filter_var( $args['labels']['name'] ) ) {
					$args['label'] = self::labelize( $post_type );
				}
			}
			if ( $this->taxonomies ) {
				$taxonomies = isset( $args['taxonomies'] ) ? (array) $args['taxonomies'] : [];
				foreach ( $this->taxonomies as $tax ) {
					if ( in_array( $post_type, (array) $tax['object_type'], true ) )
						$taxonomies[] = $tax['taxonomy'];
				}
				if ( $taxonomies )
					$args = array_merge( $args, [ 'taxonomies' => $taxonomies ] );
			}
			if ( ! $thumbnail_supported && isset( $args['supports'] ) && in_array( 'thumbnail', (array) $args['supports'], true ) ) {
				add_theme_support( 'post-thumbnails' );
				$thumbnail_supported = true;
			}
			register_post_type( $post_type, $args );
		}
	}

	/**
	 * @access private
	 */
	public function register_taxonomies() {
		if ( ! $this->taxonomies )
			return;
		foreach ( $this->taxonomies as $array ) {
			/**
			 * @var string       $taxonomy
			 * @var string|array $object_type
			 * @var array        $args
			 */
			extract( $array );
			if ( ! $taxonomy = filter_var( $taxonomy ) )
				continue;
			$args = is_array( $args ) ? $args : [];
			if ( ! isset( $args['label'] ) || ! filter_var( $args['label'] ) ) {
				if ( ! isset( $args['labels'] ) || ! isset( $args['labels']['name'] ) || ! filter_var( $args['labels']['name'] ) ) {
					$args['label'] = self::labelize( $taxonomy );
				}
			}
			register_taxonomy( $taxonomy, $object_type, $args );
		}
	}

	/**
	 * @access private
	 */
	public function add_rewrite_endpoints() {
		if ( ! $this->endpoints )
			return;
		// ~
	}

	/**
	 * @access private
	 */
	public function add_query_vars( $vars ) {
		if ( $this->endpoints ) {
			// ~
		}
		return $vars;
	}

	private static function labelize( $name ) {
		return ucwords( str_replace( [ '-', '_' ], ' ', $name ) );
	}

}
