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
        $demo_setup_id = $instance['demo_setup_id'];

        $api_instance = qSandbox_API::get_instance();
        $url = $api_instance->get_api_server_url();
        $api_path = '/app/ajax.php?cmd=demo.setup';

        $cfg = array(
            'demo_setup_end_point' => $url . $api_path,
        );

        $cfg_json = json_encode( $cfg );
		?>

        <script>
            var qsandbox_cfg = <?php echo $cfg_json; ?>;
        </script>
        
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
                <input type="hidden" name='setup_id' value="<?php echo $demo_setup_id; ?>" />
                <input id='demo_setup' class='demo_setup' type="submit" value="<?php echo $dmo_btn_cta; ?>" />
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
        $dmo_btn_cta	= esc_attr( empty( $instance[ 'dmo_btn_cta' ] ) ? 'Go!' : $instance[ 'dmo_btn_cta' ] );
        $demo_setup_id	= empty( $instance[ 'demo_setup_id' ] ) ? 0 : (int) $instance[ 'demo_setup_id' ];

        $qs_admin = qSandbox_Admin::get_instance();
        $opts = $qs_admin->get_options();

        $api_obj = qSandbox_API::get_instance();
        $setups_result_obj = $api_obj->get_demo_setups( $opts['api_key'] );

        $dropdown_elements = qSandbox_Util::array2dropdown_array( $setups_result_obj->data( 'items' ) );

        // if present we'll use it otherwise we'll default to the first element's ID.
        $sel_demo_id = empty( $demo_setup_id ) ? array_shift( array_keys( $dropdown_elements ) ) : $sel_demo_id;
        ?>
        <p>
          <label for="<?php echo $this->get_field_id( 'demo_setup_id' ); ?>"><?php _e( 'Demo Setup:' ); ?></label>
          <?php
            echo qSandbox_Util::html_select(
                $this->get_field_name( 'demo_setup_id' ),
                $sel_demo_id,
                $dropdown_elements,
                sprintf( 'id="%s"', $this->get_field_id( 'demo_setup_id' ) )
            );
           ?>
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                 placeholder="Optional"
                 name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('demo_instr'); ?>"><?php _e('Demo Instructions'); ?></label>

          <textarea class="widefat" id="<?php echo $this->get_field_id('demo_instr'); ?>"
                    name="<?php echo $this->get_field_name('demo_instr'); ?>"
                    rows="3"
                    placeholder="Try our cool themes."
                    ><?php echo $demo_instr; ?></textarea>
            <div>
              Text and HTML allowed <?php echo htmlentities( $this->allowed_tags_str ); ?>
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
		$instance['demo_setup_id'] = (int) trim( strip_tags( $new_instance['demo_setup_id'] ) );
        
        return $instance;
	}
}
