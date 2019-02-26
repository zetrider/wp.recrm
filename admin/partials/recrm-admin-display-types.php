<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();
?>

<div class="wrap">
    <h2><?php echo get_admin_page_title() ?></h2>

    <form action="options.php" method="POST">
        <?php
        settings_fields( 'recrm_estate_types' );
        do_settings_sections( 'recrm_estate_types' );
        submit_button();
        ?>
    </form>
</div>