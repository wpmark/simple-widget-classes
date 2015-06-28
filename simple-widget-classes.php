<?php
/**
Plugin Name: Simple Widget Classes
Plugin URI: https://github.com/wpmark/simple-widget-classes
Description: WordPress plugin to provide a text input for all widgets to add a custom class for that widget. functions.php file.
Author: Mark Wilkinson
Author URI: http://markwilkinson.me
Version: 0.3
*/

class Simple_Widget_Classes {
	
	/* hooks the class methods into wordpress through filters */
	public function __construct() {
		
		add_filter( 'widget_form_callback', array( $this, 'Form' ), 99, 2 );
		add_filter( 'widget_update_callback', array ($this, 'Update' ), 10, 2 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'Apply' ) );
		
	}
	
	/* adds the new form to each widget added to a sidebar in the admin */
	function form( $instance, $widget ) {
		
		if( !isset( $instance[ 'simple_widget_css_class' ] ) )
			$instance[ 'simple_widget_css_class' ] = null;

		?>
		
		<p>
			<label for='widget-<?php echo $widget->id_base; ?>-<?php echo $widget->number; ?>-simple_widget_css_class'><?php echo apply_filters( 'wpmark_swc_input_label', 'Additional CSS Classes (space separated)' ); ?>:
				<input class="widefat" id="<?php echo $instance[ 'simple_widget_css_class' ]; ?>" name="widget-<?php echo $widget->id_base; ?>[<?php echo $widget->number; ?>][simple_widget_css_class]" type="text" value="<?php echo $instance[ 'simple_widget_css_class' ]; ?>" />
			</label>							
		</p>
		
		<?php

		return $instance;
		
	}
	
	/* update the class input on widget save */
	function Update($instance, $new_instance) {
	
		$instance['simple_widget_css_class'] = wp_strip_all_tags( $new_instance['simple_widget_css_class'] );
		return $instance;
	}
	
	/* adds the class input box to each widget in the admin */
	function Apply( $params ) {
	
		global $wp_registered_widgets;
		$widget_id = $params[0][ 'widget_id' ];
		$widget = $wp_registered_widgets[ $widget_id ];
		
		if ( !( $widgetlogicfix = $widget[ 'callback' ][0]->option_name ) )
			# because the Widget Logic plugin changes this structure - how selfish of it!
			$widgetlogicfix = $widget[ 'callback_wl_redirect' ][0]->option_name;
			$option_name = get_option( $widgetlogicfix );
			$number = $widget[ 'params' ][0][ 'number' ];
			
		if( isset( $option_name[ $number ][ 'simple_widget_css_class' ] ) && !empty( $option_name[ $number ][ 'simple_widget_css_class' ] ) ) {
			/* find the end of the class= part and replace with new class and the closing "> */
			$params[0]['before_widget'] = preg_replace('/">/', " {$option_name[$number]['simple_widget_css_class']}\">", $params[0]['before_widget'], 1);
		}
		
		return $params;
		
	}
			
} # end of class

/* instatiate the plugin / class */
$new_simple_widget_classes = new Simple_Widget_Classes();