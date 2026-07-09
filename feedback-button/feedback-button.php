<?php
/**
 * Plugin Name: Feedback Button
 * Description: Adds a feedback button to the front end for users to submit feedback.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Feedback_Button {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_footer', [ $this, 'render_button' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
	}

	public function enqueue_assets() {
		wp_enqueue_style(
			'feedback-button',
			plugin_dir_url( __FILE__ ) . 'assets/feedback-button.css',
			[],
			'1.0.0'
		);
		wp_enqueue_script(
			'feedback-button',
			plugin_dir_url( __FILE__ ) . 'assets/feedback-button.js',
			[],
			'1.0.0',
			true
		);
		wp_localize_script( 'feedback-button', 'feedbackButton', [
			'restUrl' => rest_url( 'feedback-button/v1/submit' ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
		] );
	}

	public function render_button() {
		echo $this->get_button_html();
	}

	public function get_button_html() {
		return '<div id="feedback-button-wrap">
			<button id="feedback-toggle" aria-expanded="false" aria-controls="feedback-form-wrap">Feedback</button>
			<div id="feedback-form-wrap" hidden>
				<form id="feedback-form" novalidate>
					<label for="feedback-message">Your feedback</label>
					<textarea id="feedback-message" name="message" required maxlength="1000"></textarea>
					<button type="submit">Submit</button>
					<p id="feedback-status" role="status" aria-live="polite"></p>
				</form>
			</div>
		</div>';
	}

	public function register_rest_route() {
		register_rest_route( 'feedback-button/v1', '/submit', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'handle_submission' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'message' => [
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_textarea_field',
					'validate_callback' => [ $this, 'validate_message' ],
				],
			],
		] );
	}

	public function validate_message( $value ) {
		$value = trim( $value );
		if ( $value === '' ) {
			return new WP_Error( 'empty_message', 'Message cannot be empty.', [ 'status' => 400 ] );
		}
		if ( mb_strlen( $value ) > 1000 ) {
			return new WP_Error( 'message_too_long', 'Message must be 1000 characters or fewer.', [ 'status' => 400 ] );
		}
		return true;
	}

	public function handle_submission( WP_REST_Request $request ) {
		$message = $request->get_param( 'message' );

		do_action( 'feedback_button_submitted', $message );

		return rest_ensure_response( [ 'success' => true, 'message' => 'Thank you for your feedback!' ] );
	}
}

if ( ! defined( 'FEEDBACK_BUTTON_TESTING' ) ) {
	new Feedback_Button();
}
