<?php
/**
 * Twitter API Wordpress library.
 * @author Tim Whitlock <@timwhitlock>
 */




/**
 * Call a Twitter API GET method.
 * 
 * @param string endpoint/method, e.g. "users/show"
 * @param array Request arguments, e.g. array( 'screen_name' => 'timwhitlock' )
 * @return array raw, deserialised data from Twitter
 * @throws TwitterApiException
 */ 
function twitter_api_get( $path, array $args = array() ){
    $Client = twitter_api_client();
    return $Client->call( $path, $args, 'GET' );
} 




/**
 * Call a Twitter API POST method.
 * 
 * @param string endpoint/method, e.g. "users/show"
 * @param array Request arguments, e.g. array( 'screen_name' => 'timwhitlock' )
 * @return array raw, deserialised data from Twitter
 * @throws TwitterApiException
 */ 
function twitter_api_post( $path, array $args = array() ){
    $Client = twitter_api_client();
    return $Client->call( $path, $args, 'POST' );
} 




/**
 * Enable caching of Twitter API responses using APC
 * @param int Cache lifetime in seconds
 * @return TwitterApiClient
 */
function twitter_api_enable_cache( $ttl ){
    $Client = twitter_api_client();
    return $Client->enable_cache( $ttl );
}




/**
 * Disable caching of Twitter API responses
 * @return TwitterApiClient
 */
function twitter_api_disable_cache( $ttl ){
    $Client = twitter_api_client();
    return $Client->disable_cache();
}



 
/** 
 * Include a component from the lib directory.
 * @param string $component e.g. "core", or "admin"
 * @return void fatal error on failure
 */
function twitter_api_include(){
    foreach( func_get_args() as $component ){
        require_once twitter_api_basedir().'/lib/twitter-api-'.$component.'.php';
    }
} 



/**
 * Get plugin local base directory in case __DIR__ isn't available (php<5.3)
 */
function twitter_api_basedir(){
    static $dir;
    isset($dir) or $dir = dirname(__FILE__).'/..';
    return $dir;    
}    




/**
 * Get fully configured and authenticated Twitter API client.
 * @return TwitterApiClient
 */ 
function twitter_api_client( $id = null ){
    static $clients = array();
    if( ! isset($clients[$id]) ){
        twitter_api_include('core');
        $clients[$id] = TwitterApiClient::create_instance( is_null($id) );
    }
    return $clients[$id];
}




/**
 * Contact Twitter for a request token, which will be exchanged for an access token later.
 * @return TwitterOAuthToken Request token
 */
function twitter_api_oauth_request_token( $consumer_key, $consumer_secret, $oauth_callback = 'oob' ){
    $Client = twitter_api_client('oauth');
    $Client->set_oauth( $consumer_key, $consumer_secret );     
    $params = $Client->oauth_exchange( TWITTER_OAUTH_REQUEST_TOKEN_URL, compact('oauth_callback') );
    return new TwitterOAuthToken( $params['oauth_token'], $params['oauth_token_secret'] );
}




/**
 * Exchange request token for an access token after authentication/authorization by user
 * @return TwitterOAuthToken Access token
 */
function twitter_api_oauth_access_token( $consumer_key, $consumer_secret, $request_key, $request_secret, $oauth_verifier ){
    $Client = twitter_api_client('oauth');
    $Client->set_oauth( $consumer_key, $consumer_secret, $request_key, $request_secret );     
    $params = $Client->oauth_exchange( TWITTER_OAUTH_ACCESS_TOKEN_URL, compact('oauth_verifier') );
    return new TwitterOAuthToken( $params['oauth_token'], $params['oauth_token_secret'] );
}




// Include application settings panel if in admin area
if( is_admin() ){
    twitter_api_include('core','admin');
}



/**
 * Enable localisation with static list of available translations.
 * Messages merged into default domain.
 */
function _twitter_api_init_l10n(){
    static $map = array (
        'pt_BR' => 'pt_BR',
        'de_DE' => 'de_DE',
    );
    if( preg_match('/^([a-z]{2})[\-_\s]([a-z]{2})$/i', get_locale(), $r ) ){
        $locale = strtolower($r[1]).'_'.strtoupper($r[2]);
        if( isset($map[$locale]) ){
            $locale = $map[$locale];
            $mofile = twitter_api_basedir().'/lang/twitter-api-'.$locale.'.mo';
            load_textdomain( 'default', $mofile );
        }
    }
}

add_action( 'init', '_twitter_api_init_l10n' );
