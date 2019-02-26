<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();

class Recrm_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
            'recrm',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );

    }

    /**
     * Set default options
     *
     * @since    1.0.0
     */
    public function default_options() {
        $propsNamesEstate = is_array( get_option( 'recrm_estate_props' )) ? get_option( 'recrm_estate_props' ) : array();
        if(count($propsNamesEstate) <= 0)
        {
            $names = array(
                'recrm_estate_id' => __('ID', 'recrm'),
                'recrm_estate_building_id' => __('ID of the building', 'recrm'),
                'recrm_estate_agent_id' => __('Agent ID', 'recrm'),
                'recrm_estate_city_id' => __('ID of the city', 'recrm'),
                'recrm_estate_district_id' => __('ID of the district', 'recrm'),
                'recrm_estate_metro_id' => __('Metro ID', 'recrm'),
                'recrm_estate_owner_id' => __('ID of the owner', 'recrm'),
                'recrm_estate_country_id' => __('ID of the country', 'recrm'),
                'recrm_estate_type_id' => __('Property type ID', 'recrm'),
                'recrm_estate_meta_title' => __('Meta title', 'recrm'),
                'recrm_estate_url' => __('Url', 'recrm'),
                'recrm_estate_zoom' => __('Zoom maps', 'recrm'),
                'recrm_estate_address' => __('Address', 'recrm'),
                'recrm_estate_balcony' => __('Balcony', 'recrm'),
                'recrm_estate_bathhouse' => __('Bathhouse', 'recrm'),
                'recrm_estate_pool' => __('Pool', 'recrm'),
                'recrm_estate_territory_improvement' => __('Accomplishment of territory', 'recrm'),
                'recrm_estate_route' => __('Nearest highway', 'recrm'),
                'recrm_estate_currency' => __('Currency', 'recrm'),
                'recrm_estate_bathrooms' => __('The bathroom', 'recrm'),
                'recrm_estate_ventilation' => __('Ventilation', 'recrm'),
                'recrm_estate_view_from_window' => __('The view from the window', 'recrm'),
                'recrm_estate_inside_mart' => __('Domestic market', 'recrm'),
                'recrm_estate_water' => __('Water', 'recrm'),
                'recrm_estate_ipoteka' => __('Mortgage option', 'recrm'),
                'recrm_estate_credit' => __('Installment option', 'recrm'),
                'recrm_estate_kids_allowed' => __('Take with children', 'recrm'),
                'recrm_estate_pets_allowed' => __('Take with animals', 'recrm'),
                'recrm_estate_ceiling_height' => __('Ceiling height', 'recrm'),
                'recrm_estate_gas' => __('Gas', 'recrm'),
                'recrm_estate_garage_or_parking' => __('Garage / Parking', 'recrm'),
                'recrm_estate_city' => __('City', 'recrm'),
                'recrm_estate_hot' => __('Hot offer', 'recrm'),
                'recrm_estate_complete_business' => __('Ready business', 'recrm'),
                'recrm_estate_type_group' => __('Real estate group', 'recrm'),
                'recrm_estate_edit_datetime' => __('Date and time of change', 'recrm'),
                'recrm_estate_creation_datetime' => __('Creation date and time', 'recrm'),
                'recrm_estate_edit_date' => __('Date of change', 'recrm'),
                'recrm_estate_repair_date' => __('Date of last repair', 'recrm'),
                'recrm_estate_creation_date' => __('Creation date', 'recrm'),
                'recrm_estate_longitude' => __('Longitude', 'recrm'),
                'recrm_estate_lease_type' => __('Lease type', 'recrm'),
                'recrm_estate_train_line' => __('W/d branch', 'recrm'),
                'recrm_estate_live_area' => __('Living area', 'recrm'),
                'recrm_estate_title' => __('Header', 'recrm'),
                'recrm_estate_is_suburban' => __('Suburban real estate', 'recrm'),
                'recrm_estate_deposit_time' => __('Deposit in months', 'recrm'),
                'recrm_estate_is_foreign' => __('Foreign real estate', 'recrm'),
                'recrm_estate_internet' => __('Internet', 'recrm'),
                'recrm_estate_infrastructure' => __('The infrastructure of the area', 'recrm'),
                'recrm_estate_land_usage' => __('Land use', 'recrm'),
                'recrm_estate_cadastral_number' => __('Cadastral number of the object', 'recrm'),
                'recrm_estate_sewage' => __('Sewerage', 'recrm'),
                'recrm_estate_class' => __('The building class', 'recrm'),
                'recrm_estate_loading_zones_count' => __('Number of loading zones', 'recrm'),
                'recrm_estate_flats_count' => __('Number of apartments in the building', 'recrm'),
                'recrm_estate_elevators_count' => __('Number of elevators', 'recrm'),
                'recrm_estate_parking_places' => __('Number of Parking spaces', 'recrm'),
                'recrm_estate_agent_comission' => __('The agent\'s Commission', 'recrm'),
                'recrm_estate_owner_comission' => __('The Commission from the owner', 'recrm'),
                'recrm_estate_rooms' => __('Rooms', 'recrm'),
                'recrm_estate_air_conditioning' => __('Conditioner', 'recrm'),
                'recrm_estate_full_year_approach' => __('Year-round access', 'recrm'),
                'recrm_estate_elevator' => __('Elevator', 'recrm'),
                'recrm_estate_loggia' => __('Loggia', 'recrm'),
                'recrm_estate_wall_material' => __('Wall material', 'recrm'),
                'recrm_estate_location' => __('Location', 'recrm'),
                'recrm_estate_meta_description' => __('Meta description', 'recrm'),
                'recrm_estate_meta_keywords' => __('Meta keywords', 'recrm'),
                'recrm_estate_metro_walk_time' => __('Minutes walk to metro (integer)', 'recrm'),
                'recrm_estate_building_title' => __('The name of the building', 'recrm'),
                'recrm_estate_purpose' => __('The purpose of the project', 'recrm'),
                'recrm_estate_furniture' => __('The presence of furniture in the living room', 'recrm'),
                'recrm_estate_furniture_kitchen' => __('The presence of furniture in the kitchen', 'recrm'),
                'recrm_estate_washing_machine' => __('Availability of washing machine', 'recrm'),
                'recrm_estate_deposit' => __('The presence of the security Deposit', 'recrm'),
                'recrm_estate_fridge' => __('Having a refrigerator', 'recrm'),
                'recrm_estate_direction' => __('Direction', 'recrm'),
                'recrm_estate_place' => __('Human settlement', 'recrm'),
                'recrm_estate_is_new' => __('New building', 'recrm'),
                'recrm_estate_house_number' => __('House number', 'recrm'),
                'recrm_estate_burden' => __('Encumbrances', 'recrm'),
                'recrm_estate_lock' => __('The object is locked', 'recrm'),
                'recrm_estate_hidden' => __('Object hidden', 'recrm'),
                'recrm_estate_windows' => __('Windows', 'recrm'),
                'recrm_estate_environment' => __('Environment', 'recrm'),
                'recrm_estate_phone' => __('Telephone operator', 'recrm'),
                'recrm_estate_description' => __('Description', 'recrm'),
                'recrm_estate_from_developer' => __('From the developer', 'recrm'),
                'recrm_estate_display_price' => __('Display the price', 'recrm'),
                'recrm_estate_heating' => __('Heating', 'recrm'),
                'recrm_estate_office_area' => __('Office space', 'recrm'),
                'recrm_estate_security_alarm' => __('Burglar alarm', 'recrm'),
                'recrm_estate_ramp' => __('Ramp', 'recrm'),
                'recrm_estate_parking_type' => __('Parking - view', 'recrm'),
                'recrm_estate_parking_ownership' => __('Private/shared Parking', 'recrm'),
                'recrm_estate_planning' => __('Layout', 'recrm'),
                'recrm_estate_truck_platform' => __('The maneuvering area for heavy vehicles', 'recrm'),
                'recrm_estate_area' => __('Area', 'recrm'),
                'recrm_estate_ground_area' => __('Land plot area', 'recrm'),
                'recrm_estate_room_area' => __('Room area', 'recrm'),
                'recrm_estate_building_rooms_area' => __('Building rooms size', 'recrm'),
                'recrm_estate_kitchen_area' => __('Kitchen area, m2', 'recrm'),
                'recrm_estate_area_of_each_room' => __('Area by rooms', 'recrm'),
                'recrm_estate_loading_equipment' => __('Loading equipment', 'recrm'),
                'recrm_estate_build_date' => __('Built', 'recrm'),
                'recrm_estate_prepay_months' => __('Prepayment months', 'recrm'),
                'recrm_estate_owner' => __('Accessory', 'recrm'),
                'recrm_estate_separated_bathroom' => __('Separate bathroom', 'recrm'),
                'recrm_estate_district' => __('District', 'recrm'),
                'recrm_estate_region_district' => __('Region district', 'recrm'),
                'recrm_estate_distance_to_ringway' => __('Distance to ring road', 'recrm'),
                'recrm_estate_distance' => __('Distance from city, km', 'recrm'),
                'recrm_estate_metro_transport_time' => __('Transport distance to metro, min.', 'recrm'),
                'recrm_estate_region' => __('Region', 'recrm'),
                'recrm_estate_relief' => __('Relief', 'recrm'),
                'recrm_estate_fire_alarm' => __('Fire extinguishing system', 'recrm'),
                'recrm_estate_building_stage' => __('Stage of construction', 'recrm'),
                'recrm_estate_metro_station' => __('Subway station', 'recrm'),
                'recrm_estate_status' => __('Status', 'recrm'),
                'recrm_estate_price_total' => __('Cost', 'recrm'),
                'recrm_estate_country' => __('Country', 'recrm'),
                'recrm_estate_sublease' => __('Sublease', 'recrm'),
                'recrm_estate_tv' => __('TV', 'recrm'),
                'recrm_estate_condition' => __('The current status of the project', 'recrm'),
                'recrm_estate_type' => __('Type', 'recrm'),
                'recrm_estate_nds_type' => __('The VAT type', 'recrm'),
                'recrm_estate_building_type' => __('Type of building', 'recrm'),
                'recrm_estate_land_type' => __('Type of land', 'recrm'),
                'recrm_estate_heating_type' => __('Type of heating', 'recrm'),
                'recrm_estate_ceiling_type' => __('The type of floor', 'recrm'),
                'recrm_estate_offer_type' => __('Offer type', 'recrm'),
                'recrm_estate_deal' => __('Type of transaction', 'recrm'),
                'recrm_estate_toponym' => __('Toponym', 'recrm'),
                'recrm_estate_street' => __('Street', 'recrm'),
                'recrm_estate_land_shape' => __('The shape of the plot', 'recrm'),
                'recrm_estate_gallery_photos' => __('Photos', 'recrm'),
                'recrm_estate_gallery_building' => __('Photos of the building', 'recrm'),
                'recrm_estate_gallery_layouts' => __('Photos of layouts', 'recrm'),
                'recrm_estate_thumbnail' => __('Photo', 'recrm'),
                'recrm_estate_price_meter' => __('Price per m2', 'recrm'),
                'recrm_estate_price_per_meter' => __('Price per m2', 'recrm'),
                'recrm_estate_latitude' => __('Latitude', 'recrm'),
                'recrm_estate_exclusive_contract_with_owner' => __('Exclusive contract with the owner', 'recrm'),
                'recrm_estate_electricity_power' => __('Electric power, kw', 'recrm'),
                'recrm_estate_electricity' => __('Electricity', 'recrm'),
                'recrm_estate_floor' => __('Floor', 'recrm'),
                'recrm_estate_floors_total' => __('Floors', 'recrm'),
            );
            update_option( 'recrm_estate_props', $names );
        }

        $propsNamesAgent = is_array( get_option( 'recrm_agent_props' )) ? get_option( 'recrm_agent_props' ) : array();
        if(count($propsNamesAgent) <= 0)
        {
            $names = array(
                'recrm_agent_id' => __('ID', 'recrm'),
                'recrm_agent_name' => __('Name', 'recrm'),
                'recrm_agent_email' => __('EMail', 'recrm'),
                'recrm_agent_group_name' => __('Group name', 'recrm'),
                'recrm_agent_phone' => __('Phone', 'recrm'),
                'recrm_agent_mobile_phone' => __('Modile phone', 'recrm'),
                'recrm_agent_position' => __('Position', 'recrm'),
                'recrm_agent_role' => __('Role', 'recrm'),
            );
            update_option( 'recrm_agent_props', $names );
        }

    }

}
