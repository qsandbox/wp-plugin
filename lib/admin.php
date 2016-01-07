<?php

$qs_admin = qSandbox_Admin::get_instance();
add_action( 'init', array( $qs_admin, 'init' ) );

class qSandbox_Admin {
    private function __construct() {
        
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
    
    /**
     * 
     */
	public function init() {
        $suffix = '';
        
        /*wp_enqueue_script( 'jquery' );
		wp_register_script( 'qsandbox', plugins_url( "/assets/js/public{$suffix}.js", QSANDBOX_PLUGIN_FILE ), array( 'jquery', ),
				filemtime( plugin_dir_path( QSANDBOX_PLUGIN_FILE ) . "/assets/js/public{$suffix}.js" ), true );
		wp_enqueue_script( 'qsandbox' );*/

        add_action( 'admin_menu', array( $this, 'setup_admin_stuff' ) );
	}

    // Add the ? settings link in Plugins page very good
    function add_quick_plugin_settings_link($links, $file) {
        if ( $file == plugin_basename( QSANDBOX_PLUGIN_FILE ) ) {
            $link = admin_url( 'options-general.php?page=qsandbox_settings');
            $link_html = "<a href='$link'>Settings</a>";
            array_unshift($links, $link_html);
        }

        return $links;
    }

    public function setup_admin_stuff() {
        $this->add_options_page();

        // when plugins are show add a settings link near my plugin for a quick access to the settings page.
        add_filter( 'plugin_action_links', array( $this, 'add_quick_plugin_settings_link' ), 10, 2);
        register_setting( 'qsandbox_settings', 'qsandbox_options', array( $this, 'validate_settings_data' ) );
    }

    /**
     * Registers the options.
     */
    public function add_options_page() {
        add_options_page(
            'qSandbox',
            'qSandbox',
            'manage_options',
            'qsandbox_settings',
            array( $this, 'render_settings_page' )/*,
            array( $this, 'validate_settings_data' )*/
        );
    }

    /**
     *
     * @param array $data
     * @return array
     */
    public function validate_settings_data( $data ) {
        $data = array_map('strip_tags', $data);
        $data = array_map('trim', $data);
        return $data;
    }

    /**
     * Retrieves the plugin options. It inserts some defaults.
     * The saving is handled by the settings page. Basically, we submit to WP and it takes
     * care of the saving.
     *
     * @return array
     */
    function get_options() {
        $defaults = array(
            'api_key' => '',
            'setup_word' => 'go',
            'show_powered_by' => 0,
        );

        $opts = get_option('qsandbox_options');

        $opts = (array) $opts;
        $opts = array_merge($defaults, $opts);

        return $opts;
    }

    /**
    * Returns some plugin data such name and URL. This info is inserted as HTML
    * comment surrounding the embed code.
    * @return array
    */
   function get_plugin_data() {
       static $data = array();

       if ( ! empty( $data ) ) {
           return $data;
       }

       // pull only these vars
       $default_headers = array(
           'Name' => 'Plugin Name',
           'PluginURI' => 'Plugin URI',
           'Description' => 'Description',
       );

       $plugin_data = get_file_data( QSANDBOX_PLUGIN_FILE, $default_headers, 'plugin' );

       $url = $plugin_data['PluginURI'];
       $name = $plugin_data['Name'];

       $data['name'] = $name;
       $data['url'] = $url;

       $data = array_merge($data, $plugin_data);

       return $data;
   }

    /**
     * Outputs another link
     */
    public function render_settings_page() {
        $opts = $this->get_options();
        
        $api_key_notice = '';

        if ( ! empty( $opts['api_key'] ) ) {
            $api_obj = qSandbox_API::get_instance();
            $result_obj = $api_obj->verify_key( $opts['api_key'] );

            if ( $result_obj->isSuccess() ) {
                $api_key_notice .= $result_obj->msg();
                $setups_result_obj = $api_obj->get_demo_setups( $opts['api_key'] );

                $dropdown_elements = qSandbox_Util::array2dropdown_array( $setups_result_obj->data( 'items' ) );

                if ( ! empty( $dropdown_elements ) ) {
                    $api_key_notice .= "<hr/>\n";
                    $api_key_notice .= "Setup(s) found: <br/>";
                    $api_key_notice .= join( "<br/>\n", $dropdown_elements );
                    $api_key_notice .= "<hr/>\n";
                }
            } else {
                $api_key_notice .= $result_obj->msg();
            }
        }
    ?>
        <!--<h2><?php //esc_attr_e( '2 Columns Layout: static (px)', 'qsandbox' ); ?></h2>-->

        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h1><?php esc_attr_e( 'qSandbox', 'qsandbox' ); ?></h1>

            <div>
                <a href='http://qsandbox.com/?utm_source=qs_plugin&utm_medium=wp_admin_top' target="_blank">qSandbox</a> is a platform that allows you
                to launch test/staging WordPress sites in seconds to test drive plugins and themes. <br/>
                This plugin communicates with the qSandbox platform and you need to have to a registration in order to use it.
                Some of the features such as Demo Site has a minimum plan requirements
                <a href='http://qsandbox.com/app/princing.php' target="_blank">See Pricing</a> for more info.
            </div>

            <div id="poststuff">

                <div id="post-body" class="metabox-holder columns-2">

                    <!-- main content -->
                    <div id="post-body-content">

                        <div class="meta-box-sortables ui-sortable">

                            <div class="postbox">
                                <div class="inside">
                                    <form method="post" action="options.php">
                                        <?php settings_fields('qsandbox_settings'); ?>
                                        <table class="form-table">
                                            <tr>
                                                <th scope="row"><?php _e( 'API Key', 'qsandbox' ) ?></th>
                                                <td>
                                                    <label for="qsandbox_options_api_key">
                                                        <input type="text" id="qsandbox_options_api_key" class="widefat"
                                                               name="qsandbox_options[api_key]"
                                                            value="<?php echo esc_attr($opts['api_key']); ?>" />
                                                    </label>
                                                    <a href='http://qsandbox.com/app/account-api.php' target="_blank"> Get/regenerate the API key</a>
                                                    <p> <?php echo $api_key_notice; ?> </p>
                                                </td>
                                            </tr>
                                            
                                            <tr valign="top">
                                                <th scope="row"><?php _e( 'Misc', 'qsandbox' ) ?></th>
                                                <td>
                                                    <label for="radio_show_powered_by_enabled">
                                                        <input type="checkbox" id="radio_show_powered_by_enabled" name="qsandbox_options[show_powered_by]"
                                                            value="1" <?php echo empty($opts['show_powered_by']) ? '' : 'checked="checked"'; ?> />
                                                        <?php _e( 'Show Powered By qSandbox Text/Link (recommended)', 'qsandbox' ) ?>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>

                                        <p class="submit">
                                            <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'qsandbox' ) ?>" />
                                        </p>
                                    </form>
                                </div> <!-- .inside -->

                            </div> <!-- .postbox -->


                            <div class="postbox">
                                <?php
                                    $plugin_data = $this->get_plugin_data();

                                    $app_link = urlencode($plugin_data['PluginURI']);
                                    $app_title = urlencode($plugin_data['Name']);
                                    $app_descr = urlencode($plugin_data['Description']);
                                    ?>
                                    <h3>Share</h3>
                                    <p>
                                        <!-- AddThis Button BEGIN -->
                                    <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                                        <a class="addthis_button_facebook" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_twitter" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_google_plusone" g:plusone:count="false" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_linkedin" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_email" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_myspace" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_google" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_digg" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_delicious" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_stumbleupon" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_tumblr" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_favorites" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                        <a class="addthis_button_compact"></a>
                                    </div>
                                    <!-- The JS code is in the footer -->

                                    <script type="text/javascript">
                                        var addthis_config = {"data_track_clickback": true};
                                        var addthis_share = {
                                            templates: {twitter: 'Check out {{title}} #WordPress #plugin at {{lurl}} (via @orbisius)'}
                                        }
                                    </script>
                                    <!-- AddThis Button START part2 -->
                                    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=lordspace"></script>
                                    <!-- AddThis Button END part2 -->
                            </div> <!-- .postbox -->

                            <div class="postbox">

                                <h3><span>Usage / Help</span></h3>
                                <div class="inside">

                                    <strong>Process</strong><br/>
                                    <ol>
                                        <li>Install and activate the plugin - done</li>
                                        <li>Join qSandbox.</li>
                                        <li>Get the API key</li>
                                        <li>Add the qSandbox Demo Widget to your sidebar.</li>
                                    </ol>

                                    <iframe width="560" height="315" src="http://www.youtube.com/embed/IcOvYc14kBc" frameborder="0" allowfullscreen></iframe>
                                </div> <!-- .inside -->

                            </div> <!-- .postbox -->


                        </div>
                        <!-- .meta-box-sortables .ui-sortable -->

                    </div>
                    <!-- post-body-content -->

                    <!-- sidebar -->
                    <div id="postbox-container-1" class="postbox-container">

                        <div class="meta-box-sortables">

                            <div class="postbox">
                                <h3><span>Hire Us</span></h3>
                                <div class="inside">
                                    Hire us to create a plugin/web/mobile app
                                    <br/><a href="http://orbisius.com/page/free-quote/?utm_source=orbisius-theme-switcher&utm_medium=plugin-settings&utm_campaign=product"
                                       title="If you want a custom web/mobile app/plugin developed contact us. This opens in a new window/tab"
                                        class="button-primary" target="_blank">Get a Free Quote</a>
                                </div> <!-- .inside -->
                            </div> <!-- .postbox -->

                            <div class="postbox">
                                <h3><span>Newsletter</span></h3>
                                <div class="inside">
                                    <!-- Begin MailChimp Signup Form -->
                                    <div id="mc_embed_signup">
                                        <?php
                                            $current_user = wp_get_current_user();
                                            $email = empty($current_user->user_email) ? '' : $current_user->user_email;
                                        ?>

                                        <form action="http://WebWeb.us2.list-manage.com/subscribe/post?u=005070a78d0e52a7b567e96df&amp;id=1b83cd2093" method="post"
                                              id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                                            <input type="hidden" value="settings" name="SRC2" />
                                            <input type="hidden" value="qsandbox" name="SRC" />

                                            <span>Get notified about cool updates with the platform</span>
                                            <!--<div class="indicates-required"><span class="app_asterisk">*</span> indicates required
                                            </div>-->
                                            <div class="mc-field-group">
                                                <label for="mce-EMAIL">Email <span class="app_asterisk">*</span></label>
                                                <input type="email" value="<?php echo esc_attr($email); ?>" name="EMAIL" class="required email" id="mce-EMAIL">
                                            </div>
                                            <div id="mce-responses" class="clear">
                                                <div class="response" id="mce-error-response" style="display:none"></div>
                                                <div class="response" id="mce-success-response" style="display:none"></div>
                                            </div>	<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button-primary"></div>
                                        </form>
                                    </div>
                                    <!--End mc_embed_signup-->
                                </div> <!-- .inside -->
                            </div> <!-- .postbox -->

                            <?php
                                            $plugin_data = $this->get_plugin_data();
                                            $product_name = trim($plugin_data['PluginName']);
                                            $product_page = trim($plugin_data['PluginURI']);
                                            $product_descr = trim($plugin_data['Description']);
                                            $product_descr_short = substr($product_descr, 0, 50) . '...';

                                            $base_name_slug = basename(__FILE__);
                                            $base_name_slug = str_replace('.php', '', $base_name_slug);
                                            $product_page .= (strpos($product_page, '?') === false) ? '?' : '&';
                                            $product_page .= "utm_source=$base_name_slug&utm_medium=plugin-settings&utm_campaign=product";

                                            $product_page_tweet_link = $product_page;
                                            $product_page_tweet_link = str_replace('plugin-settings', 'tweet', $product_page_tweet_link);
                                        ?>

                            <div class="postbox">
                                <div class="inside">
                                    <!-- Twitter: code -->
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                                    <!-- /Twitter: code -->

                                    <!-- Twitter: Orbisius_Follow:js -->
                                        <a href="https://twitter.com/orbisius" class="twitter-follow-button"
                                           data-align="right" data-show-count="false">Follow @orbisius</a>

                                        <a href="https://twitter.com/qsandbox" class="twitter-follow-button"
                                           data-align="right" data-show-count="false">Follow @qSandbox</a>
                                    <!-- /Twitter: Orbisius_Follow:js -->

                                    &nbsp;

                                    <!-- Twitter: Tweet:js -->
                                    <a href="https://twitter.com/share" class="twitter-share-button"
                                       data-lang="en" data-text="Checkout qSandbox set up #WordPress #plugin"
                                       data-count="none" data-via="qsandbox" data-related="orbisius,qsandbox,lordspace"
                                       data-url="<?php echo $product_page_tweet_link;?>">Tweet</a>
                                    <!-- /Twitter: Tweet:js -->


                                    <br/>
                                     <a href="<?php echo $product_page; ?>" target="_blank" title="[new window]">Product Page</a>
                                        |
                                    <span>Support: <a href="http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/orbisius-theme-switcher/?utm_source=orbisius-theme-switcher&utm_medium=plugin-settings&utm_campaign=product"
                                        target="_blank" title="[new window]">Forums</a>

                                        <!--|
                                         <a href="http://docs.google.com/viewer?url=https%3A%2F%2Fdl.dropboxusercontent.com%2Fs%2Fwz83vm9841lz3o9%2FOrbisius_LikeGate_Documentation.pdf" target="_blank">Documentation</a>
                                        -->
                                    </span>
                                </div>
                            </div> <!-- .postbox -->

                            <div class="postbox"> <!-- quick-contact -->
                                <?php
                                $current_user = wp_get_current_user();
                                $email = empty($current_user->user_email) ? '' : $current_user->user_email;
                                $quick_form_action = is_ssl()
                                        ? 'https://ssl.orbisius.com/apps/quick-contact/'
                                        : 'http://apps.orbisius.com/quick-contact/';

                                if (!empty($_SERVER['DEV_ENV'])) {
                                    $quick_form_action = 'http://localhost/projects/quick-contact/';
                                }
                                ?>
                                <script>
                                    var qsandbox_quick_contact = {
                                        validate_form : function () {
                                            try {
                                                var msg = jQuery('#qsandbox_msg').val().trim();
                                                var email = jQuery('#qsandbox_email').val().trim();

                                                email = email.replace(/\s+/, '');
                                                email = email.replace(/\.+/, '.');
                                                email = email.replace(/\@+/, '@');

                                                if ( msg == '' ) {
                                                    alert('Enter your message.');
                                                    jQuery('#qsandbox_msg').focus().val(msg).css('border', '1px solid red');
                                                    return false;
                                                } else {
                                                    // all is good clear borders
                                                    jQuery('#qsandbox_msg').css('border', '');
                                                }

                                                if ( email == '' || email.indexOf('@') <= 2 || email.indexOf('.') == -1) {
                                                    alert('Enter your email and make sure it is valid.');
                                                    jQuery('#qsandbox_email').focus().val(email).css('border', '1px solid red');
                                                    return false;
                                                } else {
                                                    // all is good clear borders
                                                    jQuery('#qsandbox_email').css('border', '');
                                                }

                                                return true;
                                            } catch(e) {};
                                        }
                                    };
                                </script>
                                <h3><span>Quick Question or Suggestion</span></h3>
                                <div class="inside">
                                    <div>
                                        <form method="post" action="<?php echo $quick_form_action; ?>" target="_blank">
                                            <?php
                                                global $wp_version;
                                                $plugin_data = $this->get_plugin_data();

                                                $hidden_data = array(
                                                    'site_url' => site_url(),
                                                    'wp_ver' => $wp_version,
                                                    'first_name' => $current_user->first_name,
                                                    'last_name' => $current_user->last_name,
                                                    'product_name' => $plugin_data['Name'],
                                                    'product_ver' => $plugin_data['Version'],
                                                    'woocommerce_ver' => defined('WOOCOMMERCE_VERSION') ? WOOCOMMERCE_VERSION : 'n/a',
                                                );
                                                $hid_data = http_build_query($hidden_data);
                                                echo "<input type='hidden' name='data[sys_info]' value='$hid_data' />\n";
                                            ?>
                                            <textarea class="widefat" id='qsandbox_msg' name='data[msg]' required="required"></textarea>
                                            <br/>Your Email: <input type="text" class=""
                                                   id="qsandbox_email" name='data[sender_email]' placeholder="Email" required="required"
                                                   value="<?php echo esc_attr($email); ?>"
                                                   />
                                            <br/><input type="submit" class="button-primary" value="<?php _e('Send Feedback') ?>"
                                                        onclick="return qsandbox_quick_contact.validate_form();" />
                                            <br/>
                                            What data will be sent
                                            <a href='javascript:void(0);'
                                                onclick='jQuery(".qsandbox_data_to_be_sent").toggle();'>(show/hide)</a>
                                            <div class="hide-if-js app_hide qsandbox_data_to_be_sent">
                                                <textarea class="widefat" rows="4" readonly="readonly" disabled="disabled"><?php
                                                foreach ($hidden_data as $key => $val) {
                                                    if (is_array($val)) {
                                                        $val = var_export($val, 1);
                                                    }

                                                    echo "$key: $val\n";
                                                }
                                                ?></textarea>
                                            </div>
                                        </form>
                                    </div>
                                </div> <!-- .inside -->
                             </div> <!-- .postbox --> <!-- /quick-contact -->
                        </div>
                        <!-- .meta-box-sortables -->

                    </div>
                    <!-- #postbox-container-1 .postbox-container -->

                </div>
                <!-- #post-body .metabox-holder .columns-2 -->

                <br class="clear">
            </div>
            <!-- #poststuff -->

        </div> <!-- .wrap -->

        <!--<h2>Support & Feature Requests</h2>
        <div class="updated"><p>
                ** NOTE: ** Support is handled on our site: <a href="http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/orbisius-theme-switcher/?utm_source=orbisius-child-theme-editor&utm_medium=action_screen&utm_campaign=product" target="_blank" title="[new window]">http://club.orbisius.com/support/</a>.
                Please do NOT use the WordPress forums or other places to seek support.
        </p></div>-->

        <?php
    }

}
