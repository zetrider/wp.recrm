<?php

/**
 * Post types
 *
 * @link       https://github.com/zetrider/wp.recrm
 * @since      1.0.0
 *
 * @package    ReCRM
 * @subpackage recrm/includes
 */

defined( 'ABSPATH' ) or die();

class Recrm_Post_Types {

    /**
     * Agents prefix
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $permalink_agents_structure    for rewrite
     */
    protected $permalink_agents_structure;

    /**
     * Estate prefix
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $permalink_estate_structure    for rewrite
     */
    protected $permalink_estate_structure;

    /**
     * Agents with taxonomy
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $agents_with_tax    for rewrite
     */
    protected $agents_with_tax;

    /**
     * Estate with taxonomy
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $estate_with_tax    for rewrite
     */
    protected $estate_with_tax;

    /**
     * Define the core functionality of the import.
     *
     * @param  array $settings Plugin settings
     * array[permalink_agents_structure] string
     * array[permalink_estate_structure] string
     * array[agents_with_tax] string on / off
     * array[estate_with_tax] string on / off
     *
     * @since    1.0.0
     */
    public function __construct( $settings = array() ) {

        $this->permalink_agents_structure = isset( $settings['permalink_agents_structure'] ) ? $settings['permalink_agents_structure'] : 'agents';
        $this->permalink_estate_structure = isset( $settings['permalink_estate_structure'] ) ? $settings['permalink_estate_structure'] : 'estate';

        $this->agents_with_tax = isset( $settings['agents_with_tax'] ) ? $settings['agents_with_tax'] : '';
        $this->estate_with_tax = isset( $settings['estate_with_tax'] ) ? $settings['estate_with_tax'] : '';

    }

    /**
     * Post type: estate
     *
     * @since    1.0.0
     */
    public function estate() {

        $slug = $this->permalink_estate_structure;
        if($this->estate_with_tax == 'on')
        {
            register_taxonomy('recrm_estate_tax', array('recrm_estate'), array(
                'label' => __('Estate  category', 'recrm'),
                'hierarchical' => true,
                'rewrite'      => array(
                    'slug' => $this->permalink_estate_structure,
                ),
            ));
            $slug = $this->permalink_estate_structure.'/%recrm_estate_tax%';
        }

        register_post_type('recrm_estate', array(
            'labels' => array(
                'name' => __('Estate', 'recrm'),
                'singular_name' => __('Estate', 'recrm'),
                'menu_name' => __('Estate', 'recrm'),
            ),
            'public' => true,
            'menu_position' => 4,
            'supports' => array(
                'title',
                'editor',
                //'author',
                'thumbnail',
                //'excerpt',
                'trackbacks',
                //'custom-fields',
                'comments',
                'revisions',
                //'page-attributes',
                //'post-formats',
            ),
            'taxonomies'  => array( 'recrm_estate_tax' ),
            'has_archive' => $this->permalink_estate_structure,
            'rewrite'   => array(
                'slug' => $slug,
                'with_front' => false,
            ),
        ) );
    }

    /**
     * Post type: Agents
     *
     * @since    1.0.0
     */
    public function agents() {

        $slug = $this->permalink_agents_structure;
        if($this->agents_with_tax == 'on')
        {
            register_taxonomy('recrm_agents_tax', array('recrm_agents'), array(
                'label' => __('Agent  category', 'recrm'),
                'hierarchical' => true,
                'rewrite'      => array(
                    'slug' => $this->permalink_agents_structure,
                ),
            ));
            $slug = $this->permalink_agents_structure.'/%recrm_agents_tax%';
        }

        $args = array(
            'labels' => array(
                'name' => __('Agents', 'recrm'),
                'singular_name' => __('Agent', 'recrm'),
                'menu_name' => __('Agents', 'recrm'),
            ),
            'public' => true,
            'menu_position' => 4,
            'supports' => array(
                'title',
                'editor',
                //'author',
                'thumbnail',
                //'excerpt',
                //'trackbacks',
                //'custom-fields',
                'comments',
                'revisions',
                'page-attributes',
                //'post-formats',
            ),
            'has_archive' => $this->permalink_agents_structure,
            'rewrite'   => array(
                'slug' => $slug,
                'with_front' => false,
            ),
        );
        register_post_type( 'recrm_agents', $args );
    }

    /**
     * Generate rewrite
     *
     * @since    1.0.0
     */
    public function generate_rewrite_rules($wp_rewrite) {

        $rules = array();

        if($this->estate_with_tax == 'on')
        {
            $rules = $this->recrm_rewrite_rules('recrm_estate_tax', 'recrm_estate', $this->permalink_estate_structure);
        }

        if($this->agents_with_tax == 'on')
        {
            $rules = $this->recrm_rewrite_rules('recrm_agents_tax', 'recrm_agents', $this->permalink_agents_structure);
        }

        if(count($rules))
        {
            $wp_rewrite->rules = $rules + $wp_rewrite->rules;
        }
    }

    /**
     * Recrm rewrite template
     *
     * @since    1.0.0
     */
    public function recrm_rewrite_rules($tax, $post_type, $structure) {
        $rules = array();
        $terms = get_terms( array(
            'taxonomy'   => $tax,
            'hide_empty' => false,
        ) );
        $rules[$structure . '/all/([^/]*)$'] = 'index.php?post_type='.$post_type.'&'.$post_type.'=$matches[1]&name=$matches[1]';
        foreach ($terms as $term) {
            $rules[$structure . '/' . $term->slug . '/([^/]*)$'] = 'index.php?post_type='.$post_type.'&'.$post_type.'=$matches[1]&name=$matches[1]';
        }
        return $rules;
    }

    /**
     * Post type link
     *
     * @since    1.0.0
     */
    public function post_type_link($permalink, $post) {

        if( $post->post_type == 'recrm_estate' and $this->estate_with_tax == 'on') {
            $permalink = $this->recrm_post_type_link($post, 'recrm_estate_tax', $this->permalink_estate_structure);
        }

        if( $post->post_type == 'recrm_agents' and $this->agents_with_tax == 'on') {
            $permalink = $this->recrm_post_type_link($post, 'recrm_agents_tax', $this->permalink_agents_structure);
        }

        return $permalink;
    }

    /**
     * Recrm post type link
     *
     * @since    1.0.0
     */
    public function recrm_post_type_link($post, $tax, $structure) {
        $resource_terms = get_the_terms( $post, $tax );
        $term_slug = 'all';
        if( ! empty( $resource_terms ) ) {
            foreach ( $resource_terms as $term ) {
                if( $term->slug == 'featured' ) {
                    continue;
                }
                $term_slug = $term->slug;
                break;
            }
        }
        $permalink = get_home_url() . '/' . $structure . '/' . $term_slug . '/' . $post->post_name;
        return $permalink;
    }

}
