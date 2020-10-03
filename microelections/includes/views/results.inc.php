<?php

declare( strict_types=1 );

use MicroElections\Entity\Ballot;
use MicroElections\Entity\Election;

if ( ! defined( 'WPINC' ) ) {
	die;
}
/** @var Election $election */
/** @var Ballot[] $ballots */
/** @var int[] $sums */
/** @var int $ballot_count */
/** @var int $abstention_count */

?>

<h2><?php printf( __( 'Election "%s"', 'microelections' ), esc_html( $election->get_title() ) ); ?></h2>

<?php if ( $election->allows_abstentions() ): ?>
    <p><?php printf( __( 'Casted ballots: %s.', 'microelections' ), $ballot_count ); ?></p>
<?php else: ?>
    <p><?php printf( __( 'Casted ballots: %s, including %s abstentions.', 'microelections' ), $ballot_count, $abstention_count ); ?></p>
<?php endif ?>

<table>
    <tr>
        <th rowspan="2"><?php _e( 'Candidate/Option', 'microelections' ) ?></th>
        <th rowspan="2"><?php _e( 'Votes', 'microelections' ) ?></th>
        <th colspan="<?php echo $ballot_count ?>"><?php _e('Ballot', 'microelections'); ?></th>
    </tr>
    <tr>
		<?php foreach ( $ballots as $ballot ): ?>
            <th><?php echo esc_html( $ballot->get_identifier() ); ?></th>
		<?php endforeach; ?>
    </tr>
	<?php foreach ( $election->get_options() as $option_idx => $option ): ?>
        <tr>
            <td><?php echo esc_html( $option ); ?></td>
            <td><?php echo esc_html( $sums[ $option_idx ] ); ?></td>
			<?php foreach ( $ballots as $ballot ): ?>
				<?php if ( $ballot->is_abstention() ): ?>
					<?php if ( $option_idx === 0 ): ?>
                        <td rowspan="<?php echo count( $election->get_options() ); ?>">
							<?php echo substr( __( 'Abstention', 'microelections' ), 0, 1 ); ?>
                        </td>
					<?php endif; ?>
					<?php continue; ?>
				<?php endif ?>
                <td>
					<?php if ( $ballot->voted_vor( $option_idx ) ): ?>
                        X
					<?php endif; ?>
                </td>
			<?php endforeach; ?>
        </tr>
	<?php endforeach; ?>
</table>
<?php if ( $election->allows_abstentions() ): ?>
    <p><?php echo substr( __( 'Abstention', 'microelections' ), 0, 1 ); ?>
        : <?php _e( 'Abstention', 'microelections' ) ?></p>
<?php endif; ?>
