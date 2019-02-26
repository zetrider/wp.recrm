<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();

class Recrm_Activator {

    /**
     * Activate plugin
     *
     * @since    1.0.0
     */
    public static function activate() {
        self::activate_cron();
    }

    /**
     * Activate cron plugin
     *
     * @since    1.1.0
     */
    public static function activate_cron() {
        wp_clear_scheduled_hook( 'recrm_cron_import' );
        wp_schedule_event( time(), 'minute', 'recrm_cron_import');
    }
}
