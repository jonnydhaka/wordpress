<?php
/**
 * @package User Demo Variation
 */
/*
Plugin Name: User Demo Variation
Plugin URI: https://smartdatasoft.com/
Description: Change Color/Rtl/Boxed view for user.
Version: 1.0
Author: smartdatasoft
Author URI: https://smartdatasoft.com/
License: GPLv2 or later
Text Domain: user-demo-variation
*/

class userViewDemo {
	public $options='';
	public function __construct(){
		add_action( 'admin_menu', array($this,'userViewDemoMenu') );
		add_action( 'admin_init', array($this,'userViewDemoMenuSetting') );
		add_action( 'wp_enqueue_scripts', array($this,'userViewDemoMenu_enqueue_scripts') );
		add_action('wp_ajax_ajax_add_html',  array($this,'userViewDemoMenu_view'));
		add_action('wp_ajax_nopriv_ajax_add_html',  array($this,'userViewDemoMenu_view'));
	}
	public function userViewDemoMenu(){
		add_menu_page( 'User View Demo', "User View Demo",'manage_options' , 'userviewdemomenu', array(&$this, 'userViewDemoMenu_settings_page') , '', 10);
	}
	public function userViewDemoMenu_settings_page(){
		$this->options = get_option( 'userviewdemomenu_option' );
		?><div class="wrap">
        <h1>Setting</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields("userviewdemomenu-color-setting");
                do_settings_sections("userviewdemomenu");      
                submit_button();
            ?>          
        </form>
        </div>
    <?php
	}


	function userViewDemoMenuSetting(){
		

		register_setting(
            'userviewdemomenu-color-setting', // Option group
            'userviewdemomenu_option' // Option name
            // Sanitize
        );

        add_settings_section(
            'userviewdemomenu-setting-panel', // ID
            'Settings', // Title
            array( $this, 'userviewdemo_print_section_info' ), // Callback
            'userviewdemomenu' // Page
        );  

        add_settings_field(
            'active', // ID
            'Active', // Title 
            array( $this, 'userviewdemo_active_callback' ), // Callback
            'userviewdemomenu', // Page
            'userviewdemomenu-setting-panel' // Section           
        );      

        add_settings_field(
            'link', 
            'Link', 
            array( $this, 'userviewdemo_link_callback' ), 
            'userviewdemomenu', 
            'userviewdemomenu-setting-panel'
        );      
	}
	

	public function userviewdemo_print_section_info()
    {
        print 'Enter your settings below:';
	}

	public function userviewdemo_active_callback()
    {
		?>
		<input name="userviewdemomenu_option[check]" type="checkbox" value="1" <?php checked( '1', $this->options['check'] ); ?> /> 
    	<?php
	}

	public function userviewdemo_link_callback()
    {
        printf(
            '<input type="text" id="link" name="userviewdemomenu_option[link]" value="%s" />',
            isset( $this->options['link'] ) ? esc_attr( $this->options['link']) : ''
        );
    }
	
	function userViewDemoMenu_enqueue_scripts(){
		if(is_admin())
			return false ;
		wp_enqueue_style( 'userviewdemo-css', plugin_dir_url( __FILE__ ) . 'assets/css/user-demo-variation.css' );
		wp_enqueue_script( 'userviewdemo-js', plugin_dir_url( __FILE__ ) . 'assets/js/user-demo-variation.js', array('jquery') );
		wp_localize_script( 'userviewdemo-js', 'userviewdemoobjectlist',array('ajax_url' => admin_url('admin-ajax.php'),
		'link' =>  plugin_dir_url( __FILE__ ).'assets/css/' ));
	}
	function userViewDemoMenu_view(){
		$this->options = get_option( 'userviewdemomenu_option' );
		if(!$this->options['check'])
			return false;
		?>
		<div id="tt-boxedbutton">
			<a href="<?php echo  esc_url($this->options['link'])?>" target="_blank" class="rtlbutton external-link">
				<div class="box-btn">
					<i class="icon-g-54"></i>
				</div>
				<div class="box-description">
					Presentation&nbsp;<strong>VIDEO</strong>
				</div>
				<div class="box-disable">
					Disable
				</div>
			</a>
			<div class="rtlbutton boxbutton-js">
				<div class="box-btn">
					BOX
				</div>
				<div class="box-description">
					Use demo with&nbsp;<strong>BOX</strong>
				</div>
				<div class="box-disable">
					Disable
				</div>
			</div>
			<div class="rtlbutton rtlbutton-js">
				<div class="box-btn">
					RTL
				</div>
				<div class="box-description">
					Use demo with&nbsp;<strong>RTL</strong>
				</div>
				<div class="box-disable">
					Disable
				</div>
			</div>
			<div class="rtlbutton-color">
				<div class="box-btn"><img src="<?php echo  plugin_dir_url( __FILE__ ) ?>assets/img/rtlbutton-color.png" alt="" class="loading" data-was-processed="true"></div>
				<div class="box-description">
					<span class="box-title">COLOR SCHEME</span>
					<ul>
						<li data-color="01"class="active"><a href="#" class="colorswatch1"></a></li>
						<li data-color="02"><a href="#" class="colorswatch2"></a></li>
						<li data-color="03" class=""><a href="#" class="colorswatch3"></a></li>
						<li data-color="04" class=""><a href="#" class="colorswatch4"></a></li>
						<li data-color="05" class=""><a href="#" class="colorswatch5"></a></li>
						<li data-color="06" class=""><a href="#" class="colorswatch6"></a></li>
						<li data-color="07" class=""><a href="#" class="colorswatch7"></a></li>
						<li data-color="08" class=""><a href="#" class="colorswatch8"></a></li>
					</ul>
				</div>
			</div>
		</div>
	<?php
	wp_die();
	}

}

new userViewDemo();