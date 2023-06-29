<?php

	/**
	 * Plugin Name: Custom New User Notification
	 * Plugin URI: https://github.com/rafasashi/custom-new-user-notification
	 * Description: This plugin allows you to customize the email sent on a new user registration.
	 * Version: 1.2.0
	 * Author: Rafasashi
	 * Author URI: http://github.com/rafasashi
	 */
	 

	/**
	* Minimum version required
	*
	*/
	if ( get_bloginfo('version') < 4.3 ) return;

	/**
	* Add donation link
	*
	*/

	add_filter('plugin_row_meta', function ( $links, $file ){
		
		if ( strpos( $file, basename( __FILE__ ) ) !== false ) {
			$new_links = array( '<a href="https://www.paypal.me/recuweb" target="_blank">' . __( 'Donate', 'cleanlogin' ) . '</a>' );
			$links = array_merge( $links, $new_links );
		}
		
		return $links;
		
	}, 10, 2);
	 
	include plugin_dir_path( __FILE__ ) . '/admin/includes.php';

	//Loading style
	add_action( 'admin_init', 'cnun_plugin_admin_styles' );

	function cnun_plugin_admin_styles() {
		
		wp_register_style( 'cnunPluginStylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		wp_enqueue_style( 'cnunPluginStylesheet' );
	}

	/**
	 * Calling settings page
	 */
	add_action( 'admin_menu', 'cnun_plugin_menu' );

	function cnun_plugin_menu() {
		
		add_options_page( 'Custom New User Notification Options', 'Registration Email', 'manage_options', 'custom-new-user-notification', 'cnun_plugin_options' );
	}

	function cnun_get_email_info(){
		
		$blogname 	 = get_option('blogname');
		$admin_email = get_option('admin_email');
		
		$email_info = [];
		
		$email_info['subject_user'] 		= get_option( 'cnun_user_mail_subject', '[' . $blogname . '] Your username and password info' );
		$email_info['from_name_user'] 		= get_option( 'cnun_user_mail_sender_name', $blogname );
		$email_info['from_email_user']		= get_option( 'cnun_user_mail_sender_mail', $admin_email );
		$email_info['user_mail_content'] 	= get_option( 'cnun_user_mail_content', '<p>Username: [cnun-user-login]<br><br>To set your password, visit the following address:<br><br><a href="[cnun-reset-password-url]" data-wplink-url-error="true">[cnun-reset-password-url]</a><br></p>' );
		
		$email_info['subject_admin'] 		= get_option( 'cnun_admin_mail_subject', '[' . get_option('blogname') . '] New User Registration' );
		$email_info['from_name_admin']		= get_option( 'cnun_admin_mail_sender_name', $blogname );
		$email_info['from_email_admin'] 	= get_option( 'cnun_admin_mail_sender_mail', $admin_email );
		$email_info['admin_mail_content']	= get_option( 'cnun_admin_mail_content', '<p>New user registration on your site '.$blogname.':<br><br>Username: [cnun-user-login]<br><br>Email: [cnun-user-email]</p>' );
		
		return $email_info;
	}
	
	/*
	 * All the functions are in this file
	 */

	if ( ! function_exists( 'wp_new_user_notification' ) ) {

		/**
		 * Email login credentials to a newly-registered user.
		 *
		 * A new user registration notification is also sent to admin email.
		 *
		 * @since 2.0.0
		 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
		 * @since 4.3.1 The `$plaintext_pass` parameter was deprecated. `$notify` added as a third parameter.
		 * @since 4.6.0 The `$notify` parameter accepts 'user' for sending notification only to the user created.
		 *
		 * @global wpdb         $wpdb      WordPress database object for queries.
		 * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
		 *
		 * @param int    $user_id    User ID.
		 * @param null   $deprecated Not used (argument deprecated).
		 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
		 *                           string (admin only), 'user', or 'both' (admin and user). Default empty.
		 */
			 
		function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
			
			global $wpdb, $wp_hasher;
			
			// set email content type

			add_filter( 'wp_mail_content_type', 'cnun_mail_content_type' );
			
			// get user
			
			$user = get_userdata( $user_id );

			// Generate something random for a password reset key.
			
			$key = wp_generate_password( 20, false );		
				
			// get email info
			
			$email_info = cnun_get_email_info();

			//Shortcodes
			
			$shortcodes = array( 
			
				"[cnun-display-name]", 
				"[cnun-user-login]", 
				"[cnun-user-email]" ,
				"[cnun-reset-password-url]",
				PHP_EOL,
			);
			
			$data = array( 
			
				$user->display_name, 
				$user->user_login, 
				$user->user_email,
				network_site_url('wp-login.php?action=rp&key='.$key.'&login=' . rawurlencode($user->user_login), 'login'), 
				'<br/>',
			);
			
			if ( $deprecated !== null ) {
				
				_deprecated_argument( __FUNCTION__, '4.3.1' );
			}
			
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			
			if( 'user' !== $notify ) {
				
				$switched_locale = switch_to_locale( get_locale() );
				
				$message  = str_replace( $shortcodes, $data, $email_info['admin_mail_content'] );
				
				$subject  = str_replace( $shortcodes, $data, $email_info['subject_admin'] );
				
				$headers = 'From: ' . $email_info['from_name_admin'] . ' <' . $email_info['from_email_admin']  . '>  ' . "\r\n";
				
				@wp_mail( get_option( 'admin_email' ), $subject, $message, $headers );
				
				if ( $switched_locale ) {
					
					restore_previous_locale();
				}
			}
			
			// `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
			
			if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
				
				return;
			}
			
			/** This action is documented in wp-login.php */
			
			do_action( 'retrieve_password_key', $user->user_login, $key );
			
			// Now insert the key, hashed, into the DB.
			
			if ( empty( $wp_hasher ) ) {
				
				require_once ABSPATH . WPINC . '/class-phpass.php';
				
				$wp_hasher = new PasswordHash( 8, true );
			}
			
			$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
			
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
			
			$switched_locale = switch_to_locale( get_user_locale( $user ) );
			
			$message  = str_replace( $shortcodes, $data, $email_info['user_mail_content'] );
			
			$subject  = str_replace( $shortcodes, $data, $email_info['subject_user'] );
			
			$headers = 'From: ' . $email_info['from_name_user'] . ' <' . $email_info['from_email_user']  . '>  ' . "\r\n";
			
			@wp_mail($user->user_email, $subject, $message, $headers);
			
			if ( $switched_locale ) {
				
				restore_previous_locale();
			}
		}
	}

	function cnun_mail_content_type( $content_type ) {
		
		return 'text/html';
	}

	/**
	 * Settings link
	 */
	 
	function cnun_add_action_links( $links ) {
		
		$mylinks = array(
			'<a href="' . admin_url( 'options-general.php?page=custom-new-user-notification' ) . '">Settings</a>',
		);
		
		return array_merge( $links, $mylinks );
	}

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cnun_add_action_links' );
	
	add_filter( 'retrieve_password_message', 'replace_retrieve_password_message', 10, 4 );
	
	/**
	 * Returns the message body for the password reset mail.
	 * Called through the retrieve_password_message filter.
	 *
	 * @param string  $message    Default mail message.
	 * @param string  $key        The activation key.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 *
	 * @return string   The mail message to send.
	 */
	function replace_retrieve_password_message( $message, $key, $user_login, $user ) {

		add_filter( 'wp_mail_content_type', 'cnun_mail_content_type' );	
	
		// get email info
		
		$email_info = cnun_get_email_info();

		//Shortcodes
		
		$shortcodes = array( 
		
			"[cnun-display-name]", 
			"[cnun-user-login]", 
			"[cnun-user-email]" ,
			"[cnun-reset-password-url]",
			PHP_EOL,
		);
		
		$data = array( 
		
			$user->display_name, 
			$user->user_login, 
			$user->user_email,
			network_site_url('wp-login.php?action=rp&key='.$key.'&login=' . rawurlencode($user->user_login), 'login'), 
			'<br/>',
		);
		
		$msg  = str_replace( $shortcodes, $data, $email_info['user_mail_content'] );
		
		return $msg;
	}	
