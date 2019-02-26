<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();

class Recrm {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Recrm_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        if ( defined( 'RECRM_VERSION' ) ) {
            $this->version = RECRM_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        $this->plugin_name = 'recrm';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Plugin settings
     *
     * from options
     *
     * @since    1.0.0
     * @access   private
     */
    public static function settings() {

        $settings = is_array(get_option('recrm_options')) ? get_option('recrm_options') : array();
        $settings['estate_types'] = get_option('recrm_estate_types', array());

        if(defined('RECRM_IMPORT_API_KEY')) {
            $settings['api_key'] = RECRM_IMPORT_API_KEY;
        }

        return $settings;

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Recrm_Loader. Orchestrates the hooks of the plugin.
     * - Recrm_i18n. Defines internationalization functionality.
     * - Recrm_Admin. Defines all hooks for the admin area.
     * - Recrm_Import. Import class
     * - Recrm_Import_2_File. Import class
     * - Recrm_Store. Store class
     * - Recrm_Post_Types. Register post types
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * Composer
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

        /**
         * Pludin exception
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recrm-exception.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recrm-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recrm-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-recrm-admin.php';

        /**
         * The class responsible for references
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recrm-reference.php';

        /**
         * The class responsible for import data from recrm
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recrm-import.php';

        /**
         * The class responsible for import data from recrm and save data to temp tiles (extends)
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recrm-import-2-file.php';

        /**
         * The class responsible for store data from import
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recrm-store.php';

        /**
         * The class responsible for register post types
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-recrm-post-types.php';

        $this->loader = new Recrm_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Recrm_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Recrm_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'default_options' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Recrm_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_settings' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_estate_types' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_estate_props' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'meta_boxes' );
        //$this->loader->add_filter( 'intermediate_image_sizes_advanced', $plugin_admin, 'intermediate_image_sizes_advanced', 10, 2 );

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $post_types = new Recrm_Post_Types( $this->settings() );

        $this->loader->add_action( 'init', $post_types, 'agents', 0 );
        $this->loader->add_action( 'init', $post_types, 'estate', 0 );
        $this->loader->add_filter( 'generate_rewrite_rules', $post_types, 'generate_rewrite_rules' );
        $this->loader->add_filter( 'post_type_link', $post_types, 'post_type_link', 10, 2 );

        $reference = new Recrm_Reference();

        $this->loader->add_filter( 'cron_schedules', $reference, 'cron_add_one_min');

        // Cron import
        add_action( 'recrm_cron_import', 'recrm_import' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Recrm_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Get plugin settings
     *
     * @since     1.0.0
     * @return    array    From options
     */
    public function get_settings() {
        return $this->settings;
    }

}
