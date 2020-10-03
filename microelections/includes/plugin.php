<?php

declare( strict_types=1 );

namespace MicroElections;

final class Plugin {
	private static $instance;
	private $response = '';
	public const NAME = 'microelections';

	private function __construct() {
	}

	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init(): void {
		load_plugin_textdomain( self::NAME, false, self::NAME . '/languages/' );
		add_filter( 'page_template', [ $this, 'page_template' ] );
		add_shortcode( self::NAME, [ $this, 'shortcode' ] );
	}

	public function shortcode(): string {
		return $this->response;
	}

	public function page_template(): void {
		global $post;
		if ( $post->post_name !== 'microelections' ) {
			return;
		}
		$controller = new Controller( get_permalink( $post ) );
		$action = $_GET['action'] ?? 'create';
		if ( ! method_exists( $controller, $action . 'Action' ) ) {
			$action = 'invalid';
		}
		$this->response = $controller->{$action . 'Action'}();
	}

}
