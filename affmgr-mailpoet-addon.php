<?php
/**
 * Plugin Name: Affiliates Manager MailPoet Integration
 * Plugin URI: https://wpaffiliatemanager.com/sign-affiliates-to-mailpoet-list/
 * Description: This Addon allows you to signup your affiliates to MailPoet newsletter list
 * Version: 1.0
 * Author: wp.insider
 * Author URI: https://wpaffiliatemanager.com
 * Requires at least: 3.0
 */

if (!defined('ABSPATH')){
    exit;
}

if (!class_exists('AFFMGR_MAILPOET_ADDON')) {

    class AFFMGR_MAILPOET_ADDON {

        var $version = '1.0';
        var $db_version = '1.0';
        var $plugin_url;
        var $plugin_path;

        function __construct() {
            $this->define_constants();
            $this->includes();
            $this->loader_operations();
            //Handle any db install and upgrade task
            add_action('init', array(&$this, 'plugin_init'), 0);
            add_action('wpam_after_main_admin_menu', array(&$this, 'mailpoet_do_admin_menu'));
            add_action('wpam_front_end_registration_form_submitted', array(&$this, 'do_mailpoet_signup'), 10, 2);
        }

        function define_constants() {
            define('AFFMGR_MAILPOET_ADDON_VERSION', $this->version);
            define('AFFMGR_MAILPOET_ADDON_URL', $this->plugin_url());
            define('AFFMGR_MAILPOET_ADDON_PATH', $this->plugin_path());
        }

        function includes() {
            include_once('affmgr-mailpoet-settings.php');
        }

        function loader_operations() {
            //add_action('plugins_loaded', array(&$this, 'plugins_loaded_handler')); //plugins loaded hook		
        }

        function plugin_init() {//Gets run with WP Init is fired
        }

        function mailpoet_do_admin_menu($menu_parent_slug) {
            add_submenu_page($menu_parent_slug, __("MailPoet", 'wpam'), __("MailPoet", 'wpam'), 'manage_options', 'wpam-mailpoet', 'wpam_mailpoet_admin_interface');
        }

        function do_mailpoet_signup($model, $request) {

            $first_name = strip_tags($request['_firstName']);
            $last_name = strip_tags($request['_lastName']);
            $email = strip_tags($request['_email']);
            $mailpoet_list_id = get_option('wpam_mailpoet_list_id'); //List ID where an affiliate will be signed up to. 
            WPAM_Logger::log_debug("Mailpoet/Wysija newsletter addon. After registration hook. Debug data: " . $mailpoet_list_id . "|" . $email . "|" . $first_name . "|" . $last_name);
            //in this array firstname and lastname are optional
            $userData = array(
                'email' => $email,
                'firstname' => $first_name,
                'lastname' => $last_name
            );
            $data = array(
                'user' => $userData,
                'user_list' => array('list_ids' => array($mailpoet_list_id))
            );

            $userHelper = &WYSIJA::get('user', 'helper');
            $userHelper->addSubscriber($data);
            WPAM_Logger::log_debug("MailPoet/Wysija signup complete!");
        }

        function plugin_url() {
            if ($this->plugin_url)
                return $this->plugin_url;
            return $this->plugin_url = plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__));
        }

        function plugin_path() {
            if ($this->plugin_path)
                return $this->plugin_path;
            return $this->plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
        }

    }

    //End of plugin class
}//End of class not exists check

$GLOBALS['AFFMGR_MAILPOET_ADDON'] = new AFFMGR_MAILPOET_ADDON();
