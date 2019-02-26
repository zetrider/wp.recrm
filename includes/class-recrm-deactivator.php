<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();

class Recrm_Deactivator {

    /**
     * Deactivate plugin
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        flush_rewrite_rules();
        wp_clear_scheduled_hook( 'recrm_cron_import' );
    }

}
