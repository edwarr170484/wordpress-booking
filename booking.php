<?php
/**
 * Plugin Name: Service Booking
 * Description: Plugin realize booking functionality
 * Version: 1.0
 * Author: Edgar Zhiznevski
 * Author URI: https://github.com/edwarr170484
 */

 
register_activation_hook( __FILE__, 'ez_booking_activate');
register_deactivation_hook( __FILE__, 'ez_booking_deactivate');
register_uninstall_hook( __FILE__, 'ez_booking_uninstall');

function ez_booking_activate(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql = "CREATE TABLE " . $wpdb->prefix . "ez_booking_services (
		id int(15) NOT NULL auto_increment,
        service_name text DEFAULT NULL,
        service_price int(15) DEFAULT 0,
        is_active int(1) DEFAULT 1,
        PRIMARY KEY  (id)
	) $charset_collate;";

    $sql .= "CREATE TABLE " . $wpdb->prefix . "ez_booking_days (
		id int(15) NOT NULL auto_increment,
        day_name text DEFAULT NULL,
        is_active int(1) DEFAULT 1,
        start_time time NOT NULL,
        end_time time NOT NULL,
        time_period int(15) DEFAULT 0,
        times longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`times`)),
        PRIMARY KEY  (id)
 	) $charset_collate;\n";

    $sql .= "CREATE TABLE " . $wpdb->prefix . "ez_booking_order (
		id int(15) NOT NULL auto_increment,
        customer_name varchar(255) DEFAULT NULL,
        customer_phone varchar(255) DEFAULT NULL,
        customer_email varchar(255) DEFAULT NULL,
        customer_avatar varchar(255) DEFAULT NULL,
        date_created datetime NOT NULL,
        date date NOT NULL,
        time varchar(255) DEFAULT NUL,
        price int(15) DEFAULT NULL,
        services longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`services`)),
        answers longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`answers`)),
        comment longtext DEFAULT NULL,
        is_confirmed int(1) DEFAULT 0,
        PRIMARY KEY  (id)
 	) $charset_collate;\n";

    dbDelta($sql);
}

function ez_booking_deactivate(){
    if( shortcode_exists('ez-booking-form') ){
        remove_shortcode('ez-booking-form');
    }
}

function ez_booking_uninstall(){
    if( shortcode_exists('ez-booking-form') ){
        remove_shortcode('ez-booking-form');
    }

    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta("DROP TABLE " . $wpdb->prefix . "ez_booking_services;");
    dbDelta("DROP TABLE " . $wpdb->prefix . "ez_booking_days;");
    dbDelta("DROP TABLE " . $wpdb->prefix . "ez_booking_order;");
}

function ez_booking_create_shortcode($atts = [], $content = null, $tag = ''){
    require_once ABSPATH . 'wp-content/plugins/booking/templates/booking-form.php';
}

function ez_booking_shortocodes() {
	add_shortcode( 'ez-booking-form', 'ez_booking_create_shortcode' );
}

add_action( 'plugins_loaded', 'ez_booking_shortocodes', 10, 0 );

function ez_booking_admin_menu(){
    add_menu_page(
        'Simple booking plugin',
        'Booking',
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/options.php'     
    );
}   

add_action( 'admin_menu', 'ez_booking_admin_menu' );

function ez_booking_settings_init() {
    register_setting( 'ez_booking_options', 'ez_booking_options' );

    add_settings_section( 'questions_settings', 'Booking form questions', null, 'ez_booking' );

    add_settings_field( 'ez_booking_question_1', 'Question text 1', 'ez_booking_question_1', 'ez_booking', 'questions_settings' );
    add_settings_field( 'ez_booking_question_2', 'Question text 2', 'ez_booking_question_2', 'ez_booking', 'questions_settings' );
    add_settings_field( 'ez_booking_question_3', 'Question text 3', 'ez_booking_question_3', 'ez_booking', 'questions_settings' );
    add_settings_field( 'ez_booking_question_4', 'Question text 4', 'ez_booking_question_4', 'ez_booking', 'questions_settings' );
    add_settings_field( 'ez_booking_question_5', 'Question text 5', 'ez_booking_question_5', 'ez_booking', 'questions_settings' );
}

function ez_booking_question_1() {
    $options = get_option( 'ez_booking_options' );
    echo "<input id='ez_booking_question_1' name='ez_booking_options[question_1]' type='text' value='" . esc_attr( $options['question_1'] ) . "' style='width: 100%;' />";
}

function ez_booking_question_2(){
    $options = get_option( 'ez_booking_options' );
    echo "<input id='ez_booking_question_2' name='ez_booking_options[question_2]' type='text' value='" . esc_attr( $options['question_2'] ) . "' style='width: 100%;' />";
}

function ez_booking_question_3() {
    $options = get_option( 'ez_booking_options' );
    echo "<input id='ez_booking_question_3' name='ez_booking_options[question_3]' type='text' value='" . esc_attr( $options['question_3'] ) . "' style='width: 100%;'  />";
}

function ez_booking_question_4() {
    $options = get_option( 'ez_booking_options' );
    echo "<input id='ez_booking_question_4' name='ez_booking_options[question_4]' type='text' value='" . esc_attr( $options['question_4'] ) . "' style='width: 100%;'  />";
}

function ez_booking_question_5() {
    $options = get_option( 'ez_booking_options' );
    echo "<input id='ez_booking_question_5' name='ez_booking_options[question_5]' type='text' value='" . esc_attr( $options['question_5'] ) . "' style='width: 100%;'  />";
}

add_action('admin_init', 'ez_booking_settings_init');

function ez_booking_add_styles() {
    wp_enqueue_style( 'styles', plugin_dir_url(__FILE__) . '/assets/styles.css', array(), '1.1', 'all');
    wp_enqueue_script( 'script', plugin_dir_url(__FILE__) . '/assets/script.js', array(), '1.1', 'all');
}

add_action( 'wp_enqueue_scripts', 'ez_booking_add_styles' );

function process_qiwi_notifications(){
    
}

add_action( 'init', 'process_qiwi_notifications' );
