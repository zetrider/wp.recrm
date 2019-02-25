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
class Recrm_Deactivator {

    /**
     * Deactivate plugin
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Todo ask about trash
        flush_rewrite_rules();
    }

}
