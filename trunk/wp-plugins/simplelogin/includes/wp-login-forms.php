<?php

/*
Last Update: 2.0
*/

$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) :

case 'lostpassword' :
case 'retrievepassword' :
    do_action('lost_password');
    simplelogin_header(__($this->GetOption('password_text'), 'simplelogin'), '<p class="message">' . __($this->GetOption('password_msg'), 'simplelogin') . '</p>', $login_errors, $args);

    $user_login = isset($_POST['user_login']) ? stripslashes($_POST['user_login']) : '';
?>

<form name="lostpasswordform" id="lostpasswordform" action="<?php echo simplelogin_url(array('action' => 'lostpassword')) ?>" method="post">
    <p>
        <label><?php _e('Username or E-mail:', 'simplelogin') ?><br />
        <input type="text" name="user_login" id="user_login" class="input" value="<?php echo attribute_escape($user_login); ?>" size="20" tabindex="10" /></label>
    </p>
<?php do_action('lostpassword_form'); ?>
    <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Get New Password', 'simplelogin'); ?>" tabindex="100" /></p>
</form>

<?php
simplelogin_footer();
break;

case 'register' :
    $user_login = isset($_POST['user_login']) ? $_POST['user_login'] : '';
    $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
    simplelogin_header(__($this->GetOption('register_text'), 'simplelogin'), '', $login_errors, $args);
?>

<form name="registerform" id="registerform" action="<?php echo simplelogin_url(array('action' => 'register')) ?>" method="post">
    <p>
        <label><?php _e('Username', 'simplelogin') ?><br />
        <input type="text" name="user_login" id="user_login" class="input" value="<?php echo attribute_escape(stripslashes($user_login)); ?>" size="20" tabindex="10" /></label>
    </p>
    <p>
        <label><?php _e('E-mail', 'simplelogin') ?><br />
        <input type="text" name="user_email" id="user_email" class="input" value="<?php echo attribute_escape(stripslashes($user_email)); ?>" size="20" tabindex="20" /></label>
    </p>
<?php do_action('register_form'); ?>
    <p id="reg_passmail"><?php _e($this->GetOption('register_msg'), 'simplelogin') ?></p>
    <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Register', 'simplelogin'); ?>" tabindex="100" /></p>
</form>

<?php
simplelogin_footer();
break;

case 'login' :
default :

    // Clear errors if loggedout is set.
    if ( !empty($_GET['loggedout']) )
        $login_errors = new WP_Error();

    // If cookies are disabled we can't log in even with a valid user+pass
    if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
        $login_errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."), 'simplelogin');

    // Some parts of this script use the main login form to display a message
    if        ( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )            $login_errors->add('loggedout', __('You are now logged out.', 'simplelogin'), 'message');
    elseif    ( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )    $login_errors->add('registerdisabled', __('User registration is currently not allowed.', 'simplelogin'));
    elseif    ( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )    $login_errors->add('confirm', __('Check your e-mail for the confirmation link.', 'simplelogin'), 'message');
    elseif    ( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )    $login_errors->add('newpass', __('Check your e-mail for your new password.', 'simplelogin'), 'message');
    elseif    ( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )    $login_errors->add('registered', __($this->GetOption('register_complete'), 'simplelogin'), 'message');

    simplelogin_header(__($this->GetOption('login_text'), 'simplelogin'), '', $login_errors, $args);
    
    if ( isset($_POST['log']) )
        $user_login = ( 'incorrect_password' == $login_errors->get_error_code() || 'empty_password' == $login_errors->get_error_code() ) ? attribute_escape(stripslashes($_POST['log'])) : '';
?>
<?php if ( !isset($_GET['checkemail']) || !in_array( $_GET['checkemail'], array('confirm', 'newpass') ) ) : ?>
<form name="loginform" id="loginform" action="<?php echo simplelogin_url(array('action' => 'login')) ?>" method="post">
    <p>
        <label><?php _e('Username', 'simplelogin') ?><br />
        <input type="text" name="log" id="user_login" class="input" value="<?php echo isset($user_login) ? $user_login : ''; ?>" size="20" tabindex="10" /></label>
    </p>
    <p>
        <label><?php _e('Password', 'simplelogin') ?><br />
        <input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
    </p>
<?php do_action('login_form'); ?>
    <p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> <?php _e('Remember Me', 'simplelogin'); ?></label></p>
    <p class="submit">
        <input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Log In', 'simplelogin'); ?>" tabindex="100" />
        <input type="hidden" name="redirect_to" value="<?php echo simplelogin_url() ?>" />
        <input type="hidden" name="testcookie" value="1" />
    </p>
</form>
<?php endif; ?>

<?php
simplelogin_footer();
break;

endswitch;
?>
