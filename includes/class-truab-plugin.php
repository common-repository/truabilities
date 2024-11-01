<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('TRUAB_Plugin')):

    class TRUAB_Plugin {
        // Initilization Constructor

        public function __construct() {
            add_action('plugins_loaded', [$this, 'include_vendor']);
            add_action('wp_enqueue_scripts', [$this, 'ta_placeScriptOnSite'], 9999999);
        }

        // For Initilization CMB Module
        public function include_vendor() {
            include TRUAB_PATH . 'includes/admin/truabOptions.php';
        }

        // To output code
        public function ta_placeScriptOnSite() {
            $getAuthToken = get_option('truabLicense');
            if (isset($getAuthToken)) {
                echo $this->_get_Ada_script($getAuthToken);
            }
        }

        // Get Script
        protected function _get_Ada_script($getAuthToken) {
            $getAuthToken = get_option('truabLicense');
            if ($getAuthToken != '') {

                $scriptURL = 'https://app.truabilities.com/release/truabilities.js';
                $finalsScriptUrl = add_query_arg( 'widgetkey', $getAuthToken, $scriptURL );

                wp_enqueue_script('truab-widget-script', $finalsScriptUrl, array(), null, true );
            }
        }

    }

endif;
new TRUAB_Plugin();