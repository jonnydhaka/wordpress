<?php
final class Elementor_Test_Extension {

	public function init() {

		// Include plugin files
		$this->includes();

		// Register controls
		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );

	}

	public function includes() {

		require_once( plugin_dir_path(__FILE__) . '/icon.php' );

	}

	public function register_controls() {

		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
		$preicon=$controls_manager->get_control('icon')->get_settings('options');
		print_r(self::$preicon);
		exit();
		$controls_manager->register_control( 'icon', new CASE27_Elementor_Control_Icon($preicon) );
	}

}
$a=new Elementor_Test_Extension();
$a->init();