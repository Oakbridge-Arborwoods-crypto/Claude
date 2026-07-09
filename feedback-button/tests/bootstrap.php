<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

define( 'FEEDBACK_BUTTON_TESTING', true );

// Minimal WordPress stubs so the plugin class loads without a full WP install.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

if ( ! function_exists( 'add_action' ) )       { function add_action() {} }
if ( ! function_exists( 'plugin_dir_url' ) )   { function plugin_dir_url() { return ''; } }
if ( ! function_exists( 'rest_url' ) )         { function rest_url( $path ) { return 'http://example.com/wp-json/' . $path; } }
if ( ! function_exists( 'wp_create_nonce' ) )  { function wp_create_nonce() { return 'test-nonce'; } }
if ( ! function_exists( 'register_rest_route' )) { function register_rest_route() {} }
if ( ! function_exists( 'do_action' ) )        { function do_action() {} }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $v ) { return strip_tags( $v ); } }
if ( ! function_exists( 'rest_ensure_response' ) ) {
	function rest_ensure_response( $data ) { return $data; }
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public string $code;
		public string $message;
		public array $data;
		public function __construct( string $code, string $message, array $data = [] ) {
			$this->code = $code;
			$this->message = $message;
			$this->data = $data;
		}
	}
}

if ( ! class_exists( 'WP_REST_Request' ) ) {
	class WP_REST_Request {
		private array $params = [];
		public function set_param( string $key, mixed $value ): void { $this->params[$key] = $value; }
		public function get_param( string $key ): mixed { return $this->params[$key] ?? null; }
	}
}

// Load the plugin (instantiation is suppressed — tests create their own instances).
require_once dirname(__DIR__) . '/feedback-button.php';
