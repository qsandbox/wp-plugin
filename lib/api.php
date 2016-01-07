<?php

class qSandbox_API {
    private $api_end_point = ''; // dynamic end point based on the api key's prefix
    private $dev_api_end_point = 'http://qsandbox.com.clients.com/services/api/1.0';
    private $live_api_end_point = 'http://qsandbox.com/services/api/1.0';
    private $staging_api_end_point = 'http://bolt.devel.ca/services/api/1.0';
    
    /**
     * Only work as singleton.
     */
    private function __construct() {
        if ( ! empty( $_SERVER['DEV_ENV'] ) ) {
            $this->api_end_point = $this->dev_api_end_point;
        } else {
            $this->api_end_point = $this->live_api_end_point;
        }
    }

    /**
     * qSandbox_Admin::get_instance();
     * Singleton
     * @staticvar obj $instance
     * @return \cls
     */
    public static function get_instance() {
        static $instance = null;

        if (is_null($instance)) {
            $cls = __CLASS__;
            $instance = new $cls();
        }

        return $instance;
    }

    public function get_api_server() {
        return parse_url( $this->api_end_point, PHP_URL_HOST );
    }

    /**
     * Set up ssl.
     * @return str
     */
    public function get_api_server_url() {
        return 'http://' . $this->get_api_server();
    }

    public function get_api_endpoint() {
        return $this->api_end_point;
    }

    /**
     * 
     */
    public function verify_key( $key ) {
        $res = new qSandbox_Result();

        $url = $this->api_end_point . '/system/verify_api_key';
        
        $response = wp_remote_post( $url, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array( 'api_key' => $key, 'password' => '1234xyz' ),
            )
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            $res->msg( $error_message );
        } elseif ( !empty ( $response['body'] ) ) {
            $json = json_decode( $response['body'], true );

            if ( empty( $json ) ) {
                $res->msg( 'Cannot parse response from the server.' );
            } elseif ( ! empty( $json['status'] ) ) {
                $res->status( $json['status'] );
                $res->msg( $json['msg'] );
            } else {
                $res->msg( $json['msg'] );
            }

            $res->data( 'json', $json );
            $res->data( 'raw_body', $response['body'] );
        }

        return $res;
    }
    
    /**
     *
     */
    public function get_demo_setups( $key ) {
        $res = new qSandbox_Result();

        $url = $this->api_end_point . '/demo_setups/list';

        $response = wp_remote_post( $url, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array( 'api_key' => $key, 'password' => '' ),
            )
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            $res->msg( $error_message );
        } elseif ( ! empty ( $response['body'] ) ) {
            $json = json_decode( $response['body'], true );

            if ( empty( $json ) ) {
                $res->msg( 'Cannot parse response from the server.' );
            } elseif ( ! empty( $json['status'] ) ) {
                $res->status( $json['status'] );
                
                $res->data( 'items', $json['data']['items'] );
            } else {
                $res->msg( $json['msg'] );
            }

            $res->data( 'json', $json );
            $res->data( 'raw_body', $response['body'] );
        }

        return $res;
    }
}