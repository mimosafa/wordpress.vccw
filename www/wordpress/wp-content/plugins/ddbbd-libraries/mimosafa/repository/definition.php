<?php
namespace mimosafa\WP\Repository;

class Definition {

	private static $repositories = [];

	private $prefix = '';

	private $post_types_defaults;
	private $taxonomies_defaults;

	private static $instances = [];
	private static $maybe_prefixes = [];

	private static $cache = [];

	public static function instance( $prefix = null, $defaults = null ) {
		if ( did_action( 'init' ) )
			throw new \Exception( 'Too Late' );
		if ( ! isset( $prefix ) || $prefix === '' )
			return self::getInstance( '', $defaults );
		if ( ! is_string( $prefix ) || $prefix !== sanitize_key( $prefix ) || strlen( $prefix ) > 16 )
			throw new \Exception( 'Invalid' );
		$maybe_prefix = str_replace( [ '-', '_' ], ' ', $prefix );
		if ( in_array( $maybe_prefix, self::$maybe_prefixes, true ) )
			throw new \Exception( 'Similar Prefix Exists' );
		self::$maybe_prefixes[] = $maybe_prefix;
		return self::getInstance( (string) $prefix, $defaults );
	}

	public function post_type( $name, $args ) {
		if ( $args && is_array( $args ) )
			array_walk( $args, __NAMESPACE__ . '\\PostType\\Regulation::arguments_walker' );
		#var_dump( $args );
	}

	public function taxonomy( $name, $args ) {}

	private static function getInstance( $prefix, $defaults ) {
		if ( ! isset( self::$instances[$prefix] ) )
			self::$instances[$prefix] = new self( $prefix, $defaults );
		return self::$instances[$prefix];
	}

	private function __construct( $prefix, $defaults ) {
		static $added_action = false;
		self::$cache[$prefix] = [];
		if ( $prefix )
			$this->prefix = $prefix;
		if ( $defaults )
			$this->set_defaults( $defaults );
		if ( ! $added_action ) {
			add_action( 'init', [ &$this, 'register' ], 1 );
			$added_action = true;
		}
	}

	private function set_defaults( $defaults ) {
		//
	}

	private function &getCache() {
		static $falseVal = false;
		return self::$cache[$this->prefix] ?: $falseVal;
	}

	public function register() {
		if ( ! doing_action( 'init' ) || ! self::$repositories )
			return;
		foreach ( self::$repositories as $name => $args ) {
			if ( isset( $args['post_type'] ) )
				$this->register_post_type( $name, $args );
			else if ( isset( $args['taxonomy'] ) )
				$this->register_taxonomy( $name, $args );
		}
	}

	private function register_post_type( $name, Array $args ) {
		//
	}

	private function register_taxonomy( $name, Array $args ) {
		//
	}

}
