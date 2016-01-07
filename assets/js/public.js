jQuery(document).ready(function($) {
    qsandbox_init();
});

/**
 * 
 * @see http://qsandbox.com
 *
 * @returns void
 */
function qsandbox_init() {
    var $ = jQuery;

    if ( typeof qsandbox_cfg.demo_setup_end_point == 'undefined' ) {
        console && console.log( 'qsandbox: qsandbox_cfg is not defined so not setting up anything.' );
        return ;
    }

    $('.qsandbox_demo_setup_form').submit( function (e) {
        var $ = jQuery; // Just in case;
        var msg_container = $( '.result', $(this) );
        msg_container.html('<span class="loading">Please wait...</span>');

        // Prevent double submissions.
        var submit_btn = $(this).find(':submit');
        submit_btn.hide();

        $.ajax({
            type : "post",
            dataType : "json",
            url : qsandbox_cfg.demo_setup_end_point,
            data : $(this).serialize() + '&ajax=1&src=qs_wp_plugin',
            success: function(json) {
               if ( json.status ) {
                  msg_container.html(json.html);
               } else {
                  msg_container.html(json.msg);
                  submit_btn.show();
               }
            }
        });

        return false;
    });
}
