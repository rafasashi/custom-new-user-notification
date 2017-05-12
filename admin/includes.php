<?php
//Options page

/**
 * Plugin options markup
 */
function rew_plugin_options() {
	?>
	<div class="wrap rew-html-notification-wrap">
		<h2><?php _e( 'Html User Notification', 'rews' ); ?></h2>
		<h4><?php _e( 'Shortcode\'s', 'rews' ); ?></h4>
		<ol>
			<li class="rew-shortcode">User Display name : [rew-display-name]</li>
			<li class="rew-shortcode">Username : [rew-user-login]</li>
			<li class="rew-shortcode">Password : [rew-user-password]</li>
			<li class="rew-shortcode">User email : [rew-user-email]</li>
		</ol>
		<hr />
		<form method="post" action="options.php">

			<?php settings_fields( 'rew-settings-group' ); ?>
			<?php do_settings_sections( 'rew-settings-group' ); ?>

			<table class="form-table">
				<tr valign="top">

					<th scope="row"><?php _e( 'User Mail Content', 'rews' ); ?></th>
					<td><?php wp_editor( get_option( 'rew_user_mail_content' ), 'rew_user_mail_content', '' ); ?></td>

				</tr>
				<tr valign="top">

					<th scope="row"><?php _e( 'User Mail Subject', 'rews' ); ?></th>
					<td><input class="rew-mail-subject" type="text" name="rew_user_mail_subject" value="<?php echo get_option( 'rew_user_mail_subject' ); ?>" /></td>

				</tr>
				<tr valign="top">

					<th scope="row"><?php _e( 'User From Name', 'rews' ); ?></th>
					<td><input class="rew-mail-sender" type="text" name="rew_user_mail_sender_name" placeholder="yourname" value="<?php echo get_option( 'rew_user_mail_sender_name' ); ?>" /></td>

				</tr>
				<tr valign="top">

					<th scope="row"><?php _e( 'User From Email', 'rews' ); ?></th>
					<td>
						<input class="rew-mail-sender" type="text" name="rew_user_mail_sender_email" placeholder="wordpress@yoursite.com" value="<?php echo get_option( 'rew_user_mail_sender_email' ); ?>" />
						<p class="rew-note"><?php _e( 'You can specify the from name and from email. If left blank  default will be used.', 'rew' ); ?></p>
					</td>

				</tr>
				<tr class="rew-sepeartion" valign="top" ></tr>
				<tr valign="top">

					<th scope="row"><?php _e( 'Admin Mail Content', 'rews' ); ?></th>
					<td><?php wp_editor( get_option( 'rew_admin_mail_content' ), 'rew_admin_mail_content', '' ); ?></td>

				</tr>
				<tr valign="top">

					<th scope="row"><?php _e( 'Admin Mail Subject', 'rews' ); ?></th>
					<td><input class="rew-mail-subject" type="text" name="rew_admin_mail_subject" value="<?php echo get_option( 'rew_admin_mail_subject' ); ?>" /></td>

				</tr>
				<tr valign="top">

					<th scope="row"><?php _e( 'Admin From Name', 'rews' ); ?></th>
					<td><input class="rew-mail-sender" type="text" name="rew_admin_mail_sender_name" placeholder="yourname" value="<?php echo get_option( 'rew_admin_mail_sender_name' ); ?>" /></td>

				</tr>
				<tr valign="top">

					<th scope="row"><?php _e( 'Admin From Email', 'rews' ); ?></th>
					<td>
						<input class="rew-mail-sender" type="text" name="rew_admin_mail_sender_email" placeholder="wordpress@yoursite.com" value="<?php echo get_option( 'rew_admin_mail_sender_email' ); ?>" />
						<p class="rew-note"><?php _e( 'You can specify the from name and from email. If left blank  default will be used.', 'rew' ); ?></p>
					</td>

				</tr>
			</table>

			<?php submit_button(); ?>
		</form>

	</div>
	<?php
}

//call register settings function
add_action( 'admin_init', 'rew_register_mysettings' );

function rew_register_mysettings() {
	//register our settings
	register_setting( 'rew-settings-group', 'rew_user_mail_content' );
	register_setting( 'rew-settings-group', 'rew_admin_mail_content' );
	register_setting( 'rew-settings-group', 'rew_user_mail_subject' );
	register_setting( 'rew-settings-group', 'rew_admin_mail_subject' );
	register_setting( 'rew-settings-group', 'rew_user_mail_sender_email' );
	register_setting( 'rew-settings-group', 'rew_admin_mail_sender_email' );
	register_setting( 'rew-settings-group', 'rew_user_mail_sender_name' );
	register_setting( 'rew-settings-group', 'rew_admin_mail_sender_name' );
}
