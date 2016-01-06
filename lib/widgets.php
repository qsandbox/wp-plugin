<?php

add_action( 'widgets_init', create_function( '', 'return register_widget("qSandbox_Widget");' ) );

class qSandbox_Widget extends WP_Widget {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
        $widget_ops = array( 
            'class_name' => 'qsandbox_demo_site_widget',
            'description' => 'Demo qSandbox Demo Widget',
        );

		parent::__construct( 'qsandbox', 'qSandbox Demo Site Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		?>
        <form id='qsandbox_demo_setup_form' method="">
            <input type="hidden" name='demo_setup' value="yahoo!" />
            <input id='demo_setup' name='demo_setup' type="submit" value="Hit Me!" />
            <div class='result'></div>
        </form>
        <?php
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}
