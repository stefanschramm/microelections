<?php

declare( strict_types=1 );

namespace MicroElections;

class Setup {
	public static function activate(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = [];

		$sql[] = "
			CREATE TABLE " . self::table( 'election' ) . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				hash VARCHAR(32) NOT NULL,
				results_hash VARCHAR(32) NOT NULL,
				settings TEXT NOT NULL,
				created_at datetime DEFAULT NOW() NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate
		";

		$sql[] = "
			CREATE TABLE " . self::table( 'ballot' ) . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				id_election mediumint(9) NOT NULL,
				content TEXT NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate
		";

		// (Not using foreign keys here because of problems with dbDelta)

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public static function deactivate(): void {

	}

	public static function uninstall(): void {
		global $wpdb;

		$wpdb->query( 'DROP TABLE ' . self::table( 'election' ) );
		$wpdb->query( 'DROP TABLE ' . self::table( 'ballot' ) );
	}

	public static function table( string $table_name ): string {
		global $wpdb;

		return $wpdb->prefix . Plugin::NAME . '_' . $table_name;
	}
}
