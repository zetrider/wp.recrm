<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();

class Recrm_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/recrm-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/recrm-admin.js', array( 'jquery' ), $this->version, false );

    }

    /**
     * intermediate_image_sizes_advanced
     *
     * @since    1.0.0
     */
    public function intermediate_image_sizes_advanced() {
        if(defined('RECRM_CRON') AND RECRM_CRON === 'Y')
        {
            return array();
        }
        else
        {
            return $sizes;
        }
    }

    /**
     * Register admin menu
     *
     * @since    1.0.0
     */
    public function admin_menu() {
        add_menu_page( __( 'ReCRM Settings', 'recrm' ), __('ReCRM', 'recrm'), 'manage_options', $this->plugin_name, array( $this, 'admin_menu_display_settings' ), '', 76 );
        add_submenu_page( 'recrm', __( 'Estate types', 'recrm' ), __( 'Estate Types & Category', 'recrm' ), 'manage_options', 'recrm_estate_types', array( $this, 'admin_menu_display_types' ) );
        add_submenu_page( 'recrm', __( 'Estate props', 'recrm' ), __( 'Estate props', 'recrm' ), 'manage_options', 'recrm_estate_props', array( $this, 'admin_menu_display_props' ) );
    }

    /**
     * Include admin menu settings view
     *
     * @since    1.0.0
     */
    public function admin_menu_display_settings() {
        if(isset($_GET['settings-updated']) AND $_GET['settings-updated'] == true)
        {
            flush_rewrite_rules();
        }
        include( plugin_dir_path( __FILE__ ) . 'partials/recrm-admin-display-settings.php' );
    }

    /**
     * Include admin menu types view
     *
     * @since    1.0.0
     */
    public function admin_menu_display_types() {
        include( plugin_dir_path( __FILE__ ) . 'partials/recrm-admin-display-types.php' );
    }

    /**
     * Include admin menu props view
     *
     * @since    1.0.0
     */
    public function admin_menu_display_props() {
        include( plugin_dir_path( __FILE__ ) . 'partials/recrm-admin-display-props.php' );
    }

    /**
     * Plugin settings
     *
     * @since    1.0.0
     */
    public function plugin_settings() {

        register_setting( 'recrm_options', 'recrm_options', array( $this, 'recrm_sanitize_callback' ) );

        add_settings_section( 'recrm_settings_api', __( 'API', 'recrm' ), '', 'recrm' );

        $params = array(
            'type'        => 'text',
            'id'          => 'api_key',
            'desc'        => __( 'API key ReCRM. For more secure key storage, you can define a constant define("RECRM_IMPORT_API_KEY", "YOUR_KEY");', 'recrm' ),
            'label_for'   => 'api_key',
            'placeholder' => defined("RECRM_IMPORT_API_KEY") ? RECRM_IMPORT_API_KEY : "",
            'disabled'    => defined("RECRM_IMPORT_API_KEY"),
            'default'     => defined("RECRM_IMPORT_API_KEY") ? RECRM_IMPORT_API_KEY : "",
        );
        add_settings_field( 'api_key', __( 'Key', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_api', $params );

        $params = array(
            'type'      => 'checkbox',
            'id'        => 'cron_active',
            'desc'      => __( 'Import active', 'recrm' ),
            'label_for' => 'cron_active',
        );
        add_settings_field( 'cron_active', __( 'Cron', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_api', $params );

        $params = array(
            'type'      => 'checkbox',
            'id'        => 'with_watermark',
            'desc'      => __( 'Import images with watermark', 'recrm' ),
            'label_for' => 'with_watermark',
        );
        add_settings_field( 'with_watermark', __( 'Watermark', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_api', $params );

        $params = array(
            'type'      => 'checkbox',
            'id'        => 'with_hidden',
            'desc'      => __( 'Import of hidden estate', 'recrm' ),
            'label_for' => 'with_hidden',
        );
        add_settings_field( 'with_hidden', __( 'Hidden estate', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_api', $params );

        $params = array(
            'type'      => 'checkbox',
            'id'        => 'with_status_0',
            'desc'      => __( 'Import active estate', 'recrm' ),
            'label_for' => 'with_status_0',
        );
        add_settings_field( 'with_status_0', __( 'Status Active', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_api', $params );

        $params = array(
            'type'      => 'checkbox',
            'id'        => 'with_status_1',
            'desc'      => __( 'Import successful estate', 'recrm' ),
            'label_for' => 'with_status_1',
        );
        add_settings_field( 'with_status_1', __( 'Status Successful', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_api', $params );

        $params = array(
            'type'      => 'checkbox',
            'id'        => 'with_status_2',
            'desc'      => __( 'Import unsuccessful estate', 'recrm' ),
            'label_for' => 'with_status_2',
        );
        add_settings_field( 'with_status_2', __( 'Status Unsuccessful', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_api', $params );

        add_settings_section( 'recrm_settings_permalink', __( 'Permalink Settings', 'recrm' ), '', 'recrm' );

        $params = array(
            'type'      => 'checkbox',
            'id'        => 'agents_with_tax',
            'label_for' => 'agents_with_tax',
        );
        add_settings_field( 'agents_with_tax', __( 'Agent with taxonomy', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_permalink', $params );

        $params = array(
            'type'      => 'text',
            'id'        => 'permalink_agents_structure',
            'desc'      => sprintf(__( 'Agents prefix for url, example: agents, it will by like %s', 'recrm' ), site_url('/agents/')),
            'label_for' => 'permalink_agents_structure',
        );
        add_settings_field( 'permalink_agents_structure', __( 'Agents prefix', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_permalink', $params );

        $params = array(
            'type'      => 'checkbox',
            'id'        => 'estate_with_tax',
            'label_for' => 'estate_with_tax',
        );
        add_settings_field( 'estate_with_tax', __( 'Estate with taxonomy', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_permalink', $params );

        $params = array(
            'type'      => 'text',
            'id'        => 'permalink_estate_structure',
            'desc'      => sprintf(__( 'Estate prefix for url, example: estate, it will by like %s', 'recrm' ), site_url('/estate/')),
            'label_for' => 'permalink_estate_structure',
        );
        add_settings_field( 'permalink_estate_structure', __( 'Estate prefix', 'recrm' ), array( $this, 'recrm_settings_fill' ), 'recrm', 'recrm_settings_permalink', $params );

    }

    /**
     * Plugin estate types
     *
     * @since    1.0.0
     */
    public function plugin_estate_types() {

        register_setting( 'recrm_estate_types', 'recrm_estate_types', array( $this, 'recrm_sanitize_callback' ) );

        add_settings_section( 'recrm_estate_types', __( 'Estate type relation with category', 'recrm' ), '', 'recrm_estate_types' );

        $settings = Recrm::settings();
        $terms    = array();
        $groups   = array();

        // todo
        if(isset($_GET['page']) AND $_GET['page'] == 'recrm_estate_types')
        {
            $terms = get_terms( array(
                'taxonomy'   => 'recrm_estate_tax',
                'hide_empty' => false,
            ) );

            $settings['throw_exception_auth'] = false;

            $import = new Recrm_Import($settings);
            $groups = $import->groups(true);
        }

        if($settings['estate_with_tax'] != 'on')
        {
            $params = array(
                'type'      => 'error',
                'id'        => 'error',
                'desc'      => __( 'Please, enable tax for Estate in settings page', 'recrm' ),
                'label_for' => 'error',
            );
            add_settings_field( 'error', __('Error', 'recrm'), array( $this, 'recrm_estate_types_fill' ), 'recrm_estate_types', 'recrm_estate_types', $params );
        }
        elseif(count($terms) <= 0)
        {
            $params = array(
                'type'      => 'error',
                'id'        => 'error',
                'desc'      => __( 'Please, create a estate category', 'recrm' ),
                'label_for' => 'error',
            );
            add_settings_field( 'error', __('Error', 'recrm'), array( $this, 'recrm_estate_types_fill' ), 'recrm_estate_types', 'recrm_estate_types', $params );
        }
        elseif(count($groups) <= 0)
        {
            $params = array(
                'type'      => 'error',
                'id'        => 'error',
                'desc'      => __( 'Please, enter API Key', 'recrm' ),
                'label_for' => 'error',
            );
            add_settings_field( 'error', '', array( $this, 'recrm_estate_types_fill' ), 'recrm_estate_types', 'recrm_estate_types', $params );
        }
        else
        {
            $options = array();
            foreach($terms AS $term):
                $options[$term->term_id] = $term->name;
            endforeach;

            foreach ($groups as $group):
                foreach ($group['types'] as $type):
                    $name = __('Type:', 'recrm') . ' ' . $type['name'];
                    if(strlen($type['sub_type'])):
                        $name .= ' (' . $type['sub_type'] . ')';
                    endif;
                    $desc = __( 'Group:', 'recrm' ) . ' ' . $group['name'] . ' ' . $name;
                    $params = array(
                        'type'      => 'select',
                        'id'        => 'types_'.$type['id'],
                        'desc'      => $desc,
                        'label_for' => 'types_'.$type['id'],
                        'values'    => $options,
                    );
                    add_settings_field( 'types_'.$type['id'], $name, array( $this, 'recrm_estate_types_fill' ), 'recrm_estate_types', 'recrm_estate_types', $params );
                endforeach;
            endforeach;
        }
    }

    /**
     * Plugin props
     *
     * @since    1.0.0
     */
    public function plugin_estate_props() {

        global $wpdb;

        register_setting( 'recrm_estate_props', 'recrm_estate_props', array( $this, 'recrm_sanitize_callback' ) );

        add_settings_section( 'recrm_estate_props', __( 'The name of the properties', 'recrm' ), '', 'recrm_estate_props' );

        $res = $wpdb->get_results("SELECT * FROM `$wpdb->postmeta` WHERE meta_key LIKE 'recrm_estate%' GROUP BY meta_key");
        if(count($res) <= 0)
        {
            $params = array(
                'type'      => 'error',
                'id'        => 'error',
                'desc'      => __( 'Please first import the objects', 'recrm' ),
                'label_for' => 'error',
            );
            add_settings_field( 'error', __('Error', 'recrm'), array( $this, 'recrm_props_fill' ), 'recrm_estate_props', 'recrm_estate_props', $params );
        }
        else
        {
            foreach($res as $row):
                $params = array(
                    'type'      => 'text',
                    'id'        => $row->meta_key,
                    'label_for' => $row->meta_key,
                );
                add_settings_field( $row->meta_key, $row->meta_key, array( $this, 'recrm_props_fill' ), 'recrm_estate_props', 'recrm_estate_props', $params );
            endforeach;
        }
    }

    /**
     * Plugin settings fields
     *
     * @since    1.0.0
     */
    public function recrm_settings_fill($args)
    {
        $this->recrm_options_view($args, 'recrm_options');
    }

    /**
     * Plugin types fields
     *
     * @since    1.0.0
     */
    public function recrm_estate_types_fill($args)
    {
        $this->recrm_options_view($args, 'recrm_estate_types');
    }

    /**
     * Plugin props fields
     *
     * @since    1.0.0
     */
    public function recrm_props_fill($args)
    {
        $this->recrm_options_view($args, 'recrm_estate_props');
    }

    /**
     * Plugin options view
     *
     * @since    1.0.0
     */
    public function recrm_options_view($args, $option_name)
    {
        extract( $args );

        $options = get_option( $option_name );

        switch ( $type ) {

            case 'error':
                $options[$id] = esc_attr( stripslashes( $options[$id] ) );
                echo (!empty($desc)) ? '<span class="description">' . $desc . '</span>' : '';
            break;

            case 'text':
                $value = $options[$id] = esc_attr( stripslashes( $options[$id] ) );
                if(isset($disabled) AND $disabled === true AND $default)
                {
                    $value = $default;
                }
                echo '<input class="regular-text" type="text" id="' . $id . '" name="' . $option_name . '[' . $id . ']" value="' . $value . '" placeholder="' . (isset($placeholder) ? esc_attr($placeholder) : '') . '"'.((isset($disabled) AND $disabled === true) ? ' disabled' : '').' />';
                echo (!empty($desc)) ? '<br /><span class="description">' . $desc . '</span>' : '';
            break;

            case 'textarea':
                $options[$id] = esc_attr( stripslashes( $options[$id] ) );
                echo '<textarea class="large-text" cols="50" rows="10" id="' . $id . '" name="' . $option_name . '[' . $id . ']">' . $options[$id] . '</textarea>';
                echo (!empty($desc)) ? '<br /><span class="description">' . $desc . '</span>' : '';
            break;

            case 'checkbox':
                $checked = ($options[$id] == 'on') ? ' checked="checked"' :  '';
                echo '<label><input type="checkbox" id="' . $id . '" name="' . $option_name . '[' . $id . ']"' . $checked. ' /> ';
                echo (!empty($desc)) ? $desc : '';
                echo '</label>';
            break;

            case 'select':
                $multiple = (isset($multiple) AND $multiple == 'on') ? ' multiple=""' :  '';
                echo '<select id="' . $id . '" name="' . $option_name . '[' . $id . ']"' . $multiple . '>';
                foreach ($values as $val => $name) {

                    $selected = ($options[$id] == $val) ? 'selected="selected"' : '';
                    echo '<option value="' . $val . '"' . $selected . '>' . $name . '</option>';

                }
                echo '</select>';
                echo (!empty($desc)) ? '<br /><span class="description">' . $desc . '</span>' : '';
            break;

            case 'radio':
                echo '<fieldset>';
                foreach ($values as $val => $name) {
                    $checked = ($options[$id] == $val) ? 'checked="checked"' : '';
                    echo '<label><input type="radio" name="' . $option_name . '[' . $id . ']" value="'. $val . '"' . $checked. ' />' . $name . '</label><br />';
                }
                echo '</fieldset>';
            break;
        }
    }

    /**
     * Plugin settings validate
     *
     * @since    1.0.0
     */
    public function recrm_sanitize_callback($input) {

        foreach( $input as $key => $val ) {

            if(is_array($val))
            {
                $val = array_map('trim', $val);
            }
            else
            {
                $val = trim($val);
            }

            $valid_input[$key] = $val;

        }

        return $valid_input;
    }

    /**
     * Meta boxes
     *
     * @since    1.0.0
     */
    public function meta_boxes() {
        add_meta_box( 'recrm_meta_box_estate_properties', __('Estate properties'), array($this, 'meta_box_estate_properties'), 'recrm_estate', 'normal', 'high'  );
        add_meta_box( 'recrm_meta_box_agent_properties', __('Estate properties'), array($this, 'meta_box_agent_properties'), 'recrm_agents', 'normal', 'high'  );
    }

    /**
     * Meta boxe content
     *
     * @since    1.0.0
     */
    public function meta_box_estate_properties( $post ) {
        global $wpdb;
    ?>
        <div class="recrm_meta_box_properties">
            <?
            $names = array(
                'gallery_photos'   => __('Photos', 'recrm'),
                'gallery_layouts'  => __('Layouts', 'recrm'),
                'gallery_building' => __('Building', 'recrm'),
            );
            $gallery = get_post_meta($post->ID, 'recrm_gallery_estate', true);
            $gallery = is_serialized($gallery) ? unserialize($gallery) : $gallery;
            $gallery = is_array($gallery) ? $gallery : array();
            $counter = 0;
            foreach($gallery AS $type => $images):
                if(count($images) AND array_key_exists($type, $names)):
                    $counter++;
            ?>
                <div class="gallery">
                    <h4 class="gallery-title"><?php echo $names[$type]; ?></h4>
                    <div class="gallery-code"><small>recrm_gallery_estate[<?=$type?>]</small></div>
                    <div class="items">
                        <?php foreach($images AS $img_id => $img_external_url): ?>
                            <div class="item" style="background-image: url(<?php echo $img_external_url; ?>);"></div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php
                endif;
            endforeach;
            if($counter == 0):
                _e('Gallery is empty', 'recrm');
            endif;
            ?>
            <div class="proeprties">
                <h4><?_e('Properties', 'recrm')?></h4>
                <?
                $propsNames = is_array( get_option( 'recrm_estate_props' )) ? get_option( 'recrm_estate_props' ) : array();
                $res = $wpdb->get_results("SELECT * FROM `$wpdb->postmeta` WHERE `post_id` = '$post->ID' AND meta_key LIKE 'recrm_estate%'");
                foreach($res as $row):
                    $name = $propsNames[$row->meta_key];
                    $val  = $row->meta_value;
                    if(strlen($val) <= 0)
                    {
                        continue;
                    }
                    if($val == 'true')
                    {
                        $val = __('Yes', 'recrm');
                    }
                    elseif($val == 'false')
                    {
                        $val = __('No', 'recrm');
                    }
                ?>
                <div class="property">
                    <div class="property-name">
                        <?=strlen($name) ? $name : '...'?><br>
                        <small><?=$row->meta_key?></small>
                    </div>
                    <div class="property-value">
                        <?if(is_serialized($val)):?>
                            <div class="property-serialized" data-recrm-property-show="parent">
                                <a href="#" data-recrm-property-show="button">Serialized (show)</a>
                                <div class="d-none" data-recrm-property-show="content"><?=unserialize($val)?></div>
                            </div>
                        <?else:?>
                            <?=$val?>
                        <?endif?>
                    </div>
                </div>
                <?endforeach?>
            </div>
        </div>
        <?
    }

    /**
     * Meta box agent content
     *
     * @since    1.0.0
     */
    public function meta_box_agent_properties( $post ) {
        global $wpdb;
        ?>
        <div class="recrm_meta_box_properties">
            <div class="proeprties">
                <h4><?_e('Properties', 'recrm')?></h4>
                <?
                $propsNames = is_array( get_option( 'recrm_agent_props' )) ? get_option( 'recrm_agent_props' ) : array();
                $res = $wpdb->get_results("SELECT * FROM `$wpdb->postmeta` WHERE `post_id` = '$post->ID' AND meta_key LIKE 'recrm_agent%'");
                foreach($res as $row):
                    $name = $propsNames[$row->meta_key];
                    $val  = $row->meta_value;
                    if(strlen($val) <= 0)
                    {
                        continue;
                    }
                    if($val == 'true')
                    {
                        $val = __('Yes', 'recrm');
                    }
                    elseif($val == 'false')
                    {
                        $val = __('No', 'recrm');
                    }
                ?>
                <div class="property">
                    <div class="property-name">
                        <?=strlen($name) ? $name : '...'?><br>
                        <small><?=$row->meta_key?></small>
                    </div>
                    <div class="property-value">
                        <?if(is_serialized($val)):?>
                            <div class="property-serialized" data-recrm-property-show="parent">
                                <a href="#" data-recrm-property-show="button">Serialized (show)</a>
                                <div class="d-none" data-recrm-property-show="content"><?=unserialize($val)?></div>
                            </div>
                        <?else:?>
                            <?=$val?>
                        <?endif?>
                    </div>
                </div>
                <?endforeach?>
            </div>
        </div>
        <?
    }
}
