<?php
//Options page

/**
 * Plugin options markup
 */
function cnun_plugin_options() {

	// get variables
	
	$blogname 	 = get_option('blogname');
	$admin_email = get_option('admin_email');
	
	$email_info = cnun_get_email_info();

	// output content
	
	echo'<div class="wrap cnun-html-notification-wrap">';
		
		echo'<h2>' . __( 'Custom User Notification', 'custom-new-user-notification' ) . '</h2>';
		
		echo'<h4>' . __( 'Shortcode\'s', 'custom-new-user-notification' ) . '</h4>';
		
		echo'<ul>';
			echo'<li class="cnun-shortcode">User name 	: [cnun-display-name]</li>';
			echo'<li class="cnun-shortcode">User login 	: [cnun-user-login]</li>';
			//echo'<li class="cnun-shortcode">Password 	: [cnun-user-password]</li>';
			echo'<li class="cnun-shortcode">User email 	: [cnun-user-email]</li>';
			echo'<li class="cnun-shortcode">Password url: [cnun-reset-password-url]</li>';
		echo'</ul>';
		
		echo'<hr />';
		
		echo'<form method="post" action="options.php">';

			settings_fields( 'cnun-settings-group' );
			do_settings_sections( 'cnun-settings-group' );

			echo'<table class="form-table">';
			
				// user email setup
			
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'User Mail Subject', 'custom-new-user-notification' ) . '</th>';
					echo'<td><input class="cnun-mail-subject" type="text" name="cnun_user_mail_subject" value="' .  $email_info['subject_user'] . '" /></td>';

				echo'</tr>';
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'User From Name', 'custom-new-user-notification' ) . '</th>';
					echo'<td><input class="cnun-mail-sender" type="text" name="cnun_user_mail_sender_name" placeholder="yourname" value="' .  $email_info['from_name_user'] . '" /></td>';

				echo'</tr>';
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'User From Email', 'custom-new-user-notification' ) . '</th>';
					echo'<td>';
						echo'<input class="cnun-mail-sender" type="text" name="cnun_user_mail_sender_mail" placeholder="wordpress@yoursite.com" value="' .  $email_info['from_email_user'] . '" />';
					echo'</td>';

				echo'</tr>';			
			
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'User Mail Content', 'custom-new-user-notification' ) . '</th>';
					echo'<td>';
					
					wp_editor( $email_info['user_mail_content'], 'cnun_user_mail_content', '' );
					
					echo'</td>';

				echo'</tr>';

				// separator
				
				echo'<tr class="cnun-sepeartion" valign="top" ></tr>';
				
				// admin email setup
				
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'Admin Mail Subject', 'custom-new-user-notification' ) . '</th>';
					echo'<td><input class="cnun-mail-subject" type="text" name="cnun_admin_mail_subject" value="' .  $email_info['subject_admin'] . '" /></td>';

				echo'</tr>';
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'Admin From Name', 'custom-new-user-notification' ) . '</th>';
					echo'<td><input class="cnun-mail-sender" type="text" name="cnun_admin_mail_sender_name" placeholder="yourname" value="' .  $email_info['from_name_admin '] . '" /></td>';

				echo'</tr>';
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'Admin From Email', 'custom-new-user-notification' ) . '</th>';
					echo'<td>';
						echo'<input class="cnun-mail-sender" type="text" name="cnun_admin_mail_sender_mail" placeholder="wordpress@yoursite.com" value="' .  $email_info['from_email_admin'] . '" />';
					echo'</td>';

				echo'</tr>';				
				
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'Admin Mail Content', 'custom-new-user-notification' ) . '</th>';
					echo'<td>';
					
					wp_editor( $email_info['admin_mail_content'], 'cnun_admin_mail_content', '' );
					
					echo'</td>';

				echo'</tr>';
				
			echo'</table>';

			submit_button();
			 
		echo'</form>';

	echo'</div>';
}

//call register settings function
add_action( 'admin_init', 'cnun_register_mysettings' );

function cnun_register_mysettings() {
	
	//register our settings
	
	register_setting( 'cnun-settings-group', 'cnun_user_mail_content' );
	register_setting( 'cnun-settings-group', 'cnun_admin_mail_content' );
	
	register_setting( 'cnun-settings-group', 'cnun_user_mail_subject' );
	register_setting( 'cnun-settings-group', 'cnun_admin_mail_subject' );
	
	register_setting( 'cnun-settings-group', 'cnun_user_mail_sender_email' );
	register_setting( 'cnun-settings-group', 'cnun_admin_mail_sender_email' );
	
	register_setting( 'cnun-settings-group', 'cnun_user_mail_sender_name' );
	register_setting( 'cnun-settings-group', 'cnun_admin_mail_sender_name' );
}
