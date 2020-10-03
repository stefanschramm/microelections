<?php

declare( strict_types=1 );

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<h2><?php _e( 'Create new election', 'microelections' ); ?></h2>

<form action="" method="post">
    <label>
		<?php _e( 'Title:', 'microelections' ); ?>
        <input type="text" name="title"/>
    </label>
    <label>
		<?php _e( 'Candidates (one entry per line):', 'microelections' ); ?>
        <textarea name="options"></textarea>
    </label>
    <label>
		<?php _e( 'Maximum number of votes per ballot:', 'microelections' ); ?>
        <input type="text" name="max_votes" value="1"/>
    </label>
    <div>
        <label><input type="checkbox" name="abstentions"
                      value="1"/> <?php _e( 'allow abstentions', 'microelections' ); ?></label>
    </div>
    <div>
        <input type="submit" value="<?php _e( 'Create election', 'microelections' ); ?>"/>
    </div>
</form>
