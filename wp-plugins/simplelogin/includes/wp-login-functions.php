<?php

/*
Last Update: 2.0
*/

if (!function_exists('simplelogin_url')) :
function simplelogin_url($args = array(), $strict = false) {
    $login_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

    if ($_SERVER["SERVER_PORT"] != "80") {
        $login_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $login_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }

    $keys = array('action', 'checkemail', 'error', 'loggedout', 'registered', 'redirect_to', 'updated');
    $login_url = remove_query_arg($keys, $login_url);
    
    if ($strict === true)
        $login_url = get_bloginfo('siteurl') . '/';

    if (!empty($args)) {
        foreach ($args as $key => $value)
            $login_url = add_query_arg($key, $value, $login_url);
    }

    return $login_url;
}
endif;

if (!function_exists('simplelogin_header')) :
function simplelogin_header($title, $message = '', $wp_error = '', $args = '') {
    global $error;
    
    extract ($args);

    if ( empty($wp_error) )
        $wp_error = new WP_Error();

    if ( !empty( $error ) ) {
        $wp_error->add('error', $error);
        unset($error);
    }

    echo $before_widget . $before_title . __($title, 'simplelogin') . $after_title . "\n";
    
    echo '<div id="login">';
    
    if ( !empty( $message ) ) echo apply_filters('login_message', $message) . "\n";
    
    if ( $wp_error->get_error_code() ) {
        $errors = '';
        $messages = '';
        foreach ( $wp_error->get_error_codes() as $code ) {
            $severity = $wp_error->get_error_data($code);
            foreach ( $wp_error->get_error_messages($code) as $error ) {
                if ( 'message' == $severity )
                    $messages .= '    ' . $error . "<br />\n";
                else
                    $errors .= '    ' . $error . "<br />\n";
            }
        }
        if ( !empty($errors) )
            echo '<p class="error">' . apply_filters('login_errors', $errors) . "</p>\n";
        if ( !empty($messages) )
            echo '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
    }
}
endif;

if (!function_exists('simplelogin_footer')) :
function simplelogin_footer() {
    $_GET['action'] = isset($_GET['action']) ? $_GET['action'] : 'login';
    echo '<ul class="simplelogin-links">' . "\n";
    if (in_array($_GET['action'], array('register', 'lostpassword')))
        echo '<li><a href="' . simplelogin_url(array('action' => 'login')) . '">' . __('Log in', 'simplelogin') . '</a></li>' . "\n";
    if (get_option('users_can_register') && $_GET['action'] != 'register')
        echo '<li><a href="' . simplelogin_url(array('action' => 'register')) . '">' . __('Register', 'simplelogin') . '</a></li>' . "\n";
    if ($_GET['action'] != 'lostpassword')
        echo '<li><a href="' . simplelogin_url(array('action' => 'lostpassword')) . '" title="' . __('Password Lost and Found', 'simplelogin') . '">' . __('Lost your password?', 'simplelogin') . '</a></li>' . "\n";
    echo '</ul>' . "\n";
    echo '</div>' . "\n";
}
endif;

