/**
 * This ajax handles feedback submission.
 * It serializes the feedback form and adds action. It relies on 'orbisius_simple_feedback_config' json config
 * which should have been injected by the WP plugin.
 * The code below gives feedback if the submission was successful.
 */

jQuery(document).ready(function($) {
    orbisius_simple_feedback_setup_js();
});

/**
 * Setups the hooks, clicks of buttons etc.
 * I am using a function to call it because sometimes JS gives error.
 * I have to be able to do the hooks no matter what.
 * The div also has a nice mouseover event which is a backup.
 * I am using the flag below so we don't have to setup the callbacks
 * multiple times.
 * @author Svetoslav Marinov (SLAVI)
 * @see http://orbisius.com
 *
 * @returns void
 */
function orbisius_simple_feedback_setup_js() {
    var $ = jQuery;

    $('#qsandbox_demo_setup_form').submit( function (e) {
        var $ = jQuery; // Just in case;
        var url = '//qsandbox.com.clients.com/app/ajax.php?cmd=demo.setup';
        var msg_container = $('#qsandbox_demo_setup_form .result');
        msg_container.html('<span class="loading">Please wait...</span>');

        $('.orbisius_simple_feedback_container .result').text('').removeClass('error success').hide();

        $.ajax({
            type : "post",
            dataType : "json",
            url : url, //orbisius_simple_feedback_config.plugin_ajax_url, // contains all the necessary params
            //url : orbisius_simple_feedback_config.plugin_ajax_url, // contains all the necessary params
            data : $(this).serialize() + '&ajax=1&src=qs',
            success: function(json) {
               if ( json.status ) {
                  msg_container.html(json.html);
               } else {
                  msg_container.html(json.msg);
               }
            }
        });

        return false;
    });
}
