<?php
namespace ElementorPro\Base;

use ElementorPro\License\API as License_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Base_Widget_Trait {
	public function is_editable() {
		return true;
	}

	public function get_categories() {
		return [ 'pro-elements' ];
	}

	public function get_css_config() {
		$widget_name = $this->get_group_name();

		$direction = is_rtl() ? '-rtl' : '';

		$css_file_path = 'css/widget-' . $widget_name . $direction . '.min.css';

		return [
			'key' => $widget_name,
			'version' => ELEMENTOR_PRO_VERSION,
			'file_path' => ELEMENTOR_PRO_ASSETS_PATH . $css_file_path,
			'data' => [
				'file_url' => ELEMENTOR_PRO_ASSETS_URL . $css_file_path,
			],
		];
	}
}
