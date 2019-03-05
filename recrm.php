<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/zetrider/wp.recrm
 * @since             1.0.0
 *
 * @package           ReCRM
 *
 * @wordpress-plugin
 * Plugin Name:       ReCRM
 * Plugin URI:        https://github.com/zetrider/wp.recrm
 * Description:       ReCRM Import
 * Version:           1.1.2
 * Author:            ZetRider
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       recrm
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) or die();

define( 'RECRM_VERSION', '1.1.2' );
define( 'RECRM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'RECRM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-recrm-activator.php
 */
function activate_recrm() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-recrm-activator.php';
    Recrm_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-recrm-deactivator.php
 */
function deactivate_recrm() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-recrm-deactivator.php';
    Recrm_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_recrm' );
register_deactivation_hook( __FILE__, 'deactivate_recrm' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-recrm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function recrm() {

    $recrm = new Recrm();
    $recrm->run();

    return $recrm;

}
recrm();

/**
 * Begins execution of the plugin import.
 *
 * @since    1.0.0
 */
function recrm_import() {

    global $wpdb;

    $settings = Recrm::settings();

    if($settings['cron_active'] != 'on')
    {
        return;
    }

    if(isset($wpdb))
    {
        $wpdb->query('set wait_timeout = 3600');
    }

    $store    = new Recrm_Store( $settings );
    $import   = new Recrm_Import_2_File( $settings );
    $import->get_temp('agent', 30);

    // Import agents
    $store->store(array(
        'data'             => $import->get_temp('agent', 30),
        'post_type'        => 'recrm_agents',
        'post_key'         => 'agent',
        'post_title_key'   => 'recrm_agent_name',
        'post_content_key' => null,
    ));

    // Import estate
    $store->store(array(
        'data'             => $import->get_temp('estate', 30),
        'post_type'        => 'recrm_estate',
        'post_key'         => 'estate',
        'post_title_key'   => 'recrm_estate_title',
        'post_content_key' => 'recrm_estate_description',
    ));

    // Move to trash cart rests
    $store->trash($import);

    do_action('recrm_import_finish');

    return $import;
}
