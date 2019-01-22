<?php
/**
 * @package color-style
 * @version 1.0
 */
/*
Plugin Name: Color Switcher 5
Plugin URI: http://wordpress.org
Description: This is not just a plugin, where developing custom color schema.
Author: Smart Data Soft
Version: 1.6
Author URI: http://wordpress.org
*/



class entrepreneur_color_scheme {
	public $is_custom = false;
	public $options = array();

	public function __construct() {
		self::customizer_options();
		add_action( 'customize_register', array( $this, 'customizer_register' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_js' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'color_scheme_template' ) );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'output_css' ) );
	}

	public function customizer_options() {
        $path_color_schema = dirname(__FILE__) . '/color_schema/';
        $color_schemes = array();
        foreach (glob($path_color_schema . 'default.php') as $filename) {
            if (file_exists($filename)) {
                include ($filename);
            }
        }
        if(isset($color_schemes['default']['colors'])){
        	$tmp_color_schemes = $color_schemes['default']['colors'];
        	$options = array();
        	foreach ($tmp_color_schemes as $key => $value) {
        		$options[] = $key;
        	}
        	$this->options = $options;
        }

	}

    public function get_color_schemes() {
        $path_color_schema = dirname(__FILE__) . '/color_schema/';
        foreach (glob($path_color_schema . '*.php') as $filename) {
            if (file_exists($filename)) {
                include ($filename);
            }
        }
        return $color_schemes;
    }

	public function customizer_register( WP_Customize_Manager $wp_customize ) {
		
		/*$wp_customize->add_section( 'colors', array(
	    	'title' => __( 'Colors', 'entrepreneur' ),
		) );
		*/
		$wp_customize->add_setting( 'color_scheme', array(
		    'default' => 'default',
		    //'transport' => 'postMessage',
		) );
		
	
        $entrepreneur_color_scheme = new entrepreneur_color_scheme();
        $color_schemes = $entrepreneur_color_scheme->get_color_schemes();
        $choices = array();
        foreach ($color_schemes as $color_scheme => $value) {
            $choices[$color_scheme] = $value['label'];
        }

		$wp_customize->add_control( 'color_scheme', array(
		    'label'   => __( 'Color scheme', 'entrepreneur' ),
		    'section' => 'theme_common_color_section',
		    'type'    => 'select',
		    'choices' => $choices,
		) );


/*
		$options = array(
		    'primary_color' => __( 'Primary color', 'entrepreneur' ),
		    'primary_hover_color' => __( 'Primary hover color', 'entrepreneur' ),
		    'secondary_color' => __( 'Secondary color', 'entrepreneur' ),
		    'secondary_hover_color' => __( 'Secondary hover color', 'entrepreneur' ),
		);
		foreach ( $options as $key => $label ) {
		    $wp_customize->add_setting( $key, array(
		        'sanitize_callback' => 'sanitize_hex_color',
		        'transport' => 'postMessage',
		    ) );
		    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, array(
		        'label' => $label,
		        'section' => 'colors',
		    ) ) );
		}
		*/
	}

	public function get_color_scheme() {
	    $color_schemes = $this->get_color_schemes();
	   // $color_scheme  = get_theme_mod( 'color_scheme' ); //theme_name_colors
	    $color_scheme  = get_theme_mod( 'theme_name_colors' );
	    $opt_color_scheme  = get_theme_mod( 'theme_name_colors' );
		$temp_color_scheme ='';
		if(isset($color_scheme ['color_scheme'])){
	    	$temp_color_scheme  = $color_scheme ['color_scheme'];
		}
	   
	    $color_scheme  = isset( $color_schemes[$temp_color_scheme] ) ? $temp_color_scheme : 'default';


	    if ( 'default' != $color_scheme ) {
	      $this->is_custom = true;
	    }
		if(isset($color_scheme ['color_scheme'])){
	    $colors = array_map( 'strtolower', $color_schemes[$color_scheme]['color_scheme'] );
	    return $colors;
		}
	}

	public function output_css() {
	    $colors = $this->get_color_scheme();
	    if ( $this->is_custom ) {
	      wp_add_inline_style( 'entrepreneur_style_css', $this->get_css( $colors ) );
	    }

      
	}

	public function get_css( $colors ) {
	    $css = '

	    /* Primary color: Teal %1$s */

			a,
			a:visited,
			.page .post-content h1::after {
				color: %1$s;
			}

			button,
			.button,
			.button:visited,
			.hero,
			.more-link,
			.more-link:visited,
			.nav-reveal,
			.sidebar input[type="submit"],
			.sidebar input[type="submit"]:visited,
			.sidebar input[type="button"],
			.sidebar input[type="button"]:visited {
				background: %1$s;
			}

			.hero {
				background-color: %1$s;
			}

			.widget-title {
				border-bottom: .25rem solid %1$s;
			}

			.bypostauthor {
				border-left: .25rem solid %1$s;
			}

			/* Primary hover color: Lighter Teal %2$s */

			a:hover,
			a:focus {
				color: %2$s;
			}

			button:hover,
			button:focus,
			.button:hover,
			.button:focus,
			.more-link:hover,
			.more-link:focus,
			.nav-reveal:hover,
			.sidebar input[type="submit"]:hover,
			.sidebar input[type="submit"]:focus,
			.sidebar input[type="button"]:hover,
			.sidebar input[type="button"]:focus {
				background: %2$s;
			}

			/* Secondary color: Yellow %3$s */

			header a,
			header a:visited,
			footer a,
			footer a:visited {
				color: %3$s;
			}

			.hero .button,
			.hero .button:visited,
			.pre-footer,
			aside .button,
			aside .button:visited {
				background: %3$s;
			}

			/* Secondary hover color: Lighter yellow %4$s */

			header a:hover,
			header a:focus,
			footer a:hover,
			footer a:focus {
				color: %4$s;
			}

			.hero .button:hover,
			.hero .button:focus,
			aside .button:hover,
			aside .button:focus {
				background: %4$s;
			}

		';
    	return vsprintf( $css, $colors );
	}

	public function color_scheme_template() {
	    $colors = array(
	      'primary_color'                 => '{{ data.primary_color }}',
	      'primary_hover_color'			  => '{{ data.primary_hover_color }}',
	      'secondary_color'				  => '{{ data.secondary_color }}',
	      'secondary_hover_color'		  => '{{ data.secondary_hover_color }}',
	    );
    ?>
	    <script type="text/html" id="tmpl-entrepreneur-color-scheme">
	      <?php echo $this->get_css( $colors ); ?>
	    </script>
	<?php
	}

	public function customize_js() {
	  wp_enqueue_script( 'entrepreneur-color-scheme', plugins_url() . '/colornawim/js/color-scheme.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '', true );
	  wp_localize_script( 'entrepreneur-color-scheme', 'EntrepreneurColorScheme', $this->get_color_schemes() );
	  wp_localize_script( 'entrepreneur-color-scheme', 'colorSettings', $this->options );
	}

	public function customize_preview_js() {
		wp_enqueue_script( 'entrepreneur-color-scheme-preview', plugins_url() . '/colornawim/js/color-scheme-preview.js', array( 'customize-preview' ), '', true );
	}
}

new entrepreneur_color_scheme;