<?php
$PolicyURLs = ( isset( $_GET['tab'] ) && 'policy' == $_GET['tab'] ) ? true : false;
?>
<div class="wrap">
    <h1>Settings</h1>

        <h2 class="nav-tab-wrapper">

            <a href="<?php echo esc_url ( admin_url( 'admin.php?page=consent_settings' ) ); ?>" class="nav-tab<?php if ( ! isset( $_GET['tab'] ) || isset( $_GET['tab'] ) && 'gdpr' != $_GET['tab'] && 'policy' != $_GET['tab'] && 'consent' != $_GET['tab'] && 'organisation' != $_GET['tab'] ) echo ' nav-tab-active'; ?>">Introduction </a>

            <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'policy' ), admin_url( 'admin.php?page=consent_settings' ) ) ); ?>" class="nav-tab<?php if ( $PolicyURLs ) echo ' nav-tab-active'; ?>">Policy Pages</a>
        </h2>


    <div>

        <?php settings_fields( 'my-cool-plugin-settings-group' ); ?>
        <?php do_settings_sections( 'my-cool-plugin-settings-group' ); ?>

            <?php


                if ($PolicyURLs) {

                    // Policy Pages

                    include('policy-pages.php');

                } else {

                    include('introduction.php');
                }


            ?>

    </div>
</div>
