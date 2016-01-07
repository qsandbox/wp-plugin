<?php

add_action( 'widgets_init', create_function( '', 'return register_widget("qSandbox_Widget");' ) );

/**
 * This is responsible for rendering the QS demo setup widget.
 *
 * @see https://developer.wordpress.org/themes/functionality/widgets/
 * @see https://pippinsplugins.com/simple-wordpress-widget-template/
 * @since 1.0
 */
class qSandbox_Widget extends WP_Widget {
    private $allowed_tags_str = '<p><div><a><span><b><i><u><img><strong><style>';

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
        extract( $args );
        
        $title 		 = apply_filters( 'widget_title', $instance['title'] );
        $demo_instr  = $instance['demo_instr'];
        $dmo_btn_cta = $instance['dmo_btn_cta'];

		?>
        <div id='qsandbox_demo_setup_form_wrapper' class='qsandbox_demo_setup_form_wrapper'>
            <?php echo $before_widget; ?>

            <?php
                if ( ! empty( $title ) ) {
                    echo "<div class='title'>\n";
                    echo $before_title . $title . $after_title;
                    echo "</div>\n";
                }

                if ( ! empty( $demo_instr ) ) {
                    echo "<div class='description'>$demo_instr</div>\n";
                }
            ?>

            <form id='qsandbox_demo_setup_form' class='qsandbox_demo_setup_form' method="post">
                <input type="hidden" name='demo_setup' value="yahoo!" />
                <input id='demo_setup' class='demo_setup' name='demo_setup' type="submit" value="<?php echo $dmo_btn_cta; ?>" />
                <div class='result'></div>
            </form>
            <?php echo $after_widget; ?>
        </div> <!-- /qsandbox_demo_setup_form_wrapper -->
        <?php
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
        $title 		= esc_attr( $instance['title'] );
        $demo_instr	= esc_attr( $instance['demo_instr'] );
        $dmo_btn_cta	= esc_attr( empty( $instance['dmo_btn_cta'] ) ? 'Setup Demo' : $instance['dmo_btn_cta'] );
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                 placeholder="Optional"
                 name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('demo_instr'); ?>"><?php _e('Demo Instructions'); ?></label>

          <textarea class="widefat" id="<?php echo $this->get_field_id('demo_instr'); ?>"
                    name="<?php echo $this->get_field_name('demo_instr'); ?>" rows="3"><?php echo $demo_instr; ?></textarea>
            <div>
              Some HTML allowed <?php echo htmlentities( $this->allowed_tags_str ); ?>
            </div>
        </p>
        <p class="submit_button_wrapper">
          <label for="<?php echo $this->get_field_id('dmo_btn_cta'); ?>"><?php _e('Demo Button Label (Call to Action)'); ?></label>
          <input class="submit_button widefat" id="<?php echo $this->get_field_id('dmo_btn_cta'); ?>"
                 placeholder="Enter some text"
                 name="<?php echo $this->get_field_name('dmo_btn_cta'); ?>" type="text" value="<?php echo $dmo_btn_cta; ?>" />
        </p>
        <?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = trim( strip_tags( $new_instance['title'], $this->allowed_tags_str ) );
		$instance['demo_instr'] = trim( strip_tags( $new_instance['demo_instr'], $this->allowed_tags_str ) );
		$instance['dmo_btn_cta'] = trim( strip_tags( $new_instance['dmo_btn_cta'], $this->allowed_tags_str ) );
        
        return $instance;
	}
}
