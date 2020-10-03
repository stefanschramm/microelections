<?php

declare( strict_types=1 );

namespace MicroElections;

use MicroElections\Entity\Ballot;
use MicroElections\Entity\Election;
use MicroElections\Entity\Entity;

class Database {
	public static function persist_entity( Entity $entity ) {
		self::insert( Setup::table( $entity::table_name() ), $entity->to_db() );
	}

	public static function get_election( string $hash ): ?Election {
		$query = self::prepare(
			'SELECT * FROM ' . Setup::table( 'election' ) . ' WHERE hash = %s;',
			[ $hash ]
		);

		$result = self::get_results( $query );

		if ( count( $result ) === 0 ) {
			return null;
		}

		/** @var Election $election */
		$election = Election::from_db( $result[0] );

		return $election;
	}

	/**
	 * @return Ballot[]
	 */
	public static function get_ballots( Election $election ): array {
		$query = self::prepare(
			'SELECT * FROM ' . Setup::table( 'ballot' ) . ' WHERE id_election = %s;',
			[ $election->get_id() ]
		);

		$result = self::get_results( $query );

		return array_map( [ Ballot::class, 'from_db' ], $result );
	}

	public static function cleanup(): void {
		$maxAge = 60 * 60; // 1 hour

		// Remove old elections
		$query = self::prepare( 'DELETE FROM ' . Setup::table( 'election' ) . ' WHERE TIMESTAMPDIFF(SECOND, created_at, NOW()) > %s', [ $maxAge ] );
		self::get_results( $query );

		// Remove unassociated ballots
		$query = self::prepare( 'DELETE FROM ' . Setup::table( 'ballot' ) . ' WHERE id_election NOT IN (SELECT id FROM ' . Setup::table( 'election' ) . ')', [ $maxAge ] );
		self::get_results( $query );
	}

	private static function insert( $table, $data ) {
		global $wpdb;

		$wpdb->insert( $table, $data );
	}

	private static function prepare( $query, $args ) {
		global $wpdb;

		return $wpdb->prepare( $query, $args );
	}

	private static function get_results( $query ) {
		global $wpdb;

		return $wpdb->get_results( $query, ARRAY_A );
	}
}
