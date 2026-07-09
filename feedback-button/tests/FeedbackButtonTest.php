<?php

use PHPUnit\Framework\TestCase;

class FeedbackButtonTest extends TestCase {

	private Feedback_Button $plugin;

	protected function setUp(): void {
		$this->plugin = new Feedback_Button();
	}

	public function test_get_button_html_contains_toggle_button(): void {
		$html = $this->plugin->get_button_html();
		$this->assertStringContainsString( 'id="feedback-toggle"', $html );
		$this->assertStringContainsString( 'Feedback', $html );
	}

	public function test_get_button_html_contains_form(): void {
		$html = $this->plugin->get_button_html();
		$this->assertStringContainsString( 'id="feedback-form"', $html );
		$this->assertStringContainsString( '<textarea', $html );
	}

	public function test_get_button_html_has_aria_attributes(): void {
		$html = $this->plugin->get_button_html();
		$this->assertStringContainsString( 'aria-expanded="false"', $html );
		$this->assertStringContainsString( 'aria-controls="feedback-form-wrap"', $html );
	}

	public function test_validate_message_rejects_empty_string(): void {
		$result = $this->plugin->validate_message( '' );
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'empty_message', $result->code );
	}

	public function test_validate_message_rejects_whitespace_only(): void {
		$result = $this->plugin->validate_message( '   ' );
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'empty_message', $result->code );
	}

	public function test_validate_message_rejects_over_1000_chars(): void {
		$result = $this->plugin->validate_message( str_repeat( 'a', 1001 ) );
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'message_too_long', $result->code );
	}

	public function test_validate_message_accepts_valid_input(): void {
		$this->assertTrue( $this->plugin->validate_message( 'This is valid feedback.' ) );
	}

	public function test_validate_message_accepts_exactly_1000_chars(): void {
		$this->assertTrue( $this->plugin->validate_message( str_repeat( 'a', 1000 ) ) );
	}

	public function test_handle_submission_returns_success(): void {
		$request = new WP_REST_Request();
		$request->set_param( 'message', 'Great site!' );
		$response = $this->plugin->handle_submission( $request );
		$this->assertTrue( $response['success'] );
		$this->assertStringContainsString( 'Thank you', $response['message'] );
	}
}
