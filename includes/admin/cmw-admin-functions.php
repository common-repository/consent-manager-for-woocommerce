<?php

function cmw_update_notice() {
    if( isset($_GET['settings-updated']) ) {
        ?>
    <div class="updated notice">
        <p><?php _e( 'Settings have been saved!', 'my_plugin_textdomain' ); ?></p>
    </div>
    <?php }
}
add_action( 'admin_notices', 'cmw_update_notice' );
