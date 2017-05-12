<?php

/**
 * Plugin Name: Custom New User Notification
 * Plugin URI: http://github.com/rafasashi
 * Description: This plugin allows you to customize the email sent on a new user registration.
 * Version: 1.0.0
 * Author: Rafasashi
 * Author URI: http://github.com/rafasashi
 */
include plugin_dir_path( __FILE__ ) . '/admin/includes.php';

//Loading style
add_action( 'admin_init', 'rew_plugin_admin_styles' );

function rew_plugin_admin_styles() {
	wp_register_style( 'rewPluginStylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	wp_enqueue_style( 'rewPluginStylesheet' );
}

/**
 * Calling settings page
 */
add_action( 'admin_menu', 'rew_plugin_menu' );

function rew_plugin_menu() {
	add_options_page( 'Custom New User Notification Options', 'Html User Notification', 'manage_options', 'rew-plugin-menu', 'rew_plugin_options' );
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
	 * @param int    $user_id        User ID.
	 * @param string $plaintext_pass Optional. The user's plaintext password. Default empty.
	 */
	function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
		$user = get_userdata( $user_id );
		$subject_user = get_option( 'rew_user_mail_subject' );
		$subject_admin = get_option( 'rew_admin_mail_subject' );

		$from_name_user = get_option( 'rew_user_mail_sender_name' );
		if ( empty( $from_name_user ) )
			$from_name_user = 'WordPress';

		$from_name_admin = get_option( 'rew_admin_mail_sender_name' );
		if ( empty( $from_name_admin ) )
			$from_name_admin = 'WordPress';

		$from_email_user = get_option( 'rew_user_mail_sender_email' );
		if ( empty( $from_email_user ) )
			$from_email_user = rew_stripurl( get_bloginfo( 'url' ) );

		$from_email_admin = get_option( 'rew_admin_mail_sender_email' );
		if ( empty( $from_email_admin ) )
			$from_email_admin = rew_stripurl( get_bloginfo( 'url' ) );

		$headers_user = 'From: ' . $from_name_user . ' <' . $from_email_user . '>  ' . "\r\n";
		$headers_admin = 'From: ' . $from_name_admin . ' <' . $from_email_admin . '>  ' . "\r\n";

		//Shortcodes
		$search = array( "[rew-display-name]", "[rew-user-login]", "[rew-user-password]", "[rew-user-email]" );
		$replace = array( $user->display_name, $user->user_login, $plaintext_pass, $user->user_email );

		// set content type to html
		add_filter( 'wp_mail_content_type', 'wpmail_content_type' );

		//for admin
		ob_start();

		echo rew_filter_post_content( get_option( 'rew_admin_mail_content' ) );

		$message_admin_raw = ob_get_contents();
		$message_admin = str_replace( $search, $replace, $message_admin_raw );

		ob_end_clean();

		@wp_mail( get_option( 'admin_email' ), $subject_admin, $message_admin, $headers_admin );

		if ( empty( $plaintext_pass ) ) {
			// remove html content type
			remove_filter( 'wp_mail_content_type', 'wpmail_content_type' );
			return;
		}

		//for user
		ob_start();

		echo rew_filter_post_content( get_option( 'rew_user_mail_content' ) );

		$message_user_raw = ob_get_contents();
		$message_user = str_replace( $search, $replace, $message_user_raw );

		ob_end_clean();

		wp_mail( $user->user_email, $subject_user, $message_user, $headers_user );

		// remove html content type
		remove_filter( 'wp_mail_content_type', 'wpmail_content_type' );
	}

}

/**
 * wpmail_content_type
 * allow html emails
 * @return string
 */
function wpmail_content_type() {

	return 'text/html';
}

/**
 * Apply wordpress post content filter on plain content. *
 */
function rew_filter_post_content( $content = '' ) {
	if ( ! empty( $content ) ) {
		$content = apply_filters( 'the_content', $content );
		$filtered_content = str_replace( ']]>', ']]&gt;', $content );
		return $filtered_content;
	} else {
		return $content;
	}
}

/**
 * Return domain name
 */
function rew_stripurl( $url ) {

	$urlParts = parse_url( $url );

	return $urlParts[ 'host' ];
}

/**
 * Settings link
 */
function rew_add_action_links( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=rew-plugin-menu' ) . '">Settings</a>',
	);
	return array_merge( $links, $mylinks );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'rew_add_action_links' );
