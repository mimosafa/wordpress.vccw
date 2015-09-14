<?php
namespace DDBBD\WP\PostType;

class Builder {

	/**
	 * @var array { @type DDBBD\WP\PostType\Builder }
	 */
	private static $instances = [];

	/**
	 * @var array
	 */
	private static $maybe_prefixes = [];

	/**
	 * @var string
	 */
	private $prefix;

	/**
	 * @var array
	 */
	private $post_types = [];

	/**
	 *
	 */
	private static $cache = [];

	/**
	 * @access public
	 *
	 * @param  string $prefix Optional.
	 * @return DDBBD\WP\PostType\Builder
	 */
	public static function getInstance( $prefix = '' ) {
		if ( ! is_string( $prefix ) )
			return;
		if ( ! $prefix )
			return isset( self::$instances[''] ) ? self::$instances[''] : self::$instances[''] = new self( '' );
		if ( ! isset( self::$instances[$prefix] ) ) {
			if ( $prefix !== sanitize_key( $prefix ) || strlen( $prefix ) > 16 )
				return;
			$maybe_prefix = str_replace( [ '-', '_' ], ' ', $prefix );
			if ( in_array( $maybe_prefix, self::$maybe_prefixes, true ) )
				return;
			self::$maybe_prefixes[] = $maybe_prefix;
			self::$instances[$prefix] = new self( $prefix );
		}
		return self::$instances[$prefix];
	}

	/**
	 * Constructor
	 *
	 * @access private
	 *
	 * @param  string $prefix
	 */
	private function __construct( $prefix ) {
		$this->prefix = $prefix;
		self::$cache[$prefix] = [];
	}

	/**
	 * @access private
	 */
	private function &getCache() {
		static $falseVal = false;
		return self::$cache[$this->prefix] ?: $falseVal;
	}

	public function init( $post_type ) {
		if ( $cache =& $this->getCache() )
			$this->cleanCache();
		if ( isset( $this->post_types[$post_type] ) )
			return;
		if ( $post_type !== sanitize_key( $post_type ) || strlen( $this->prefix . $post_type ) > 20 )
			return;
		$cache['post_type'] = $post_type;
	}

	private function cleanCache() {
		$cache =& $this->getCache();
		$post_type = $cache['post_type'];
		unset( $cache['post_type'] );
		$this->post_type[$post_type] = [ 'post_type' => $this->prefix . $post_type, 'args' => $cache ];
		$cache = [];
	}

}
