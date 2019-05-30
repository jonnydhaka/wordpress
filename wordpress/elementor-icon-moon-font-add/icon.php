<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * A Font Icon select box.
 */
class CASE27_Elementor_Control_Icon extends Base_Control {

	public static $icon;

	public function __construct($icon) {
		self::$icon = $icon;
		parent::__construct();
	}  
	
	public function get_type() {
		return 'icon';
	}
	public static function get_icons() {
		//$icons = $controls_registry->get_control('icon')->get_settings('options');
	//$icons = [];
		// Get arrays of icons.
		//$font_awesome_icons = require get_theme_file_path( 'fa.php' );
		$material_icons = require get_theme_file_path( 'fa.php' );
		$custom_icons = require get_theme_file_path( 'fa.php' );
		// foreach ($font_awesome_icons as $icon) {
		// 	$icons["fa {$icon}"] = str_replace('fa-', '', $icon);
		// }

		foreach ($custom_icons as $icon) {
			self::$icon["{$icon}"] = str_replace('icon-', '', $icon); 
		}
		//self::$icon["material-icons {$icon}"] = $icon;
		// foreach ($custom_icons as $icon) {
		// 	$icons[$icon] = str_replace('icon-', '', $icon);
		// }
		//$controls_registry->get_control('icon')->set_settings('options', $new_icons);
	
		return self::$icon;
	}
	protected function get_default_settings() {
		$default_settings = parent::get_default_settings();
		
		return [
			'icons' => self::get_icons(),
		];
	}
	public function content_template() {
		wp_enqueue_style( 'carleader-wp-default-font', 			 CAR_LEADER_THEME_URI . '/font/style.css', '', null );
		?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<select class="elementor-control-icon" data-setting="{{ data.name }}" data-placeholder="<?php _e( 'Select Icon', 'elementor' ); ?>">
					<option value=""><?php _e( 'Select Icon', 'elementor' ); ?></option>
					<# _.each( data.icons, function( option_title, option_value ) { #>
					<option value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
	}
}
?>

<?php
add_action('elementor/controls/controls_registered', function($el) {
	$preicon=$el->get_control('icon')->get_settings('options');
	$el->register_control('icon', new CASE27_Elementor_Control_Icon($preicon));
	
});