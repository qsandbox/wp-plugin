<?php

$qs_bootstrap = new qSandbox_Bootstrap();
add_action( 'init', array( $qs_bootstrap, 'init' ) );

class qSandbox_Bootstrap {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
        
	}

    /**
     * 
     */
	public function init() {
        $suffix = '';
        wp_enqueue_script( 'jquery' );
		wp_register_script( 'qsandbox', plugins_url( "/assets/js/public{$suffix}.js", QSANDBOX_PLUGIN_FILE ), array( 'jquery', ),
				filemtime( plugin_dir_path( QSANDBOX_PLUGIN_FILE ) . "/assets/js/public{$suffix}.js" ), true );
		wp_enqueue_script( 'qsandbox' );
	}
}
