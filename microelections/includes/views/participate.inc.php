<?php

declare( strict_types=1 );

use MicroElections\Entity\Election;

if ( ! defined( 'WPINC' ) ) {
	die;
}
/** @var Election $election */
/** @var string|null $error */

?>

<h2><?php printf( __( 'Election "%s"', 'microelections' ), esc_html( $election->get_title() ) ); ?></h2>
<form method="post">
	<?php if ( $error !== null ): ?>
        <p><strong><?php _e('Error:', 'microelections'); ?></strong> <?php echo $error; ?></p>
	<?php endif ?>

	<?php if ( ! $election->is_single_choice() ): ?>
        <p><?php printf( __( 'You can vote for up to %s candidates/options.', 'microelections' ), $election->get_max_votes() ); ?></p>
	<?php endif ?>
	<?php foreach ( $election->get_options() as $idx => $option ): ?>
        <div>
            <label>
				<?php if ( $election->is_single_choice() ): ?>
                    <input type="radio" name="option" value="<?php echo $idx; ?>">
				<?php else: ?>
                    <input type="checkbox" name="option[<?php echo $idx; ?>]" value="1">
				<?php endif; ?>
				<?php echo esc_html( $option ); ?>
            </label>
        </div>
	<?php endforeach; ?>
	<?php if ( $election->allows_abstentions() ): ?>
        <label>
			<?php if ( $election->is_single_choice() ): ?>
                <input type="radio" name="option" value="-1">
			<?php else: ?>
                <input type="checkbox" name="abstention" value="1">
			<?php endif; ?>
			<?php _e( 'Abstention', 'microelections' ); ?>
        </label>
	<?php endif; ?>
    <div>
        <input type="submit" value="<?php _e( 'Cast ballot', 'microelections' ); ?>"/>
    </div>
</form>
