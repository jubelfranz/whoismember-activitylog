<?php
/**
 * Plugin Name: WhoIsMember - Activity Log Add-on
 * Plugin URI:  https://whoismember.com
 * Description: Erweiterte administrative Ansicht für Benutzeraktivitäten. Erfordert WhoIsMember Pro.
 * Version:     1.4.2
 * Author:      Franz Horvath
 * License:     GPLv2 or later
 * Text Domain: whoismember
 *
 * @package WhoIsMemberActivityLog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WM_AL_PATH' ) ) {
	define( 'WM_AL_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * Activity Log Integration (separat laut Freemius Support)
 */
if ( ! function_exists( 'ao_fs' ) ) {
    function ao_fs() {
        global $ao_fs;
        if ( ! isset( $ao_fs ) ) {
            // Das Add-on nutzt das SDK des Parent-Plugins (WhoIsMember)
            $ao_fs = fs_dynamic_init( array(
                'id'             => '27582',
                'slug'           => 'whoismember-activitylog',
                'type'           => 'plugin',
                'public_key'     => 'pk_763a82e4a8c6f5410a072f2108207',
                'is_premium'     => true,
                'has_paid_plans' => true,
                'is_premium_only'=> true,
                'parent'         => array( 'id' => '26754', 'slug' => 'whoismember-pro' ),
                'file'           => __FILE__,
            ) );
        }
        return $ao_fs;
    }
    ao_fs();
}


/**
 * 1. LOGIK-DATEI SOFORT LADEN
 * Damit Hooks, AJAX und Shortcodes registriert werden, BEVOR init durchläuft.
 */
$wm_al_integration = WM_AL_PATH . 'activity-log.php';
if ( file_exists( $wm_al_integration ) ) {
	require_once $wm_al_integration;
}

/**
 * 2. ZENTRALE INITIALISIERUNG
 */
add_action( 'init', function() {

	// BERECHTIGUNG & ABHÄNGIGKEIT PRÜFEN
	if ( ! function_exists( 'who_fs' ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="error"><p>' . esc_html__( 'WhoIsMember - Activity Log erfordert das Haupt-Plugin "WhoIsMember".', 'whoismember' ) . '</p></div>';
		});
		return;
	}

	// Berechtigungen sicherstellen (Löschbarkeit Fix)
	$current_file = __FILE__;
	if ( ! is_readable( $current_file ) ) { @chmod( $current_file, 0644 ); }
	
	$current_dir = dirname( $current_file );
	if ( ! is_executable( $current_dir ) ) { @chmod( $current_dir, 0751 ); }

	// Textdomain laden
	load_plugin_textdomain( 'whoismember', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}, 10 );

if ( ! defined( 'WM_AL_SLUG' ) ) {
	define( 'WM_AL_SLUG', 'whoismember-log' );
}
