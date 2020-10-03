<?php

declare( strict_types=1 );

if ( ! defined( 'WPINC' ) ) {
	die;
}

/** @var \MicroElections\Entity\Election $election */
/** @var string $ballot_identifier */
/** @var string $result_link */

?>

<h2><?php _e('Ballot casted!', 'microelections'); ?></h2>
<p><?php printf( __( 'Your ballot for the election "%s" has been casted.', 'microelections' ), esc_html( $election->get_title() ) ); ?></p>
<p><?php _e('Random ballot identification code:', 'microelections'); ?> <span><?php echo esc_html( $ballot_identifier ); ?></span></p>
