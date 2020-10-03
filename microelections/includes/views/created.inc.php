<?php

declare( strict_types=1 );

use MicroElections\Entity\Election;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/** @var Election $election */
/** @var string $participate_link */
/** @var string $results_link */

?>

<h2><?php printf(__('Election "%s" created', 'microelections'), esc_html($election->get_title())); ?></h2>

<div><?php _e('Participation link:', 'microelections'); ?>
    <pre><?php echo esc_html( $participate_link ); ?></pre>
</div>

<div><?php _e('Results link:', 'microelections'); ?>
    <pre><?php echo esc_html( $results_link ); ?></pre>
</div>