if (!function_exists('retrieve_password')) :
function retrieve_password() {
    global $wpdb, $SimpleLogin;

    $errors = new WP_Error();

    if ( empty( $_POST['user_login'] ) && empty( $_POST['user_email'] ) )
        $errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.', 'simplelogin'));

    if ( strpos($_POST['user_login'], '@') ) {
        $user_data = get_user_by_email(trim($_POST['user_login']));
        if ( empty($user_data) )
            $errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.', 'simplelogin'));
    } else {
        $login = trim($_POST['user_login']);
        $user_data = get_userdatabylogin($login);
    }

    do_action('lostpassword_post');

    if ( $errors->get_error_code() )
        return $errors;

    if ( !$user_data ) {
        $errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.', 'simplelogin'));
        return $errors;
    }

    // redefining user_login ensures we return the right case in the email
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;

    do_action('retreive_password', $user_login);  // Misspelled and deprecated
    do_action('retrieve_password', $user_login);

    $allow = apply_filters('allow_password_reset', true, $user_data->ID);

    if ( ! $allow )
        return new WP_Error('no_password_reset', __('Password reset is not allowed for this user', 'simplelogin'));
    else if ( is_wp_error($allow) )
        return $allow;

    $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
    if ( empty($key) ) {
        // Generate something random for a key...
        $key = wp_generate_password(20, false);
        do_action('retrieve_password_key', $user_login, $key);
        // Now insert the new md5 key into the db
        $wpdb->query($wpdb->prepare("UPDATE $wpdb->users SET user_activation_key = %s WHERE user_login = %s", $key, $user_login));
    }
    
    $from = $SimpleLogin->MailFrom();
    $from_name = $SimpleLogin->MailFromName();
    $subject = $SimpleLogin->GetOption('custom_user_pass_request_mail_subject');
    $message = $SimpleLogin->GetOption('custom_user_pass_request_mail_message');
    $replace_this = array('/%blogname%/', '/%siteurl%/', '/%reseturl%/', '/%user_login%/', '/%user_email%/', '/%user_ip%/');
    $replace_with = array(get_option('blogname'), get_option('siteurl'), simplelogin_url(array('action' => 'rp', 'key' => $key)), $user->user_login, $user->user_email, $_SERVER['REMOTE_ADDR']);

    if (!empty($from))
        add_filter('wp_mail_from', array(&$SimpleLogin, 'MailFrom'));
    if (!empty($from_name))
        add_filter('wp_mail_from_name', array(&$SimpleLogin, 'MailFromName'));
    if (empty($subject))
        $subject = sprintf(__('[%s] Password Reset', 'simplelogin'), get_option('blogname'));
    else
        $subject = preg_replace($replace_this, $replace_with, $subject);
    if (empty($message)) {
        $message = __('Someone has asked to reset the password for the following site and username.', 'simplelogin') . "\r\n\r\n";
        $message .= get_option('siteurl') . "\r\n\r\n";
        $message .= sprintf(__('Username: %s', 'simplelogin'), $user_login) . "\r\n\r\n";
        $message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.', 'simplelogin') . "\r\n\r\n";
        $message .= simplelogin_url(array('action' => 'rp', 'key' => $key)) . "\r\n";
    } else {
        $message = preg_replace($replace_this, $replace_with, $message);
    }

    if ( !wp_mail($user_email, $subject, $message) )
        die('<p>' . __('The e-mail could not be sent.', 'simplelogin') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...', 'simplelogin') . '</p>');

    return true;
}
endif;

if (!function_exists('reset_password')) :
function reset_password($key) {
    global $wpdb, $SimpleLogin;

    $key = preg_replace('/[^a-z0-9]/i', '', $key);

    if ( empty( $key ) )
        return new WP_Error('invalid_key', __('Invalid key', 'simplelogin'));

    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s", $key));
    if ( empty( $user ) )
        return new WP_Error('invalid_key', __('Invalid key', 'simplelogin'));

    do_action('password_reset', $user);

    // Generate something random for a password...
    $new_pass = wp_generate_password();
    wp_set_password($new_pass, $user->ID);
    
    $from = $SimpleLogin->MailFrom();
    $from_name = $SimpleLogin->MailFromName();
    $subject = $SimpleLogin->GetOption('custom_user_pass_changed_mail_subject');
    $message = $SimpleLogin->GetOption('custom_user_pass_changed_mail_message');
    $replace_this = array('/%blogname%/', '/%siteurl%/', '/%user_login%/', '/%user_email%/', '/%user_pass%/', '/%user_ip%/');
    $replace_with = array(get_option('blogname'), get_option('siteurl'), $user->user_login, $user->user_email, $new_pass, $_SERVER['REMOTE_ADDR']);

    if (!empty($from))
        add_filter('wp_mail_from', array(&$SimpleLogin, 'MailFrom'));
    if (!empty($from_name))
        add_filter('wp_mail_from_name', array(&$SimpleLogin, 'MailFromName'));
    if (empty($subject))
        $subject = sprintf(__('[%s] Your new password', 'simplelogin'), get_option('blogname'));
    else
        $subject = preg_replace($replace_this, $replace_with, $subject);
    if (empty($message)) {
        $message  = sprintf(__('Username: %s', 'simplelogin'), $user->user_login) . "\r\n";
        $message .= sprintf(__('Password: %s', 'simplelogin'), $new_pass) . "\r\n";
        $message .= simplelogin_url(array('action' => 'login')) . "\r\n";
    } else {
        $message = preg_replace($replace_this, $replace_with, $message);
    }

    if (  !wp_mail($user->user_email, $subject, $message) )
        die('<p>' . __('The e-mail could not be sent.', 'simplelogin') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...', 'simplelogin') . '</p>');

    wp_password_change_notification($user);

    return true;
}
endif;

if ( !function_exists('wp_password_change_notification') ) :
function wp_password_change_notification(&$user) {
    global $SimpleLogin;
    if ( $SimpleLogin->GetOption('disable_admin_pass_changed_mail') )
        return;

    $from = $SimpleLogin->MailFrom();
    $from_name = $SimpleLogin->MailFromName();
    $subject = $SimpleLogin->GetOption('custom_admin_pass_changed_mail_subject');
    $message = $SimpleLogin->GetOption('custom_admin_pass_changed_mail_message');
    $replace_this = array('/%blogname%/', '/%siteurl%/', '/%user_login%/', '/%user_email%/', '/%user_ip%/');
    $replace_with = array(get_option('blogname'), get_option('siteurl'), $user->user_login, $user->user_email, $_SERVER['REMOTE_ADDR']);

    if (!empty($from))
        add_filter('wp_mail_from', array(&$SimpleLogin, 'MailFrom'));
    if (!empty($from_name))
        add_filter('wp_mail_from_name', array(&$SimpleLogin, 'MailFromName'));
    if (empty($subject))
        $subject = sprintf(__('[%s] Password Lost/Changed'), get_option('blogname'));
    else
        $subject = preg_replace($replace_this, $replace_with, $subject);
    if (empty($message)) {
        $message = sprintf(__('Password Lost and Changed for user: %s', 'simplelogin'), $user->user_login) . "\r\n";
    } else {
        $message = preg_replace($replace_this, $replace_with, $message);
    }
    if ( $user->user_email != get_option('admin_email') ) {
        wp_mail(get_option('admin_email'), $subject, $message);
    }
}
endif;

if ( !function_exists('wp_new_user_notification') ) :
function wp_new_user_notification($user_id, $plaintext_pass = '') {
    global $wpdb, $SimpleLogin;
    
    $user = new WP_User($user_id);
    
    $ref = explode('?', $_SERVER['HTTP_REFERER']);
    $ref = $ref[0];
    $admin = trailingslashit(get_option('siteurl')) . 'wp-admin/users.php';
    if ($SimpleLogin->GetOption('allow_user_pass') && $_POST['pass1'])
        $plaintext_pass = $wpdb->prepare($_POST['pass1']);
    elseif ($ref == $admin && $_POST['pass1'] == $_POST['pass2'])
        $plaintext_pass = $wpdb->prepare($_POST['pass1']);
    else
        $plaintext_pass = wp_generate_password();

    wp_set_password($plaintext_pass, $user_id);
    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);
    
    $from = $SimpleLogin->MailFrom();
    $from_name = $SimpleLogin->MailFromName();
    if (!empty($from))
        add_filter('wp_mail_from', array(&$SimpleLogin, 'MailFrom'));
    if (!empty($from_name))
        add_filter('wp_mail_from_name', array(&$SimpleLogin, 'MailFromName'));
    
    if (!$SimpleLogin->GetOption('disable_admin_reg_mail')) :
    $subject = $SimpleLogin->GetOption('custom_admin_reg_mail_subject');
    $message = $SimpleLogin->GetOption('custom_admin_reg_mail_message');
    $replace_this = array('/%blogname%/', '/%siteurl%/', '/%user_login%/', '/%user_email%/', '/%user_ip%/');
    $replace_with = array(get_option('blogname'), get_option('siteurl'), $user->user_login, $user->user_email, $_SERVER['REMOTE_ADDR']);
    
    if (empty($subject))
        $subject = sprintf(__('[%s] New User Registration', 'simplelogin'), get_option('blogname'));
    else
        $subject = preg_replace($replace_this, $replace_with, $subject);
    if (empty($message)) {
        $message  = sprintf(__('New user registration on your blog %s:', 'simplelogin'), get_option('blogname')) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s', 'simplelogin'), $user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s', 'simplelogin'), $user_email) . "\r\n";
    } else {
        $message = preg_replace($replace_this, $replace_with, $message);
    }

    @wp_mail(get_option('admin_email'), $subject, $message);
    endif;

    if ( empty($plaintext_pass) )
        return;

    if (!$SimpleLogin->GetOption('disable_user_reg_mail')) :
    $subject = $SimpleLogin->GetOption('custom_user_reg_mail_subject');
    $message = $SimpleLogin->GetOption('custom_user_reg_mail_message');
    $replace_this = array('/%blogname%/', '/%siteurl%/', '/%user_login%/', '/%user_email%/', '/%user_pass%/', '/%user_ip%/');
    $replace_with = array(get_option('blogname'), get_option('siteurl'), $user->user_login, $user->user_email, $plaintext_pass, $_SERVER['REMOTE_ADDR']);

    if (empty($subject))
        $subject = sprintf(__('[%s] Your username and password', 'simplelogin'), get_option('blogname'));
    else
        $subject = preg_replace($replace_this, $replace_with, $subject);
    if (empty($message)) {
        $message  = sprintf(__('Username: %s', 'simplelogin'), $user_login) . "\r\n";
        $message .= sprintf(__('Password: %s', 'simplelogin'), $plaintext_pass) . "\r\n";
        $message .= simplelogin_url(array('action' => 'login')) . "\r\n";
    } else {
        $message = preg_replace($replace_this, $replace_with, $message);
    }

    wp_mail($user_email, $subject, $message);
    endif;

}
endif;

