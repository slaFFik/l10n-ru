<?php

/*
Last Update: 2.0
*/

global $wp_roles;
$user_roles = $wp_roles->get_names();
$email_names = array('user_reg_mail'           => 'New User Registration: User Notification',
                     'admin_reg_mail'          => 'New User Registration: Admin Notification',
                     'user_pass_request_mail'  => 'Password Request: User Notifcation',
                     'user_pass_changed_mail'  => 'Password Changed: User Notifcation',
                     'admin_pass_changed_mail' => 'Password Changed: Admin Notification'
                     );

if ( $_POST ) {
    if ( !current_user_can('manage_options') )
        die( __('Cheatin&#8217; huh?', 'simplelogin') );

    check_admin_referer('simple-login');
    $uninstall = isset($_POST['uninstall']) ? true : false;
    $allow_user_pass = isset($_POST['allow_user_pass']) ? true : false;
    foreach ($user_roles as $role => $value) {
        $dashboard_link[$role] = isset($_POST['dashboard_link'][$role]) ? true : false;
        $profile_link[$role] = isset($_POST['profile_link'][$role]) ? true : false;
        $login_redirect[$role] = stripslashes($_POST['login_redirect'][$role]);
    }
    $this->SetOption('uninstall', $uninstall);
    $this->SetOption('allow_user_pass', $allow_user_pass);
    $this->SetOption('login_redirect', $login_redirect);
    $this->SetOption('login_text', stripslashes($_POST['login_text']));
    $this->SetOption('register_text', stripslashes($_POST['register_text']));
    $this->SetOption('register_msg', stripslashes($_POST['register_msg']));
    $this->SetOption('register_complete', stripslashes($_POST['register_complete']));
    $this->SetOption('password_text', stripslashes($_POST['password_text']));
    $this->SetOption('password_msg', stripslashes($_POST['password_msg']));
    $this->SetOption('show_gravatar', $_POST['show_gravatar']);
    $this->SetOption('gravatar_size', absint($_POST['gravatar_size']));
    $this->SetOption('dashboard_link', $dashboard_link);
    $this->SetOption('dashboard_url', stripslashes($_POST['dashboard_url']));
    $this->SetOption('profile_link', $profile_link);
    $this->SetOption('profile_url', stripslashes($_POST['profile_url']));
    foreach ($email_names as $key => $value) {
        if (isset($_POST['disable_' . $key]))
            $this->SetOption('disable_' . $key, $_POST['disable_' . $key]);
        $this->SetOption('custom_' . $key . '_subject', stripslashes($_POST['custom_' . $key . '_subject']));
        $this->SetOption('custom_' . $key . '_message', stripslashes($_POST['custom_' . $key . '_message']));
    }
    $this->SaveOptions();

    if ($uninstall)
        $success = "To complete uninstall, deactivate this plugin. If you do not wish to uninstall, please uncheck the 'Complete Uninstall' checkbox.";
    else
        $success = "Settings saved.";
}
$login_redirect = $this->GetOption('login_redirect');
$show_gravatar = $this->GetOption('show_gravatar');
$dashboard_link = $this->GetOption('dashboard_link');
$profile_link = $this->GetOption('profile_link');
?>

<div class="updated" style="background:aliceblue; border:1px solid lightblue">
    <p><?php _e('If you like this plugin, please help keep it up to date by <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3836253">donating through PayPal</a>!', 'simplelogin'); ?></p>
</div>

