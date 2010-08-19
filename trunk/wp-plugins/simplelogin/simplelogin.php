<?php

/*
Plugin Name: SimpleLogin
Plugin URI: http://webdesign.jaedub.com/wordpress-plugins/simplelogin-plugin
Description: An awesome plugin that allows users to login, register and retrieve passwords from any page on your blog.
Version: 2.0
Author: Jae Dub
Author URI: http://webdesign.jaedub.com
*/

global $wp_version;

if ($wp_version < '2.6') {
    if ( !defined('WP_CONTENT_DIR') )
        define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
    if ( !defined('WP_CONTENT_URL') )
        define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
    if ( !defined('WP_PLUGIN_DIR') )
        define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
    if ( !defined('WP_PLUGIN_URL') )
        define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
        
    require (WP_PLUGIN_DIR . '/simplelogin/includes/compat.php');
}
    
require_once (WP_PLUGIN_DIR . '/simplelogin/includes/wp-login-functions.php');


if (!class_exists('SimpleLogin')) {
    class SimpleLogin {

        var $version = '2.0';
        var $options = array();

        function SimpleLogin() {
            $this->__construct();
        }

        function __construct() {

            register_activation_hook ( __FILE__, array( &$this, 'Activate' ) );
            register_deactivation_hook ( __FILE__, array( &$this, 'Deactivate' ) );
            
            add_action('admin_menu', array(&$this, 'AddAdminPage'));
            add_action('init', array(&$this, 'Init'), 0);
            add_action('widgets_init', array(&$this, 'WidgetsInit'));
            add_action('wp_head', array(&$this, 'WPHead'));
            add_action('register_form', array(&$this, 'RegisterForm'));
            add_action('registration_errors', array(&$this, 'RegistrationErrors'));
            
            $this->LoadOptions();
        }

        function Activate() {
            $this->SetOption('version', $this->version);
            $this->SaveOptions();
        }

        function Deactivate() {
            if ($this->GetOption('uninstall')) {
                delete_option('simplelogin');
            }
        }

        # Sets up default options
        function InitOptions() {
            $this->options['uninstall']             = 0;
            $this->options['allow_user_pass']       = 0;
            $this->options['login_redirect']        = array('subscriber' => '', 'contributor' => '', 'author' => '', 'editor' => '', 'administrator' => '');
            $this->options['login_text']            = __('Log In', 'simpelogin');
            $this->options['register_text']         = __('Register', 'simpelogin');
            $this->options['register_msg']          = __('A password will be e-mailed to you.', 'simpelogin');
            $this->options['register_complete']     = __('Registration complete. Please check your e-mail.', 'simpelogin');
            $this->options['password_text']         = __('Lost Password', 'simpelogin');
            $this->options['password_msg']          = __('Please enter your username or e-mail address. You will receive a new password via e-mail.', 'simpelogin');
            $this->options['show_gravatar']         = 1;
            $this->options['gravatar_size']         = 50;
            $this->options['dashboard_link']        = array('subscriber' => 1, 'contributor' => 1, 'author' => 1, 'editor' => 1, 'administrator' => 1);
            $this->options['dashboard_url']         = '';
            $this->options['profile_link']          = array('subscriber' => 1, 'contributor' => 1, 'author' => 1, 'editor' => 1, 'administrator' => 1);
            $this->options['profile_url']           = '';
            $this->options['custom_mail_from']      = get_option('admin_email');
            $this->options['custom_mail_from_name'] = get_option('blogname');
        }

        function LoadOptions() {

            $this->InitOptions();

            $storedoptions = get_option( 'simplelogin' );
            if ( $storedoptions && is_array( $storedoptions ) ) {
                foreach ( $storedoptions as $key => $value ) {
                    $this->options[$key] = $value;
                }
            } else update_option( 'simplelogin', $this->options );
        }

        function GetOption( $key ) {
            if ( array_key_exists( $key, $this->options ) ) {
                return $this->options[$key];
            } else return null;
        }

        function SetOption( $key, $value ) {
            $this->options[$key] = $value;
        }

        function SaveOptions() {
            $oldvalue = get_option( 'simplelogin' );
            if( $oldvalue == $this->options ) {
                return true;
            } else return update_option( 'simplelogin', $this->options );
        }

        function AddAdminPage(){
            add_submenu_page('options-general.php', __('SimpleLogin', 'simpelogin'), __('SimpleLogin', 'simpelogin'), 'manage_options', __('SimpleLogin', 'simpelogin'), array(&$this, 'AdminPage'));
        }

        function AdminPage(){
            require (WP_PLUGIN_DIR . '/simplelogin/includes/admin-page.php');
        }
        
        function Init() {
            global $pagenow, $login_errors;
            
            if ($pagenow == 'wp-login.php' && isset($_GET['loggedout'])) {
                $redirect_to = get_bloginfo('siteurl') . '?loggedout=true';
                wp_redirect($redirect_to);
                exit;
            } elseif ($pagenow == 'wp-login.php') {
                return;
            }
            
            $login_errors = new WP_Error();

            require (WP_PLUGIN_DIR . '/simplelogin/includes/wp-login-actions.php');
        }
        
        function WidgetsInit() {
            if ( !function_exists('register_sidebar_widget') ) return;
            register_sidebar_widget('SimpleLogin', array(&$this, 'DoSimpleLogin'));
        }
        
        function DoSimpleLogin($args = '') {
            global $user_ID, $current_user, $login_errors, $user_level;
            
            get_currentuserinfo();
            
            extract ($args);

            if ($user_ID != '') {
                require_once (ABSPATH . '/wp-admin/includes/upgrade.php');
                $user_role = translate_level_to_role($user_level);
                $dashboard_link = $this->GetOption('dashboard_link');
                $profile_link = $this->GetOption('profile_link');
                echo $before_widget . $before_title . __('Welcome', 'simpelogin') . ', ' . $current_user->display_name . $after_title . "\n";
                if ($this->GetOption('show_gravatar') == true) :
                    echo '<div class="simplelogin-avatar">' . get_avatar( $user_ID, $size = $this->GetOption('gravatar_size') ) . '</div>' . "\n";
                endif;
                do_action('simplelogin_avatar', $current_user);
                echo '<ul class="simplelogin-links">' . "\n";
                if ($dashboard_link[$user_role] == true) :
                    $dashboard_url = $this->GetOption('dashboard_url');
                    $dashboard_url = (!empty($dashboard_url)) ? $dashboard_url : admin_url();
                    echo '<li><a href="' . $dashboard_url . '">' . __('Dashboard', 'simpelogin') . '</a></li>' . "\n";
                endif;
                if ($profile_link[$user_role] == true) :
                    $profile_url = $this->GetOption('profile_url');
                    $profile_url = (!empty($profile_url)) ? $profile_url : admin_url('profile.php');
                echo '<li><a href="' . $profile_url . '">' . __('Profile', 'simpelogin') . '</a></li>' . "\n";
                endif;
                do_action('simplelogin_custom_links', $user_role);
                echo '<li><a href="' . wp_nonce_url( simplelogin_url(array('action' => 'logout', 'redirect_to' => simplelogin_url())), 'log-out' ) . '">' . __('Logout', 'simpelogin') . '</a></li>' . "\n";
                echo '</ul>' . "\n";
            } else {
                require (WP_PLUGIN_DIR . '/simplelogin/includes/wp-login-forms.php');
            }
            echo $after_widget;
        }
        
        function WPHead() {
            echo '<!-- SimpleLogin Version ' . $this->version . ' -->' . "\n";
            echo '<link rel="stylesheet" type="text/css" href="' . WP_PLUGIN_URL . '/simplelogin/simplelogin.css">' . "\n";
            echo '<!-- SimpleLogin Version ' . $this->version . ' -->' . "\n";
        }
        
        function RegisterForm() {
            if ($this->GetOption('allow_user_pass')) {
        ?>
        <p><label><?php _e('Password:', 'simplelogin');?> <br />
        <input autocomplete="off" name="pass1" id="pass1" value="<?php echo $pass1; ?>" type="password" tabindex="40" /></label><br />
        <label><?php _e('Confirm Password:', 'simplelogin');?> <br />
        <input autocomplete="off" name="pass2" id="pass2" value="<?php echo $pass2; ?>" type="password" tabindex="41" /></label>
        <?php
            }
        }
        
        function RegistrationErrors($errors) {
            if ( $this->GetOption('allow_user_pass') ){
                if(empty($_POST['pass1']) || $_POST['pass1'] == '' || empty($_POST['pass2']) || $_POST['pass2'] == ''){
                    $errors->add('empty_password', __('<strong>ERROR</strong>: Please enter a Password.', 'simplelogin'));
                }elseif($_POST['pass1'] !== $_POST['pass2']){
                    $errors->add('password_mismatch', __('<strong>ERROR</strong>: Your Password does not match.', 'simplelogin'));
                }elseif(strlen($_POST['pass1'])<6){
                    $errors->add('password_length', __('<strong>ERROR</strong>: Your Password must be at least 6 characters in length.', 'simplelogin'));
                }
            }
            return $errors;
        }
        
        function MailFrom() {
            return $this->GetOption('custom_mail_from');
        }
        
        function MailFromName() {
            return $this->GetOption('custom_mail_from_name');
        }
    }
}

//instantiate the class
if (class_exists('SimpleLogin')) {
    $SimpleLogin = new SimpleLogin();
    
    function simplelogin($args = '') {
        global $SimpleLogin;
        $defaults = array('before_widget' => '<li>', 'after_widget' => '</li>', 'before_title' => '<h2>', 'after_title' => '</h2>');
        $r = wp_parse_args( $args, $defaults );
        $SimpleLogin->DoSimpleLogin($r);
    }
}

?>
