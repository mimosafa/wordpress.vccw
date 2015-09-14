<?php
namespace DDBBD\WP\Types;

/**
 * Include Registration Interface Class
 *
 */
require_once 'register.php';

/**
 * Interface Class for Post Types & Taxonomies Definition.
 *
 * @package WordPress
 * @subpackage Dana Don-Boom-Boom-Doo
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Builder {

	/**
	 * @var string
	 */
	private $prefix = '';

	/**
	 * @var array
	 */
	private $post_types = [];
	private $taxonomies = [];

	/**
	 *
	 */
	private $default_args_post_types = [ 'public' => true ];
	private $default_args_taxonomies = [ 'public' => true ];

	/**
	 *
	 */
	private static $cache = [];

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * @param  string $prefix Optional.
	 */
	public function __construct( $prefix = null ) {
		if ( did_action( 'after_setup_theme' ) )
			throw new \Exception( 'Too Late' );
		if ( isset( $prefix ) ) {
			if ( ! is_string( $prefix ) )
				throw new \Exception( 'Invalid Argument Type' );
			if ( $prefix !== sanitize_key( $prefix ) )
				throw new \Exception( 'Regexp Error' );
			if ( strlen( $prefix ) > 16 )
				throw new \Exception( 'String Length is too long' );
			$this->prefix = $prefix;
		}
		self::$cache[$prefix] = [];
		add_action( 'after_setup_theme', [ &$this, 'register_types' ], 1 );
	}

	/**
	 * @access private
	 */
	public function register_types() {
		if ( ! doing_action( 'after_setup_theme' ) )
			return;
		if ( $this->taxonomies ) {
			foreach ( $this->taxonomies as $taxonomy ) {
				if ( $this->default_args_taxonomies )
					$this->args_merge_taxonomy( $taxonomy['args'] );
				if ( isset( $taxonomy['object_type'] ) ) {
					//
				}
				#var_dump( $taxonomy );
				call_user_func_array( __NAMESPACE__ . '\\Register::taxonomy', $taxonomy );
			}
		}
		if ( $this->post_types ) {
			foreach ( $this->post_types as $post_type ) {
				if ( $this->default_args_post_types )
					$this->args_merge_post_type( $post_type['args'] );
				#var_dump( $post_type );
				call_user_func_array( __NAMESPACE__ . '\\Register::post_type', $post_type );
			}
		}
	}

	private function args_merge_post_type( Array &$args ) {
		foreach ( $this->default_args_post_types as $key => $val ) {
			if ( $key = 'public' )
				$args['public'] = $val;
		}
	}

	private function args_merge_taxonomy( Array &$args ) {
		foreach ( $this->default_args_taxonomies as $key => $val ) {
			if ( $key = 'public' )
				$args['public'] = $val;
		}
	}

	/**
	 *
	 */
	public function post_type( $name ) {
		if ( ! $name = filter_var( $name, \FILTER_CALLBACK, [ 'options' => [ &$this, 'validate_post_type_name' ] ] ) )
			return false;
		if ( $cache =& $this->getCache() ) {
			if ( ! isset( $cache['post_type'] ) || $cache['post_type'] !== $name )
				$this->cleanCache();
		}
		if ( ! $cache ) {
			if ( isset( $this->post_types[$name] ) ) {
				$cache = [
					'post_type' => $name,
					'args' => $this->post_types[$name]['args']
				];
				unset( $this->post_types[$name] );
			} else {
				$cache['post_type'] = $name;
			}
		}
		return $this;
	}

	private function validate_post_type_name( $name ) {
		if ( ! is_string( $name ) )
			return null;
		if ( $name !== sanitize_key( $name ) )
			return null;
		if ( strlen( $this->prefix . $name ) > 20 )
			return null;
		return $name;
	}

	public function taxonomy( $name ) {
		if ( ! $name = filter_var( $name, \FILTER_CALLBACK, [ 'options' => [ &$this, 'validate_taxonomy_name' ] ] ) )
			return false;
		if ( $cache =& $this->getCache() ) {
			if ( ! isset( $cache['taxonomy'] ) || $cache['taxonomy'] !== $name )
				$this->cleanCache();
		}
		if ( ! $cache ) {
			if ( isset( $this->taxonomies[$name] ) ) {
				$cache = [
					'taxonomy' => $name,
					'object_type' => $this->taxonomies[$name]['object_type'],
					'args' => $this->taxonomies[$name]['args']
				];
				unset( $this->taxonomies[$name] );
			} else {
				$cache['taxonomy'] = $name;
			}
		}
		return $this;
	}

	private function validate_taxonomy_name( $name ) {
		if ( ! is_string( $name ) )
			return null;
		if ( $name !== sanitize_key( $name ) )
			return null;
		if ( preg_match( '/\-/', $name ) )
			return null;
		if ( strlen( $this->prefix . $name ) > 32 )
			return null;
		return $name;
	}

	/**
	 * @access private
	 *
	 * @param  string $type post_type|taxonomy
	 * @return &array|boolean
	 */
	private function &getCache() {
		static $falseVal = false;
		return self::$cache[$this->prefix] ?: $falseVal;
	}

	/**
	 * @access private
	 *
	 * @param  string $type post_type|taxonomy
	 */
	private function cleanCache() {
		$cache =& $this->getCache();
		$args = [];
		if ( isset( $cache['post_type'] ) ) {
			$name = $cache['post_type'];
			unset( $cache['post_type'] );
			$args['post_type'] = $this->prefix . $name;
			$store =& $this->post_types;
		} else if ( isset( $cache['taxonomy'] ) ) {
			$name = $cache['taxonomy'];
			unset( $cache['taxonomy'] );
			$args['taxonomy'] = $this->prefix . $name;
			if ( isset( $cache['object_type'] ) ) {
				$args['object_type'] = $cache['object_type'];
				unset( $cache['object_type'] );
			} else {
				$args['object_type'] = null;
			}
			$store =& $this->taxonomies;
		}
		$args['args'] = $cache;
		$store[$name] = $args;
		$cache = [];
	}

}
