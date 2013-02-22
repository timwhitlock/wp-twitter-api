<?php
/*
Plugin Name: Twitter API
Plugin URI: https://timwhitlock.info/tag/twitter
Description: Provides generic access to the Twitter API with full authentication
Author: Tim Whitlock
Version: 1
Author URI: http://timwhitlock.info/
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
    _twitter_api_include('core');
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
    _twitter_api_include('core');
    $Client = twitter_api_client();
    return $Client->call( $path, $args, 'POST' );
} 



 
/** 
 * Include a component from the lib directory.
 * @param string $component e.g. "core", or "admin"
 * @return void fatal error on failure
 */
function _twitter_api_include(){
    static $dir;
    isset($dir) or $dir = dirname(__FILE__);
    foreach( func_get_args() as $component ){
        require_once $dir.'/lib/twitter-api-'.$component.'.php';
    }
} 






// Include application settings panel if in admin area
if( is_admin() ){
    _twitter_api_include('core','admin');
}
