<?php
/**
 * Twitter API admin functions.
 * Configures and authenticates with the Twitter API.
 * @author Chris Ferdinandi <@cferdinandi>
 */



/**
 * Render the form field markup
 */

function twitter_api_settings_field_consumer_key() {
    $options = twitter_api_get_theme_options();
    ?>
    <input type="text" name="twitter_api_theme_options[consumer_key]" id="consumer-key" value="<?php echo esc_attr( $options['consumer_key'] ); ?>" />
    <label class="description" for="consumer-key"><?php _e( 'Your OAuth consumer Key', 'twitter_api' ); ?></label>
    <?php
}

function twitter_api_settings_field_consumer_secret() {
    $options = twitter_api_get_theme_options();
    ?>
    <input type="text" name="twitter_api_theme_options[consumer_secret]" id="consumer-secret" value="<?php echo esc_attr( $options['consumer_secret'] ); ?>" />
    <label class="description" for="consumer-secret"><?php _e( 'Your OAuth consumer secret', 'twitter_api' ); ?></label>
    <?php
}

function twitter_api_settings_field_access_key() {
    $options = twitter_api_get_theme_options();
    ?>
    <input type="text" name="twitter_api_theme_options[access_key]" id="access-key" value="<?php echo esc_attr( $options['access_key'] ); ?>" />
    <label class="description" for="access-key"><?php _e( 'Your OAuth access token', 'twitter_api' ); ?></label>
    <?php
}

function twitter_api_settings_field_access_secret() {
    $options = twitter_api_get_theme_options();
    ?>
    <input type="text" name="twitter_api_theme_options[access_secret]" id="access-secret" value="<?php echo esc_attr( $options['access_secret'] ); ?>" />
    <label class="description" for="access-secret"><?php _e( 'Your OAuth access secret', 'twitter_api' ); ?></label>
    <?php
}



/**
 * Sanitize fields before saving to the database
 */
function twitter_api_theme_options_validate( $input ) {
    $output = array();

    if ( isset( $input['consumer_key'] ) && ! empty( $input['consumer_key'] ) )
        $output['consumer_key'] = wp_filter_nohtml_kses( $input['consumer_key'] );

    if ( isset( $input['consumer_secret'] ) && ! empty( $input['consumer_secret'] ) )
        $output['consumer_secret'] = wp_filter_nohtml_kses( $input['consumer_secret'] );

    if ( isset( $input['access_key'] ) && ! empty( $input['access_key'] ) )
        $output['access_key'] = wp_filter_nohtml_kses( $input['access_key'] );

    if ( isset( $input['access_secret'] ) && ! empty( $input['access_secret'] ) )
        $output['access_secret'] = wp_filter_nohtml_kses( $input['access_secret'] );

    return apply_filters( 'twitter_api_theme_options_validate', $output, $input );
}



/**
 * Render the admin menu
 */
function twitter_api_theme_options_render_page() {
    ?>
    <div class="wrap">
        <?php

            // Variables
            $options = twitter_api_get_theme_options();
            $message = '';
            $css = '';

            // Get message
            if ( empty($options['consumer_key']) || empty($options['consumer_secret']) ) {
                $message = __( 'Twitter application not fully configured', 'twitter-api' );
                $css = 'error';
            } elseif ( empty($options['access_key']) || empty($options['access_secret']) ) {
                $message = __( 'Plugin not yet authenticated with Twitter', 'twitter-api' );
                $css = 'error';
            } else {
                try {
                    $me = twitter_api_get('account/verify_credentials');
                    $message = sprintf( __('Authenticated as @%s','twitter-api'), $me['screen_name'] );
                    $css = 'updated';
                }
                catch ( TwitterApiException $Ex ) {
                    $message = $Ex->getStatus().': Error '.$Ex->getCode().', '.$Ex->getMessage();
                    $css = 'error';
                }
                catch ( Exception $Ex ) {
                    $message = $Ex->getMessage();
                    $css = 'error';
                }
            }

        ?>
        <h2><?php _e( 'Twitter API Authentication Settings', 'twitter_api' ); ?></h2>
        <div class="<?php echo $css?>">
            <h3><?php echo esc_html($message)?></h3>
        </div>


        <form method="post" action="options.php">
            <?php
                settings_fields( 'twitter_api_options' );
                do_settings_sections( 'theme_options' );
                submit_button();
            ?>
        </form>
    </div>
    <?php
}



/**
 * Register the theme options page and its fields
 */
function twitter_api_theme_options_init() {

    // Register a setting and its sanitization callback
    register_setting( 'twitter_api_options', 'twitter_api_theme_options', 'twitter_api_theme_options_validate' );


    // Register our settings field group
    add_settings_section( 'general', 'General Options',  '__return_false', 'theme_options' );


    // Register our individual settings fields
    add_settings_field( 'consumer_key', __( 'Consumer Key', 'twitter_api' ), 'twitter_api_settings_field_consumer_key', 'theme_options', 'general' );
    add_settings_field( 'consumer_secret', __( 'Consumer Secret', 'twitter_api' ), 'twitter_api_settings_field_consumer_secret', 'theme_options', 'general' );
    add_settings_field( 'access_key', __( 'Access Key', 'twitter_api' ), 'twitter_api_settings_field_access_key', 'theme_options', 'general' );
    add_settings_field( 'access_secret', __( 'Access Secret', 'twitter_api' ), 'twitter_api_settings_field_access_secret', 'theme_options', 'general' );
}
add_action( 'admin_init', 'twitter_api_theme_options_init' );



/**
 * Add the admin page to the admin menu
 */
function twitter_api_theme_options_add_page() {
    $title = __( 'Twitter API', 'twitter-api' );
    add_options_page( $title, $title, 'manage_options', 'twitter-api-admin', 'twitter_api_theme_options_render_page' );
}
add_action( 'admin_menu', 'twitter_api_theme_options_add_page' );



/**
 * Restrict access to the theme options page to admins
 * @param  string $capability Minimum user capability to access the admin page
 */
function twitter_api_option_page_capability( $capability ) {
    return 'manage_options';
}
add_filter( 'option_page_capability_twitter_api_options', 'twitter_api_option_page_capability' );
