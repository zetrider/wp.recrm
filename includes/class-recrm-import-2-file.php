<?php

/**
 * Import data from RcCrm Api v1 to file
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();

class Recrm_Import_2_File extends Recrm_Import {

    protected $upload_tmp;

    public function __construct($settings) {
        parent::__construct($settings);

        // Upload dir
        $upload_dir = wp_upload_dir();
        $upload_tmp = $upload_dir['basedir'].'/tmp';
        if(!file_exists($upload_tmp))
        {
            mkdir($upload_tmp, 0777);
        }
        $this->upload_tmp = $upload_tmp;
    }

    /**
     * Save data 2 temp files, for chunk data...
     * @param  string $type agent/estate
     * @param  mixed  $data | 'get'
     * @return mixed
     */
    private function temp_data($type = '', $data = array())
    {
        $name = $type.'_'.md5($this->api_key);
        if($data === 'get')
        {
            $res = array();
            $file = $this->upload_tmp.'/'.$name;
            if(file_exists($file))
            {
                $res = unserialize(base64_decode(file_get_contents($file)));
            }
        }
        else
        {
            $res  = $name;
            $data = base64_encode(serialize($data));
            file_put_contents($this->upload_tmp.'/'.$name, $data);
        }
        return $res;
    }

    /**
     * Get temp data from file
     * @param  string  $type  agent/estate
     * @param  integer $chunk chunk files
     * @return data
     */
    public function get_temp($type = '', $chunk = 30)
    {
        $data   = array();
        $estate = glob($this->upload_tmp . '/estate_*_'.md5($this->api_key)); // All estate temp files
        $agent  = glob($this->upload_tmp . '/agent_*_'.md5($this->api_key)); // All agent temp files
        $search = ${$type}; // For each
        $chunk  = (defined('RECRM_CHUNK_TEMP_FILES') AND RECRM_CHUNK_TEMP_FILES > 0) ? RECRM_CHUNK_TEMP_FILES : $chunk;

        if(count($search) <= 0)
        {
            $import_data = array();
            // depends from estate
            if($type == 'agent' AND count($estate) <= 0)
            {
                $import_data = $this->agents();
            }
            elseif($type == 'estate')
            {
                $import_data = $this->properties();
            }

            // Save new data to temp files
            if(count($import_data))
            {
                foreach($import_data AS $entity_id => $entity)
                {
                    $this->temp_data($type.'_'.$entity_id, $entity);
                }
                $this->temp_data($type.'data', array(
                    'ids' => array_keys($import_data),
                ));
            }

            // Search again
            $search = glob($this->upload_tmp . '/'.$type.'_*_'.md5($this->api_key));
        }

        if(count($search))
        {
            $files = $search;
            // Chunk data
            if($chunk > 0)
            {
                $tmp   = array_chunk($search, $chunk);
                $files = $tmp[0];
            }
            foreach($files AS $file)
            {
                $arr = explode('_', basename($file));
                $entity_id = $arr[1];
                $data[$entity_id] = $this->temp_data($type.'_'.$entity_id, 'get');
                @unlink($file);
            }
        }

        // For store
        return $data;
    }

    /**
     * Get ids, helper for found removed elements
     * @param  string  $type  agentsdata/estatedata
     * @return data
     */
    public function get_data($type = '')
    {
        $res = array();
        $search = glob($this->upload_tmp . '/'.$type.'_'.md5($this->api_key));
        if(count($search))
        {
            $res = $this->temp_data($type, 'get');
            foreach($search AS $file)
            {
                @unlink($file);
            }
        }
        return $res;
    }
}