if (!function_exists('register_new_user')) :
function register_new_user($user_login, $user_email) {
 $errors = new WP_Error();

    $user_login = sanitize_user( $user_login );
    $user_email = apply_filters( 'user_registration_email', $user_email );

    // Check the username
    if ( $user_login == '' )
        $errors->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.', 'simplelogin'));
    elseif ( !validate_username( $user_login ) ) {
        $errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.', 'simplelogin'));
        $user_login = '';
    } elseif ( username_exists( $user_login ) )
        $errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.', 'simplelogin'));

    // Check the e-mail address
    if ($user_email == '') {
        $errors->add('empty_email', __('<strong>ERROR</strong>: Please enter your e-mail address.', 'simplelogin'));
    } elseif ( !is_email( $user_email ) ) {
        $errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'simplelogin'));
        $user_email = '';
    } elseif ( email_exists( $user_email ) )
        $errors->add('email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.', 'simplelogin'));

    do_action('register_post', $user_login, $user_email, $errors);

    $errors = apply_filters( 'registration_errors', $errors );

    if ( $errors->get_error_code() )
        return $errors;

    $user_pass = wp_generate_password();
    
    $user_id = wp_create_user( $user_login, $user_pass, $user_email );
    if ( !$user_id ) {
        $errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'simplelogin'), get_option('admin_email')));
        return $errors;
    }

    wp_new_user_notification($user_id, $user_pass);

    return $user_id;
}
endif;

?>
