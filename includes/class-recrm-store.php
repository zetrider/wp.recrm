<?php

/**
 * Store data from Import
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

use \Curl\MultiCurl;

class Recrm_Store {

    /**
     * Types for categories
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $estate_types
     */
    protected $estate_types;

    /**
     * Define the core functionality of the store.
     *
     * @param  array $settings Plugin settings
     * array[estate_types]   array relation types with tax
     *
     * @since    1.0.0
     */
    public function __construct( $settings = array() ) {

        $this->estate_types = isset( $settings['estate_types'] ) ? $settings['estate_types'] : array();

        // Upload dir
        $upload_dir = wp_upload_dir();
        $upload_tmp = $upload_dir['basedir'].'/tmp';
        if(!file_exists($upload_tmp))
        {
            mkdir($upload_tmp, 0777);
        }
        $this->upload_tmp = $upload_tmp;

        // todo check
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    /**
     * Make hash data
     *
     * @param array $data from import
     * @return string
     * @since    1.0.0
     */
    public static function hash($data = array()) {
        ksort($data);
        return md5(serialize($data));
    }

    /**
     * Save entities
     *
     * @param  array $data from import
     * string  $post_type post type
     * string  $post_key prop key for data
     * string  $post_title_key prop key for title
     * string  $post_content_key prop key for content
     * @since    1.0.0
     */
    public function store($args = array()) {

        global $wpdb;

        foreach($args AS $key => $val)
        {
            ${$key} = $val;
        }

        if(count($data))
        {
            $this->batch_thumbnail        = array();
            $this->batch_gallery_photos   = array();
            $this->batch_gallery_layouts  = array();
            $this->batch_gallery_building = array();

            $entity_ids   = array_keys($data);
            $entities     = array();
            $gallery      = array();
            $gallery_keys = array( 'thumbnail', 'gallery_photos', 'gallery_layouts', 'gallery_building' );

            $meta_key  = esc_sql('recrm_'.$post_key.'_id');
            $meta_val  = implode(',', array_map(function($v) { return "'".intval($v)."'"; }, $entity_ids));
            $entity_db = $wpdb->get_results("SELECT `post_id`, `meta_value` FROM `$wpdb->postmeta` WHERE `meta_key` = '".$meta_key."' AND `meta_value` IN ($meta_val)");
            $ids       = array();
            foreach($entity_db as $db_data) {
                $post_id     = $db_data->post_id;
                $external_id = $db_data->meta_value;

                $ids[$external_id] = $post_id;
                $entities[$post_id]['hash'] = get_post_meta($post_id, 'recrm_hash_'.$post_key, true);

                $entity_gallery = get_post_meta($post_id, 'recrm_gallery_'.$post_key, true);
                $entity_gallery = is_serialized($entity_gallery) ? unserialize($entity_gallery) : $entity_gallery;
                $entity_gallery = is_array($entity_gallery) ? $entity_gallery : array();
                $gallery[$post_id] = $entity_gallery;
            }
            foreach($data AS $item) {

                $item = array_map(function($val) {
                    return is_array($val) ? serialize($val) : (string) $val;
                }, $item);

                $hash = $this->hash($item);

                $post_id = $ids[$item['recrm_'.$post_key.'_id']];

                $tax_id = null;
                if(array_key_exists('types_' . $item['recrm_estate_type_id'], $this->estate_types))
                {
                    $tax_id = $this->estate_types['types_' . $item['recrm_estate_type_id']];
                }
                $tax_id = apply_filters( 'recrm_store_tax_id', $tax_id, $item);

                if($post_id > 0)
                {
                    $item['recrm_hash_'.$post_key] = $hash;
                    if($hash != $entities[$post_id]['hash'])
                    {
                        wp_insert_post(array(
                            'ID'           => $post_id,
                            'post_title'   => $item[$post_title_key],
                            'post_content' => $item[$post_content_key] ?: '',
                            'post_status'  => 'publish',
                            'post_type'    => $post_type,
                            'meta_input'   => $item,
                        ));

                        foreach($gallery_keys AS $key)
                        {
                            $item_images = $item['recrm_'.$post_key.'_'.$key];
                            $item_images = is_serialized($item_images) ? unserialize($item_images) : $item_images;
                            $item_images = is_array($item_images) ? $item_images : array();

                            if(is_array($gallery[$post_id][$key]) AND count($gallery[$post_id][$key]))
                            {
                                foreach($gallery[$post_id][$key] AS $img_id => $img_url)
                                {
                                    if(!in_array($img_url, $item_images))
                                    {
                                        wp_delete_attachment($img_id, true);
                                        unset($gallery[$post_id][$key][$img_id]);
                                        if($key == 'thumbnail')
                                        {
                                            delete_post_thumbnail($post_id);
                                        }
                                    }
                                    else
                                    {
                                        unset($item_images[array_search($img_url, $item_images)]);
                                    }
                                }
                            }
                            // Save images later
                            if(count($item_images))
                            {
                                foreach($item_images AS $img)
                                {
                                    $this->{'batch_' . $key}[$img] = $post_id;
                                }
                            }
                        }
                    }
                }
                else
                {
                    $post_id = wp_insert_post(array(
                        'post_title'   => $item[$post_title_key],
                        'post_content' => $item[$post_content_key] ?: '',
                        'post_status'  => 'publish',
                        'post_type'    => $post_type,
                        'meta_input'   => $item,
                    ));

                    if($post_id > 0)
                    {
                        foreach($gallery_keys AS $key)
                        {
                            $item_images = $item['recrm_'.$post_key.'_'.$key];
                            $item_images = is_serialized($item_images) ? unserialize($item_images) : $item_images;
                            $item_images = is_array($item_images) ? $item_images : array();
                            // Save images later
                            foreach($item_images AS $img)
                            {
                                $this->{'batch_' . $key}[$img] = $post_id;
                            }
                        }
                    }
                }

                if($post_id > 0 AND $post_type == 'recrm_estate')
                {
                    if($tax_id > 0)
                    {
                        wp_set_object_terms($post_id, (int) $tax_id, 'recrm_estate_tax');
                    }
                    else
                    {
                        $terms = wp_get_object_terms($post_id, 'recrm_estate_tax', array( 'fields' => 'ids' ));
                        if($terms AND !is_wp_error($terms))
                        {
                            wp_delete_object_term_relationships($post_id, 'recrm_estate_tax');
                        }
                    }
                }
            }

            // Save images
            clearstatcache();
            $this->post_gallery = array();
            $MultiCurl = new MultiCurl();
            foreach($this->batch_thumbnail AS $url => $entity_id)
            {
                $name = $this->tmpFilaNameUrl($url);
                $tmp  = $this->upload_tmp .'/'. $name;
                $MultiCurl->addDownload($url, $tmp);
                $this->post_gallery[$entity_id]['thumbnail'][$tmp] = $url;
            }
            foreach($this->batch_gallery_photos AS $url => $entity_id)
            {
                $name = $this->tmpFilaNameUrl($url);
                $tmp  = $this->upload_tmp .'/'. $name;
                $MultiCurl->addDownload($url, $tmp);
                $this->post_gallery[$entity_id]['gallery_photos'][$tmp] = $url;
            }
            foreach($this->batch_gallery_layouts AS $url => $entity_id)
            {
                $name = $this->tmpFilaNameUrl($url);
                $tmp  = $this->upload_tmp .'/'. $name;
                $MultiCurl->addDownload($url, $tmp);
                $this->post_gallery[$entity_id]['gallery_layouts'][$tmp] = $url;
            }
            foreach($this->batch_gallery_building AS $url => $entity_id)
            {
                $name = $this->tmpFilaNameUrl($url);
                $tmp  = $this->upload_tmp .'/'. $name;
                $MultiCurl->addDownload($url, $tmp);
                $this->post_gallery[$entity_id]['gallery_building'][$tmp] = $url;
            }
            $MultiCurl->start();

            foreach($this->post_gallery AS $entity_id => $entity_images)
            {
                foreach($entity_images AS $type => $images)
                {
                    $new = array();
                    foreach($images AS $tmp => $url)
                    {
                        preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png|bmp)/i', $url, $matches);
                        $file_arr = array(
                            'name'     => basename($matches[0]),
                            'tmp_name' => $tmp,
                        );
                        $img_id = media_handle_sideload($file_arr, $entity_id);
                        if(!is_wp_error($img_id)) {
                            $new[$img_id] = $url;
                        }
                        else
                        {
                            //echo $img_id->get_error_messages()."\n";
                        }
                        @unlink($tmp);
                    }

                    if($type == 'thumbnail' AND count($new))
                    {
                        $tmp = array_keys($new);
                        set_post_thumbnail($entity_id, $tmp[0]);
                    }
                    $arr1 = is_array($gallery[$entity_id][$type]) ? $gallery[$entity_id][$type] : array();
                    $arr2 = $new;
                    $gallery[$entity_id][$type] = $arr1 + $arr2; // keys is id
                }
            }

            foreach($gallery AS $post_id => $images)
            {
                update_post_meta($post_id, 'recrm_gallery_'.$post_key, serialize($images));
            }

            unset($this->batch);
            unset($this->batch_thumbnail);
            unset($this->batch_gallery_photos);
            unset($this->batch_gallery_building);
            unset($this->post_gallery);

        }
    }

    public function trash($Import)
    {
        global $wpdb;
        $types = array('agent', 'estate');
        foreach($types AS $type)
        {
            $data = $Import->get_data($type.'data');
            $ids  = is_array($data['ids']) ? $data['ids'] : array();
            if(count($ids))
            {
                $meta_key  = esc_sql('recrm_'.$post_key.'_id');
                $meta_val  = implode(',', array_map(function($v) { return "'".intval($v)."'"; }, $ids));
                $destroy   = $wpdb->get_col("SELECT `post_id` FROM `$wpdb->postmeta` WHERE `meta_key` = '".$meta_key."' AND `meta_value` NOT IN ($meta_val)");
                if(count($destroy))
                {
                    foreach($destroy AS $post_id)
                    {
                        wp_update_post(array(
                            'ID' => $post_id,
                            'post_status' => 'trash',
                        ));
                    }
                }
            }
        }
    }

    protected function tmpFilaNameUrl($url = '')
    {
        preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png|bmp)/i', $url, $matches);
        return md5($url.uniqid()).'.'.$matches[1];
    }
}
