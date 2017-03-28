<?php

/*
Plugin Name: test
Plugin URI: http://test.com
Description:
Version: 999999
Author:
Author URI:
*/

class Test
{
    function __construct()
    {
        /**
         * Check if WooCommerce is active
         **/
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $this->initHooks();
        }

    }

    public function initHooks()
    {

        add_action('woocommerce_before_cart', array($this, 'testFunc'));
        add_filter('woocommerce_get_sections_product', array($this, 'wcslider_add_section'));
        add_filter('woocommerce_get_settings_product', array($this, 'wcslider_all_settings'), 10, 2);

        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_tab_demo', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_tab_demo', __CLASS__ . '::update_settings' );

        //add shiping method
        add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );
        add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );


    }


    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['tab_demo'] = __( 'Settings Demo Tab', 'woocommerce-settings-tab-demo' );
        return $settings_tabs;
    }

    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }


    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }

    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {


        $settings = array(
            'section_title' => array(
                'name'     => __( 'Section Title', 'woocommerce-settings-tab-demo' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_demo_section_title'
            ),
            'title' => array(
                'name' => __( 'Title', 'woocommerce-settings-tab-demo' ),
                'type' => 'text',
                'desc' => __( 'This is some helper text', 'woocommerce-settings-tab-demo' ),
                'id'   => 'wc_settings_tab_demo_title'
            ),
            'description' => array(
                'name' => __( 'Description', 'woocommerce-settings-tab-demo' ),
                'type' => 'textarea',
                'desc' => __( 'This is a paragraph describing the setting. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda.', 'woocommerce-settings-tab-demo' ),
                'id'   => 'wc_settings_tab_demo_description'
            ),
            array(
                'title'    => __( 'Enable Test', 'some-text-domain' ),
                'desc'     => __( 'Enable test options', 'some-text-domain' ),
                'id'       => 'test_option',
                'type'     => 'checkbox',
                'default'  => get_option('test_option'),
            ),
            array(
                'name' => __('Field 1', 'text-domain'),
                'id' => 'field_one',
                'type' => 'text',
                'default' => get_option('field_one'),
            ),
            array(
                'id'          => 'woocommerce_shop_page_display',
                'option_key'  => 'woocommerce_shop_page_display',
                'name'       => __( 'Shop Page Display', 'woocommerce' ),
                'desc' => __( 'This controls what is shown on the product archive.', 'woocommerce' ),
                'default'     => get_option('woocommerce_shop_page_display'),
                'type'        => 'select',
                'options'     => array(
                    ''              => __( 'Show products', 'woocommerce' ),
                    'subcategories' => __( 'Show categories &amp; subcategories', 'woocommerce' ),
                    'both'          => __( 'Show both', 'woocommerce' ),
                ),
            ),

            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_tab_demo_section_end'
            )
        );
        return apply_filters( 'wc_settings_tab_demo_settings', $settings );
    }


    /**
     * Create the section beneath the products tab
     **/
    function wcslider_add_section($sections)
    {
        $sections['wcslider'] = __('WC Slider', 'text-domain');
        return $sections;

    }

    /**
     * Add settings to the specific section we created before
     */
    function wcslider_all_settings($settings, $current_section)
    {
        /**
         * Check the current section is what we want
         **/
        if ($current_section == 'wcslider') {
            $settings_slider = array();
            // Add Title to the Settings
            $settings_slider[] = array('name' => __('WC Slider Settings', 'text-domain'), 'type' => 'title', 'desc' => __('The following options are used to configure WC Slider', 'text-domain'), 'id' => 'wcslider');
            // Add first checkbox option
            $settings_slider[] = array(
                'name' => __('Auto-insert into single product page', 'text-domain'),
                'desc_tip' => __('This will automatically insert your slider into the single product page', 'text-domain'),
                'id' => 'wcslider_auto_insert',
                'type' => 'checkbox',
                'css' => 'min-width:300px;',
                'desc' => __('Enable Auto-Insert', 'text-domain'),
            );
            // Add second text field option
            $settings_slider[] = array(
                'name' => __('Slider Title', 'text-domain'),
                'desc_tip' => __('This will add a title to your slider', 'text-domain'),
                'id' => 'wcslider_title',
                'type' => 'text',
                'desc' => __('Any title you want can be added to your slider with this option!', 'text-domain'),
            );

            $settings_slider[] = array('type' => 'sectionend', 'id' => 'wcslider');
            return $settings_slider;

            /**
             * If not, return the standard settings
             **/
        } else {
            return $settings;
        }
    }


    function testFunc()
    {
        $woocommerce = new WooCommerce();
        print_r($woocommerce->api_request_url('http://wc.loc/wp-json/wc/v2/products/tags/34?_jsonp=tagDetails'));
    }

    /**
     *   Init  new shipping method
     */
    function your_shipping_method_init() {
        if ( ! class_exists( 'WC_Your_Shipping_Method' ) ) {
            $shiping_method = new WC_Your_Shipping_Method();
        }
    }

    /**
     * Add new shipping method
     *
     * @param $methods
     * @return mixed
     */
    function add_your_shipping_method( $methods ) {
        $methods['your_shipping_method'] = 'WC_Your_Shipping_Method';
        return $methods;
    }
}

$test = new Test();