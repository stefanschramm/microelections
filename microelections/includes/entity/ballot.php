<?php

declare( strict_types=1 );

namespace MicroElections\Entity;

use MicroElections\Exception\ValidationException;

class Ballot implements Entity {

	private $id_election;
	private $abstention;
	private $votes;
	private $identifier;

	private function __construct( int $id_election, bool $abstention, array $votes, string $identifier ) {
		$this->id_election = $id_election;
		$this->abstention = $abstention;
		$this->votes = $votes;
		$this->identifier = $identifier;
	}

	/**
	 * @throws ValidationException
	 */
	public static function from_form( Election $election, array $post_data ): self {

		$abstention = false;
		if ( $election->allows_abstentions() ) {
			if ( $election->is_single_choice() ) {
				$abstention = $post_data['option'] === '-1';
			} else {
				$abstention = $post_data['abstention'] === '1';
			}
		}

		if ( $election->is_single_choice() ) {
			$votes = $abstention ? [] : [ $post_data['option'] ];
		} else {
			$votes = is_array( $post_data['option'] ) ? array_keys( $post_data['option'] ) : [];
		}

		if ( count( $votes ) !== 0 && $abstention ) {
			throw new ValidationException( __( 'It\'s not possible to abstain and vote at the same time.', 'microelections' ) );
		}

		if ( count( $votes ) > $election->get_max_votes() ) {
			throw new ValidationException( __( 'Invalid number of votes', 'microelections' ) );
		}

		$options = $election->get_options();

		foreach ( $votes as $option_idx ) {
			if ( ! isset( $options[ $option_idx ] ) ) {
				throw new \RuntimeException( 'Invalid option index.' );
			}
		}

		// TODO: validate votes

		return new self(
			$election->get_id(),
			$abstention,
			$votes,
			self::generate_random_identifier()
		);
	}

	public static function table_name(): string {
		return 'ballot';
	}

	public function to_db(): array {
		return [
			'id_election' => $this->id_election,
			'content'     => json_encode( [
				'abstention' => $this->abstention,
				'votes'      => $this->votes,
				'identifier' => $this->identifier,
			] ),
		];
	}

	public static function from_db( array $row ): Entity {
		$content = json_decode( $row['content'], true );

		return new self(
			(int) $row['id_election'],
			$content['abstention'],
			$content['votes'],
			$content['identifier']
		);
	}

	public function get_id_election(): int {
		return $this->id_election;
	}

	public function is_abstention(): bool {
		return $this->abstention;
	}

	public function voted_vor( int $option_idx ): bool {
		return in_array( $option_idx, $this->votes );
	}

	public function get_identifier(): string {
		return $this->identifier;
	}

	private static function generate_random_identifier(): string {
		return substr( md5( microtime() . '-xyz-' . rand() ), 0, 5 );
	}
}