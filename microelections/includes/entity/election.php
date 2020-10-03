<?php

declare( strict_types=1 );

namespace MicroElections\Entity;

class Election implements Entity {

	public static $table = 'election';

	private $id;
	private $hash;
	private $results_hash;
	private $title;
	private $abstentions;
	private $max_votes;
	private $options;

	public static function table_name(): string {
		return 'election';
	}

	private function __construct( ?int $id, string $hash, string $results_hash, string $title, array $options, bool $abstentions, int $max_votes ) {
		$this->id = $id;
		$this->hash = $hash;
		$this->results_hash = $results_hash;
		$this->title = $title;
		$this->abstentions = $abstentions;
		$this->max_votes = $max_votes;
		$this->options = $options;
	}

	public static function from_form( array $form ): self {
		return new self(
			null,
			md5( microtime() . '-' . rand() ),
			md5( microtime() . '-' . rand() ),
			$form['title'],
			array_map( 'trim', explode( "\n", str_replace( "\r", "", trim( $form['options'] ) ) ) ),
			(bool) $form['abstentions'],
			(int) $form['max_votes']
		);
	}

	public static function from_db( array $row ): Entity {
		$settings = json_decode( $row['settings'], true );

		return new self(
			(int) $row['id'],
			$row['hash'],
			$row['results_hash'],
			$settings['title'],
			$settings['options'],
			$settings['abstentions'],
			$settings['max_votes']
		);
	}

	public function to_db(): array {
		return [
			'hash'     => $this->hash,
			'results_hash'     => $this->results_hash,
			'settings' => json_encode( [
				'title'       => $this->title,
				'options'     => $this->options,
				'abstentions' => $this->abstentions,
				'max_votes'   => $this->max_votes,
			] )
		];
	}

	public function get_id(): int {
		return $this->id;
	}

	public function get_hash(): string {
		return $this->hash;
	}

	public function get_results_hash(): string {
		return $this->results_hash;
	}

	public function get_title(): string {
		return $this->title;
	}

	public function allows_abstentions(): bool {
		return $this->abstentions;
	}

	public function get_max_votes(): int {
		return $this->max_votes;
	}

	public function is_single_choice(): bool {
		return $this->max_votes === 1;
	}

	public function get_options(): array {
		return $this->options;
	}
}
