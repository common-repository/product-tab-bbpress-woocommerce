<?php 

if (!defined('ABSPATH')) {
    return;
}
global $woocommerce;
$global_get  = get_option('wc_bb_settings_tab_title');
$global_set  = !empty($global_get) ? $global_get : update_option('wc_bb_settings_tab_title', 'Comments','yes');
$global_get  = get_option('wc_bb_settings_tab_priority');
$global_set  = !empty($global_get) ? $global_get : update_option('wc_bb_settings_tab_priority', '50','yes');
$global_get  = get_option('wc_bb_settings_tab_post_count');
$global_set  = !empty($global_get) ? $global_get : update_option('wc_bb_settings_tab_post_count', '10','yes');
$global_get  = get_option('wc_bb_settings_tab_show_reply_link');
$global_set  = !empty($global_get) ? $global_get : update_option('wc_bb_settings_tab_show_reply_link', '1','yes');
$global_get  = get_option('wc_bb_settings_tab_view_topic');
$global_set  = !empty($global_get) ? $global_get : update_option('wc_bb_settings_tab_view_topic', 'yes','yes');
$global_get  = get_option('wc_bb_settings_tab_view_post_count');
$global_set  = !empty($global_get) ? $global_get : update_option('wc_bb_settings_tab_view_post_count', 'yes','yes');
$global_get  = get_option('wc_bb_settings_tab_view_post_images');
$global_set  = !empty($global_get) ? $global_get : update_option('wc_bb_settings_tab_view_post_images', 'no','yes');

//wp_die($global_set);
class EH_BB_TAB_SETTINGS_CLASS {
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_bb_settings_tab', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_bb_settings_tab', __CLASS__ . '::update_settings' );
    }
    
    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['bb_settings_tab'] = __( 'bbPress Tab', 'eh_bb_product_tab' );
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
      
      include('market.php');
      
        $settings = array(
            'section_title1' => array(
                'name'     => __( 'bbPress Tab Settings', 'eh_bb_product_tab' ),
                'type'     => 'title',
                'desc'     => 'The following options are used to configure bbPress Tab. You have to choose the forum from individual product admin page.',
                'id'       => 'wc_bb_settings_tab_section_title1'
            ),
         //   'global_settings' => array(
         //       'name' => __( 'Global Settings', 'eh_bb_product_tab' ),
         //       'type' => 'checkbox',
                
        //        'desc' => __('Enable','eh_bb_product_tab'),
          //      'id'   => 'wc_bb_settings_tab_global_settings'
         //   ),
            'tab_title' => array(
                'name' => __( 'Tab Title', 'eh_bb_product_tab' ),
                'type' => 'text',
                    'css'     => 'min-width:195px;',
               
                'desc_tip' => __( 'Tiltle of the product page tab.', 'eh_bb_product_tab' ),
                'id'   => 'wc_bb_settings_tab_title',
                'custom_attributes' => array(
		 'required'      => true,
                 'step' 	=> 'any',
                    ) 
            ),
            'tab_priority' => array(
                'name' => __( 'Tab priority', 'eh_bb_product_tab' ),
                'type' => 'number',
                    'css'     => 'min-width:195px;',
              
                'desc_tip' => __( 'Tab Priority to be shown in Fornt Page.', 'eh_bb_product_tab' ),
                'id'   => 'wc_bb_settings_tab_priority',
                'custom_attributes' => array(
		 'required'      => true,
               		'step' 	=> 'any',
				'min'	=> '1'
                    ) 
            ),
            'post_count' => array(
                'name' => __( 'Post Count', 'eh_bb_product_tab' ),
                'type' => 'number',
               'desc_tip' => __( 'Number of posts to be shown on the product page', 'eh_bb_product_tab' ),
                  'css'     => 'min-width:195px;',
                'id'   => 'wc_bb_settings_tab_post_count',
                'custom_attributes' => array(
                'required'      => true,
                   		'step' 	=> 'any',
				'min'	=> '1'
                    ) 
            ),
            'sub_topic' => array(
                'name' => __( 'Global Settings:', 'eh_bb_product_tab' ),
                'type' => 'title',
                'id'   => 'wc_bb_settings_tab_sub_topic'
            ),
           'reply_link' => array(
                        'name'    => __( 'Show Reply Link', 'eh_bb_product_tab' ),
                        'desc'    => __( 'This controls the reply option on the product page', 'eh_bb_product_tab' ),
                        'id'      => 'wc_bb_settings_tab_show_reply_link',
                        'css'     => 'min-width:150px;',
                        'std'     => '0', // WooCommerce < 2.0
                        'default' => '0', // WooCommerce >= 2.0
                        'type'    => 'select',
                        'options' => array(
                                '0'        => __( 'Disable', 'eh_bb_product_tab' ),
                                '1'       => __( 'Enable for All', 'eh_bb_product_tab' ),
                                '2'  => __( 'Enable for Logged In Users', 'eh_bb_product_tab' ),
                        ),
                        'desc_tip' =>  true,
          ),
            'view_topic' => array(
                'name' => __( 'Show - View Topic', 'eh_bb_product_tab' ),
                'type' => 'checkbox',
                'desc' => __('Enable','eh_bb_product_tab'),
                'id'   => 'wc_bb_settings_tab_view_topic'
            ),

           'view_new_topic' => array(
                'name' => __( 'Generic Settings:', 'eh_bb_product_tab' ),
                'type' => 'title',
                'desc' => __('The following options are used to configure bbPress Tab on the product page. ','eh_bb_product_tab'),
                'id'   => 'wc_bb_settings_tab_view_new_topic'
            ),
            'view_post_count' => array(
                'name' => __( 'Show Post Count With Tab', 'eh_bb_product_tab' ),
                'type' => 'checkbox',
                'desc' => __('Enable','eh_bb_product_tab'),
                'id'   => 'wc_bb_settings_tab_view_post_count'
            ),
             'view_post_images' => array(
                'name' => __( 'Do not show images inline', 'eh_bb_product_tab' ),
                'type' => 'checkbox',
                'desc' => __('Enable','eh_bb_product_tab'),
                'id'   => 'wc_bb_settings_tab_view_post_images'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_bb_settings_tab_section_end'
            )
            
            
        );
        return apply_filters( 'wc_bb_settings_tab_settings', $settings );
    }
}
EH_BB_TAB_SETTINGS_CLASS::init();