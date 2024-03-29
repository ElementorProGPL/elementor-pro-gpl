<?php
/**
 * Plugin Name: ElementorProGPL
 * Description: This plugin enables GPL features of Elementor Pro: widgets, theme builder, dynamic colors & content, forms & popup builder, and more. Note that ElementorProGPL is not a substitute for Elementor Pro. If you need all Elementor Pro features, including access to pro templates library and dedicated support, we encourage you to <a href="https://elementor.com/pro/" target="_blank">purchase Elementor Pro</a>.
 * Plugin URI: https://elementorprogpl.github.io/elementor-pro-gpl/
 * Author: ElementorProGPL
 * Version: 3.12.2
 * Elementor tested up to: 3.11.0
 * Author URI: https://elementorprogpl.github.io/elementor-pro-gpl/
 *
 * Text Domain: elementor-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


function elementor_pro_gpl_plugin_load_plugin() {

	if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		return;
	}

	define( 'ELEMENTOR_PRO_VERSION', '3.12.2' );

	define( 'ELEMENTOR_PRO__FILE__', __FILE__ );
	define( 'ELEMENTOR_PRO_PLUGIN_BASE', plugin_basename( ELEMENTOR_PRO__FILE__ ) );
	define( 'ELEMENTOR_PRO_PATH', plugin_dir_path( ELEMENTOR_PRO__FILE__ ) );
	define( 'ELEMENTOR_PRO_ASSETS_PATH', ELEMENTOR_PRO_PATH . 'assets/' );
	define( 'ELEMENTOR_PRO_MODULES_PATH', ELEMENTOR_PRO_PATH . 'modules/' );
	define( 'ELEMENTOR_PRO_URL', plugins_url( '/', ELEMENTOR_PRO__FILE__ ) );
	define( 'ELEMENTOR_PRO_ASSETS_URL', ELEMENTOR_PRO_URL . 'assets/' );
	define( 'ELEMENTOR_PRO_MODULES_URL', ELEMENTOR_PRO_URL . 'modules/' );
	define( 'IS_ELEMENTOR_PRO_GPL', 'true' );
	add_action( 'plugins_loaded', 'elementor_pro_gpl_load_plugin_func' );
}

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementor_pro_gpl_load_plugin_func() {
	load_plugin_textdomain( 'elementor-pro' );

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'elementor_pro_gpl_plugin_fail_load' );

		return;
	}

	$elementor_version_required = '3.8.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'elementor_pro_gpl_plugin_fail_load_out_of_date' );

		return;
	}

	$elementor_version_recommendation = '3.12.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_recommendation, '>=' ) ) {
		add_action( 'admin_notices', 'elementor_pro_gpl_admin_notice_upgrade_recommendation' );
	}

	require ELEMENTOR_PRO_PATH . 'plugin.php';
}

elementor_pro_gpl_plugin_load_plugin();

function elementor_pro_gpl_print_error( $message ) {
	if ( ! $message ) {
		return;
	}
	// PHPCS - $message should not be escaped
	echo '<div class="error">' . $message . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementor_pro_gpl_plugin_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

		$message = '<h3>' . esc_html__( 'You\'re not using ElementorProGPL yet!', 'elementor-pro' ) . '</h3>';
		$message .= '<p>' . esc_html__( 'Activate the Elementor plugin to start using all of ElementorProGPL plugin’s features.', 'elementor-pro' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate Now', 'elementor-pro' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

		$message = '<h3>' . esc_html__( 'ElementorProGPL plugin requires installing the Elementor plugin', 'elementor-pro' ) . '</h3>';
		$message .= '<p>' . esc_html__( 'Install and activate the Elementor plugin to access all the features.', 'elementor-pro' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install Now', 'elementor-pro' ) ) . '</p>';
	}

	elementor_pro_gpl_print_error($message);
}

function elementor_pro_gpl_plugin_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );

	$message = sprintf(
	/* translators: 1: Title opening tag, 2: Title closing tag */
		esc_html__( '%1$sElementorProGPL  requires newer version of the Elementor plugin%2$s Update the Elementor plugin to reactivate the ElementorProGPL plugin.', 'elementor-pro' ),
		'<h3>',
		'</h3>'
	);
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, esc_html__( 'Update Now', 'elementor-pro' ) ) . '</p>';

	elementor_pro_gpl_print_error($message);
}

function elementor_pro_gpl_admin_notice_upgrade_recommendation() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = sprintf(
	/* translators: 1: Title opening tag, 2: Title closing tag */
		esc_html__( '%1$sDon’t miss out on the new version of Elementor%2$s Update to the latest version of Elementor to enjoy new features, better performance and compatibility.', 'elementor-pro' ),
		'<h3>',
		'</h3>'
	);
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, esc_html__( 'Update Now', 'elementor-pro' ) ) . '</p>';

	elementor_pro_gpl_print_error($message);
}

if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}
