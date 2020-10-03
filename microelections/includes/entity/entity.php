<?php

declare( strict_types=1 );

namespace MicroElections\Entity;

interface Entity {
	public static function table_name(): string;

	public function to_db(): array;

	public static function from_db( array $row ): Entity;
}
