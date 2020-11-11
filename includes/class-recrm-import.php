<?php

/**
 * Import data from RcCrm Api v1.
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();

use \Curl\MultiCurl;
use \Curl\Curl;

class Recrm_Import {

    /**
     * Api Key ReCRM v1
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $api_key    Authorization key
     */
    protected $api_key;

    /**
     * Api Url ReCRM v1
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $api_url    Request to
     */
    protected $api_url;

    /**
     * Api Routes ReCRM v1
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $api_routes    Routes for request
     */
    protected $api_routes;

    /**
     * Imprt images with watermark
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $with_watermark
     */
    protected $with_watermark;

    /**
     * Imprt hidden estate objects
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $with_hidden
     */
    protected $with_hidden;

    /**
     * Imprt estate objects with status 0
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $with_status_0
     */
    protected $with_status_0;

    /**
     * Imprt estate objects with status 1
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $with_status_1
     */
    protected $with_status_1;

    /**
     * Imprt estate objects with status 2
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $with_status_2
     */
    protected $with_status_2;

    /**
     * Agent fields from api
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $agent_fields
     */
    protected $agent_fields;

    /**
     * Estate fields from api
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $estate_fields
     */
    protected $estate_fields;

    /**
     * Need throw exception whey api key isn't correct
     *
     * @since    1.0.0
     * @access   public
     * @var      bool    $throw_exception_auth
     */
    public $throw_exception_auth;

    /**
     * Define the core functionality of the import.
     *
     * @param  array $settings Plugin settings
     * array[api_key] string  Api Key
     * array[with_watermark]  string on/off
     * array[with_hidden]     string on/off
     * array[with_status_0]   string on/off
     * array[with_status_1]   string on/off
     * array[with_status_2]   string on/off
     * array[throw_exception_auth]   bool
     *
     * @since    1.0.0
     */
    public function __construct( $settings = array() ) {

        $this->api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';

        $this->with_watermark = isset( $settings['with_watermark'] ) ? $settings['with_watermark'] : '';
        $this->with_hidden    = isset( $settings['with_hidden'] ) ? $settings['with_hidden'] : '';
        $this->with_status_0  = isset( $settings['with_status_0'] ) ? $settings['with_status_0'] : '';
        $this->with_status_1  = isset( $settings['with_status_1'] ) ? $settings['with_status_1'] : '';
        $this->with_status_2  = isset( $settings['with_status_2'] ) ? $settings['with_status_2'] : '';

        $this->api_url       = 'http://api.recrm.ru/json';
        $this->api_routes    = Recrm_Reference::api_routes();
        $this->agent_fields  = Recrm_Reference::agent_fields();
        $this->estate_fields = Recrm_Reference::estate_fields();

        $this->throw_exception_auth = isset( $settings['throw_exception_auth'] ) ? $settings['throw_exception_auth'] : true;

        $this->check_auth();
    }

    /**
     * Get endpoint by route key
     *
     * @since    1.0.0
     */
    public function get_endpoint($route = '', $params = array()) {
        $params['key'] = $this->api_key;
        return $this->api_url . $this->api_routes[$route] . '?' . http_build_query($params);
    }

    /**
     * Request method
     *
     * @since    1.0.0
     */
    public function request($endpoint = '') {

        $curl = new Curl();
        $curl->get($endpoint);
        $error = $curl->error;
        $response = json_decode( $curl->getRawResponse(), true );
        $curl->close();

        //do_action('recrm_import_log', 'request', $endpoint);

        if($error OR $response === false OR is_null($response))
        {
            throw new \Recrm_Exception( __( 'API not responding', 'recrm' ) );
        }

        return $response;
    }

    /**
     * Conver phone
     * @param  string $phone
     * @return string
     */
    public function convert_phone($phone = '')
    {
        $res = $phone;
        if(strlen($phone))
        {
            $res = preg_replace('/[^0-9+]/', '', $phone);
        }
        $res = apply_filters( 'recrm_import_convert_phone', $res, $phone);
        return $res;
    }

    /**
     * Check auth key
     *
     * @since    1.0.0
     */
    public function check_auth() {
        $endpoint = $this->get_endpoint( 'check_auth' );
        $response = $this->request( $endpoint );

        if(isset($response['error']))
        {
            if($this->throw_exception_auth === true)
            {
                throw new \Recrm_Exception( __( 'API key is not valid', 'recrm' ) );
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Agents fields
     *
     * @since    1.0.0
     */
    public function get_agent_fields() {
        return $this->agent_fields;
    }

    /**
     * Estate fields
     *
     * @since    1.0.0
     */
    public function get_estate_fields() {
        return $this->estate_fields;
    }

    /**
     * Estate Group
     *
     * @since    1.0.0
     */
    public function groups($with_subtypes = false) {
        $endpoint = $this->get_endpoint( 'estatetgroups' );
        $response = $this->request( $endpoint );
        $groups   = is_array( $response['groups'] ) ? $response['groups'] : array();
        $data     = array();

        foreach($groups AS $group) {
            $data[$group['id']] = $group;

            if($with_subtypes === true)
            {
                $data[$group['id']]['types'] = $this->types($group['id']);
            }
        }

        return $data;
    }

    /**
     * Estate types
     *
     * @since    1.0.0
     */
    public function types($group_id) {
        $endpoint = $this->get_endpoint( 'estatetypes', array('group_id' => $group_id) );
        $response = $this->request( $endpoint );
        $types    = is_array( $response['types'] ) ? $response['types'] : array();
        $data     = array();

        foreach($types AS $type) {
            $data[$type['id']] = $type;
        }

        return $data;
    }

    /**
     * Agents
     *
     * @since    1.0.0
     */
    public function agents() {

        $this->data = array();

        $endpoint = $this->get_endpoint( 'agents_search' );
        $response = $this->request( $endpoint );
        $agents   = is_array( $response['agents'] ) ? $response['agents'] : array();

        // Init Multi Curl
        $MultiCurl = new MultiCurl();

        // For multi search images
        $this->url_images = array();

        // Each agent
        foreach($agents AS $agent) {

            $data = array();
            foreach($agent as $key => $val)
            {
                // Convery phone
                if(in_array($key, array('phone', 'mobile_phone')))
                {
                    $val = $this->convert_phone($val);
                }
                // Convery keys
                $data['recrm_agent_' . $key] = $val;
            }

            // Set empty fields
            foreach($this->get_agent_fields() as $field) {
                if(!isset($data[$field])) {
                    $data[$field] = false;
                }
            }

            // Save var
            $this->data[$agent['id']] = $data;

            // Get photos later
            $photo_endpoint = $this->get_endpoint( 'agents_photo', array(
                'agent_id'  => $agent['id'],
                'width'     => 1920,
                'height'    => 1080,
                'watermark' => $this->with_watermark == 'on' ? 1 : 0,
            ) );
            $this->url_images[$agent['id']] = $photo_endpoint;
            $MultiCurl->addGet($photo_endpoint);
        }

        // Import photos
        $MultiCurl->success(function($instance) {
            $agent_id = array_search($instance->url, $this->url_images);
            $images   = is_array( $instance->response->pictures ) ? $instance->response->pictures : array();
            foreach($images AS $image) {
                if(!empty($image->url))
                {
                    $this->data[$agent_id]['recrm_agent_thumbnail'][] = $image->url;
                }
            }
        });
        $MultiCurl->start();
        $MultiCurl->close();

        unset($agents);
        unset($this->url_images);

        return $this->data;
    }

    /**
     * Properties
     *
     * @since    1.0.0
     */
    public function properties() {

        $this->data = array();

        $this->batch_elemets          = array();
        $this->batch_thumbnail        = array();
        $this->batch_gallery_photos   = array();
        $this->batch_gallery_layouts  = array();
        $this->batch_gallery_building = array();

        $default_params = array(
            'start' => 0,
            'count' => 500,
        );

        $with_statuses = array();
        if($this->with_status_0 === 'on')
        {
            $with_statuses[] = 0;
        }
        if($this->with_status_1 === 'on')
        {
            $with_statuses[] = 1;
        }
        if($this->with_status_2 === 'on')
        {
            $with_statuses[] = 2;
        }

        if(count($with_statuses) < 0)
        {
            throw new \Recrm_Exception( __( 'No statuses selected in plugin settings', 'recrm' ) );
        }

        if($this->with_hidden === 'on')
        {
            $default_params['search_hidden'] = '1';
        }

        // Search estate
        for($status = 0; $status <= max($with_statuses); $status++)
        {
            // Get total estate count
            $counter_params = $default_params;
            $counter_params['count'] = 1;
            $counter_params['status'] = $status;

            $endpoint    = $this->get_endpoint( 'estate_search', $counter_params );
            $response    = $this->request( $endpoint );
            $total_count = intval($response['total_count']);

            if($total_count > 0)
            {
                $steps = ceil($total_count / $default_params['count']);
                for($step = 0; $step <= $steps; $step++)
                {
                    $step_params = $default_params;
                    $step_params['start']  = $default_params['count'] * $step;
                    $step_params['status'] = $status;

                    $endpoint = $this->get_endpoint( 'estate_search', $step_params );
                    $response = $this->request( $endpoint );

                    $results = is_array( $response['results'] ) ? $response['results'] : array();

                    foreach($results AS $val)
                    {
                        // Get estate info later
                        $this->batch_elemets[$val['id']] =  $this->get_endpoint( 'estate_info', array( 'id' => $val['id'], 'description_format' => 1 ) );
                    }
                }
            }
        }

        // Import estate
        $MultiCurl = new MultiCurl();
        foreach($this->batch_elemets AS $estate_id => $url)
        {
            $MultiCurl->addGet($url);
        }
        $MultiCurl->success(function($instance) {
            $estate_id = array_search($instance->url, $this->batch_elemets);
            $response  = json_decode(json_encode($instance->response), true);
            $estate    = is_array( $response['property'] ) ? $response['property'] : array();
            $this->data[$estate_id] = $this->estate($estate);
        });
        $MultiCurl->error(function($instance) {
            throw new \Recrm_Exception( 'Error request:' . $instance->url.', Code: '.$instance->errorCode.', Message: '. $instance->errorMessage );
        });

        $MultiCurl->start();
        $MultiCurl->close();

        // Import images
        $MultiCurl = new MultiCurl();
        $images = array_merge($this->batch_thumbnail, $this->batch_gallery_photos, $this->batch_gallery_layouts, $this->batch_gallery_building);
        foreach($images AS $url)
        {
            $MultiCurl->addGet($url);
        }
        $MultiCurl->success(function($instance) {
            $prop_name = '';
            $estate_id = 0;
            if($id = array_search($instance->url, $this->batch_thumbnail))
            {
                $prop_name = 'thumbnail';
                $estate_id = $id;
            }
            elseif($id = array_search($instance->url, $this->batch_gallery_photos))
            {
                $prop_name = 'gallery_photos';
                $estate_id = $id;
            }
            elseif($id = array_search($instance->url, $this->batch_gallery_layouts))
            {
                $prop_name = 'gallery_layouts';
                $estate_id = $id;
            }
            elseif($id = array_search($instance->url, $this->batch_gallery_building))
            {
                $prop_name = 'gallery_building';
                $estate_id = $id;
            }
            $images = is_array( $instance->response->pictures ) ? $instance->response->pictures : array();
            foreach($images AS $image) {
                if(!empty($image->url))
                {
                    $this->data[$estate_id]['recrm_estate_'.$prop_name][] = $image->url;
                }
            }
        });
        $MultiCurl->error(function($instance) {
            throw new \Recrm_Exception( 'Error request:' . $instance->url.', Code: '.$instance->errorCode.', Message: '. $instance->errorMessage );
        });
        $MultiCurl->start();
        $MultiCurl->close();

        unset($this->batch_elemets);
        unset($this->batch_thumbnail);
        unset($this->batch_gallery_photos);
        unset($this->batch_gallery_building);

        return $this->data;
    }

    /**
     * Estate
     *
     * @since    1.0.0
     */
    protected function estate($estate) {

        if(isset($estate['id'])) {

            // Merge parameters
            if(is_array($estate['parameters'])) {
                foreach($estate['parameters'] AS $params) {
                    $estate[$params['name']] = $params['value'].(strlen($params['unit']) ? ' ' . $params['unit'] : '');
                }
                unset($estate['parameters']);
            }

            foreach($estate AS $key => $val) {

                unset($estate[$key]);

                // Convert parameter name, because can be like 'Condition (object) 2018'
                $key = strtolower($key);
                $key = preg_replace( '/[^a-z0-9_]/', '', $key );
                $key = substr($key, 0, 191);

                // Convert bool value
                if(is_bool($val) and $val === true) {
                    $val = 'true';
                }
                elseif(is_bool($val) and $val === false) {
                    $val = 'false';
                }

                $estate[$key] = $val;
            }

            // Import photos later
            $gallery_params = array(
                'thumbnail'        => 'estate_cover',
                'gallery_photos'   => 'estate_photo',
                'gallery_layouts'  => 'estate_layout',
                'gallery_building' => 'building_photo',
            );
            foreach($gallery_params AS $gallery_key => $gallery_route)
            {
                $estate[$gallery_key] = array();

                $args_key = 'estate_id';
                $args_val = $estate['id'];
                if($gallery_key == 'gallery_building')
                {
                    $args_key = 'building_id';
                    $args_val = $estate['building_id'];
                    if(intval($estate['building_id']) <= 0)
                    {
                        continue;
                    }
                }
                $endpoint = $this->get_endpoint( $gallery_route, array(
                    $args_key   => $args_val,
                    'width'     => 1920,
                    'height'    => 1080,
                    'watermark' => $this->with_watermark == 'on' ? 1 : 0,
                ) );
                $this->{'batch_' . $gallery_key}[$estate['id']] = $endpoint;
            }
        }

        // Convery keys
        foreach($estate as $key => $val)
        {
            unset($estate[$key]);
            $estate['recrm_estate_' . $key] = $val;
        }

        // Set empty fields
        foreach($this->get_estate_fields() as $field) {
            if(!isset($estate[$field])) {
                $estate[$field] = false;
            }
        }

        return $estate;
    }
}
