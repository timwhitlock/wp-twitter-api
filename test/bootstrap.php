<?php
/**
 * Unit test bootstrapper.
 * This is nothing close to an accurate simulation of Wordpress environment, it's just for testing utils.
 * @usage phpunit --colors --bootstrap bootstrap.php .
 */
 
function is_admin(){
    return false;
} 

function esc_html( $text ){
    return htmlspecialchars( $text, ENT_COMPAT, 'UTF-8' );
}

require __DIR__.'/../twitter-api.php';

twitter_api_include('utils','core','unicode');
