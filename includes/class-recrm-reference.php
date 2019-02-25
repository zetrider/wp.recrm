<?php

/**
 * Reference
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */
class Recrm_Reference {

    /**
     * Get api routes
     *
     * @since    1.0.0
     */
    public static function api_routes() {
        return array(
            'estate_info'    => '/estate/info',
            'estate_search'  => '/estate/search',
            'estate_cover'   => '/picture/EstateCoverPhoto',
            'estate_photo'   => '/picture/EstatePhoto',
            'estate_layout'  => '/picture/EstateLayout',
            'agents_search'  => '/agent/all',
            'agent_info'     => '/agent/info',
            'agents_photo'   => '/picture/AgentPhoto',
            'building_photo' => '/picture/BuildingPhoto',
            'check_auth'     => '/estatetypes/groups',
            'estatetgroups'  => '/estatetypes/groups',
            'estatetypes'    => '/estatetypes',
        );
    }

    /**
     * Get api agent fields
     *
     * @since    1.0.0
     */
    public static function agent_fields() {
        global $wpdb;
        $query = "SELECT `meta_key` FROM " . $wpdb->postmeta . " WHERE `meta_key` LIKE 'recrm_agent_%' GROUP BY meta_key";
        return $wpdb->get_col($query);
    }

    /**
     * Get api estate fields
     *
     * @since    1.0.0
     */
    public static function estate_fields() {
        global $wpdb;
        $query = "SELECT `meta_key` FROM " . $wpdb->postmeta . " WHERE `meta_key` LIKE 'recrm_estate_%' GROUP BY meta_key";
        return $wpdb->get_col($query);
    }

}
