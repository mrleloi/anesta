<?php
/**
 * Child-Theme functions and definitions
 */

function anesta_child_scripts() {
    wp_enqueue_style( 'anesta-style', get_template_directory_uri(). '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'anesta_child_scripts' );

// Set BP to use wp_mail
add_filter( 'bp_email_use_wp_mail', '__return_true' );

// Set messages to HTML
remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
add_filter( 'wp_mail_content_type', 'set_html_content_type' );
function set_html_content_type() {
    return 'text/html';
}
 
// Use HTML template
add_filter( 'bp_email_get_content_plaintext', 'get_bp_email_content_plaintext', 10, 4 );
function get_bp_email_content_plaintext( $content = '', $property = 'content_plaintext', $transform = 'replace-tokens', $bp_email ) {
    if ( ! did_action( 'bp_send_email' ) ) {
        return $content;
    }
    return $bp_email->get_template( 'add-content' );
}
//Optionally remove the filter above after it's run
remove_filter('wp_mail','redirect_mails',20);
 
// Optionally change your email address
add_filter('wp_mail_from','noreply_from');
function noreply_from($from) {
  return 'no-reply@baityapp.online'; //Replace 'YOUR_DOMAIN.org' with email address
}

// Optionally change your from name
add_filter('wp_mail_from_name','noreply_from_name');
function noreply_from_name($name) {
    return 'BaityApp No-Reply'; //Replace 'YOUR_DOMAIN No-Reply' with the from name
}

//Give all members who register membership level 1
function my_pmpro_default_registration_level($user_id) {
    pmpro_changeMembershipLevel(1, $user_id);
}
add_action('user_register', 'my_pmpro_default_registration_level');

?>