<div class="wrap">
<?php if ( isset($success) && strlen($success) > 0 ) { ?>
    <div id="message" class="updated fade">
        <p><strong><?php _e($success, 'simplelogin'); ?></strong></p>
    </div>
<?php } ?>
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php _e('SimpleLogin Settings', 'simplelogin'); ?></h2>

    <form action="" method="post" id="tml-settings">
    <?php if ( function_exists('wp_nonce_field') ) wp_nonce_field('simple-login'); ?>
    
    <h3><?php _e('General Settings', 'simplelogin'); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="uninstall"><?php _e('Plugin', 'simplelogin'); ?></label></th>
            <td>
                <input name="uninstall" type="checkbox" id="uninstall" value="1" <?php if ($this->GetOption('uninstall')) { echo 'checked="checked"'; } ?> />
                <?php _e('Uninstall', 'simplelogin'); ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="allow_user_pass"><?php _e('Passwords', 'simplelogin'); ?></label></th>
            <td>
                <input name="allow_user_pass" type="checkbox" id="allow_user_pass" value="1" <?php if ($this->GetOption('allow_user_pass')) { echo 'checked="checked"'; } ?> />
                <?php _e('Allow users to set their own password', 'simplelogin'); ?>
            </td>
        </tr>
    </table>
    
    <h3><?php _e('Redirection Settings', 'simplelogin'); ?></h3>
    <p class="setting-description"><?php _e('Leave blank to send a user back to the page that they logged in from.', 'simplelogin'); ?></p>
    <table class="form-table">
        <?php foreach ($user_roles as $role => $value) : ?>
        <tr valign="top">
            <th scope="row"><label for="login_redirect[<?php echo $role; ?>]"><?php echo ucwords($role); ?> <?php _e('Login Redirect', 'simplelogin'); ?></label></th>
            <td>
                <input name="login_redirect[<?php echo $role; ?>]" type="text" id="login_redirect[<?php echo $role; ?>]" value="<?php echo htmlspecialchars($login_redirect[$role]); ?>" class="regular-text" />
                <?php echo $login_redirect[$role]; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3><?php _e('Template Settings', 'simplelogin'); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="register_text"><?php _e('Register Text', 'simplelogin'); ?></label></th>
            <td>
                <input name="register_text" type="text" id="register_text" value="<?php echo( htmlspecialchars ( $this->GetOption('register_text') ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="register_msg"><?php _e('Register Message', 'simplelogin'); ?></label></th>
            <td>
                <input name="register_msg" type="text" id="register_msg" value="<?php echo( htmlspecialchars ( $this->GetOption('register_msg') ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="register_complete"><?php _e('Registration Complete Message', 'simplelogin'); ?></label></th>
            <td>
                <input name="register_complete" type="text" id="register_complete" value="<?php echo( htmlspecialchars ( $this->GetOption('register_complete') ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="login_text"><?php _e('Login Text', 'simplelogin'); ?></label></th>
            <td>
                <input name="login_text" type="text" id="login_text" value="<?php echo( htmlspecialchars ( $this->GetOption('login_text') ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="password_text"><?php _e('Lost Password Text', 'simplelogin'); ?></label></th>
            <td>
                <input name="password_text" type="text" id="password_text" value="<?php echo( htmlspecialchars ( $this->GetOption('password_text') ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="password_msg"><?php _e('Lost Password Message', 'simplelogin'); ?></label></th>
            <td>
                <input name="password_msg" type="text" id="password_msg" value="<?php echo( htmlspecialchars ( $this->GetOption('password_msg') ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="show_gravatar"><?php _e('Show Gravatar?', 'simplelogin'); ?></label></th>
            <td>
                <select name="show_gravatar" id="show_gravatar"><option value="1" <?php if ($show_gravatar == true) echo 'selected="selected"'; ?>>Yes</option><option value="0" <?php if ($show_gravatar == false) echo 'selected="selected"'; ?>>No</option></select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="gravatar_size"><?php _e('Gravatar Size', 'simplelogin'); ?></label></th>
            <td>
                <input name="gravatar_size" type="text" id="gravatar_size" value="<?php echo absint($this->GetOption('gravatar_size')); ?>" class="small-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="dashboard_link"><?php _e('Show Dashboard Link?', 'simplelogin'); ?></label></th>
            <td>
                <?php foreach ($user_roles as $role => $value) : ?>
                <input name="dashboard_link[<?php echo $role; ?>]" type="checkbox" id="dashboard_link[<?php echo $role; ?>]" value="1" <?php if ($dashboard_link[$role] == true) { echo 'checked="checked"'; } ?> /> <?php echo ucwords($role); ?> &nbsp;
                <?php endforeach; ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="dashboard_url"><?php _e('Dashboard Link URL', 'simplelogin'); ?></label></th>
            <td>
                <input name="dashboard_url" type="text" id="dashboard_url" value="<?php echo( htmlspecialchars ( $this->GetOption('dashboard_url') ) ); ?>" class="regular-text" />
                <span class="setting-description"><?php _e('Leave blank for default.', 'simplelogin'); ?></span>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="profile_link"><?php _e('Show Profile Link?', 'simplelogin'); ?></label></th>
            <td>
                <?php foreach ($user_roles as $role => $value) : ?>
                <input name="profile_link[<?php echo $role; ?>]" type="checkbox" id="profile_link[<?php echo $role; ?>]" value="1" <?php if ($profile_link[$role] == true) { echo 'checked="checked"'; } ?> /> <?php echo ucwords($role); ?> &nbsp;
                <?php endforeach; ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="profile_url"><?php _e('Profile Link URL', 'simplelogin'); ?></label></th>
            <td>
                <input name="profile_url" type="text" id="profile_url" value="<?php echo( htmlspecialchars ( $this->GetOption('profile_url') ) ); ?>" class="regular-text" />
                <span class="setting-description"><?php _e('Leave blank for default.', 'simplelogin'); ?></span>
            </td>
        </tr>
    </table>
    
    <h3><?php _e('E-mail Settings', 'simplelogin'); ?></h3>
    <p class="setting-description"><?php _e('Leave blank to use defaults. Allowed variables:', 'simplelogin'); ?> %sitename%, %siteurl%, %reseturl% (Link for password reset), %user_login%, %user_pass%, %user_ip%</p>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="custom_<?php echo $key; ?>_from_address"><?php _e('From Address', 'simplelogin'); ?></label></th>
            <td>
                <input name="custom_mail_from_address" type="text" id="custom_mail_from_address" value="<?php echo htmlspecialchars($this->GetOption('custom_mail_from')); ?>" class="regular-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="custom_mail_from_name"><?php _e('From Name', 'simplelogin'); ?></label></th>
            <td>
                <input name="custom_mail_from_name" type="text" id="custom_mail_from_name" value="<?php echo htmlspecialchars($this->GetOption('custom_mail_from_name')); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php foreach($email_names as $key => $value) : ?>
    <h4><?php _e($value, 'simplelogin'); ?></h4>
    <table class="form-table">
        <?php if ($key !== 'user_pass_request_mail' || $key !== 'user_pass_changed_mail') : ?>
        <tr valign="top">
            <th scope="row"><label for="disable_<?php echo $key; ?>"><?php _e('Notification', 'simplelogin'); ?></label></th>
            <td>
                <input name="disable_<?php echo $key; ?>" type="checkbox" id="disable_<?php echo $key; ?>" value="1" <?php if ($this->GetOption('disable_' . $key)) { echo 'checked="checked"'; } ?> />
                <?php _e('Disable Notifcation', 'simplelogin'); ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr valign="top">
            <th scope="row"><label for="custom_<?php echo $key; ?>_subject"><?php _e('Subject', 'simplelogin'); ?></label></th>
            <td>
                <input name="custom_<?php echo $key; ?>_subject" type="text" id="custom_<?php echo $key; ?>_subject" value="<?php echo( htmlspecialchars ( $this->GetOption('custom_' . $key . '_subject') ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="custom_<?php echo $key; ?>_message"><?php _e('Message', 'simplelogin'); ?></label></th>
            <td>
                <textarea name="custom_<?php echo $key; ?>_message" id="custom_<?php echo $key; ?>_message" class="large-text"><?php echo( htmlspecialchars ( $this->GetOption('custom_' . $key . '_message') ) ); ?></textarea>
            </td>
        </tr>
    </table>
    <?php endforeach; ?>
    
    <p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', 'simplelogin'); ?>" />
    </form>
</div>
