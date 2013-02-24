# Twitter API Wordpress Plugin

This plugin exposes a fully authenticated Twitter API client to Wordpress sites.


## Features

* Compatible with the new Twitter API 1.1;
* OAuth flow connects your Twitter account via Wordpress admin;
* Access to Twitter API client that any other plugin can use;
* Latest Tweets sidebar widget included as a fully functioning example;
* Caching of API responses - currently use APC only.


## Authentication

Once the plugin is installed and enabled you can bind it to a Twitter account, as follows:

* Register a Twitter application at https://dev.twitter.com/apps
* Note the Consumer key and Consumer secret under *OAuth settings*
* Go back to Wordpress admin and go to *Settings > Twitter API*
* Enter the consumer key and secret and click 'Save settings'
* Click the 'Connect to Twitter' button and follow the prompts.

Any Wordpress plugin can now make fully authenticated calls to the Twitter API.


## Twitter Client

The following functions are available as soon as the plugin is authenticated and operate as the default system Twitter account.

#### twitter_api_get
`array twitter_api_get ( string $path [, array $args ]  )`  
GETs data from the Twitter API, returning the raw unserialized data.

`$path` is any Twitter API method, e.g. `'users/show'` or `'statuses/user_timeline'`  
`$args` is an associative array of parameters.

Note that neither the path nor the arguments are validated.

#### twitter_api_post
`array twitter_api_post ( string $path [, array $args ]  )`  
As above, but POSTs data to the Twitter API.

#### twitter_api_enable_cache
`TwitterApiClient twitter_api_enable_cache( int $ttl )`  
Enable caching of Twitter response data for `$ttl` seconds. Requires the APC PHP extension.

#### twitter_api_disable_cache
`TwitterApiClient twitter_api_disable_cache( )`  
Disables caching of responses. Caching is disabled by default.


### Custom OAuth flows

The above functions work with a single authenticated Twitter account.
If you want to authenticate multiple clients or create OAuth flows outside of Wordpress admin, you'll have to work directly with the `TwitterApiClient` class and roll your own OAuth user flows.

The following utility functions will help you construct your own OAuth flows, but please see [Twitter's own documentation](https://dev.twitter.com/docs/auth/obtaining-access-tokens) if you're not familiar with the process.

#### twitter_api_oauth_request_token
`TwitterOAuthToken twitter_api_oauth_request_token ( string $consumer_key, string $consumer_secret, string $oauth_callback )`  
Fetches an OAuth request token from Twitter: e.g. `{ key: 'your request key', secret: 'your request secret' }`

#### twitter_api_oauth_access_token
`TwitterOAuthToken twitter_api_oauth_access_token ( $consumer_key, $consumer_secret, $request_key, $request_secret, $oauth_verifier )`
Exhanges a verified request token for an access token: e.g. `{ key: 'your access key', secret: 'your access secret' }`

### TwitterApiClient

Once you have your own authentication credentials you can work directly with the API client.
This example shows the main methods you might use:
    
    try {
       $Client = twitter_api_client('some client');
       $Client->set_oauth( 'my consumer key', 'my consumer secret', 'their access key', 'their access secret' );
       $user = $Client->call( 'users/show', array( 'screen_name' => 'timwhitlock' ), 'GET' );
       var_dump( $user );
    }
    catch( TwitterApiRateLimitException $Ex ){
        $info = $Client->last_rate_limit();
        wp_die( 'Rate limit exceeded. Try again at '.date( 'H:i:s', $info['reset'] ) );
    }
    catch( TwitterApiException $Ex ){
        wp_die( 'Twitter responded with status '.$Ex->getStatus().', '.$Ex->getMessage() );
    }
    catch( Exception $Ex ){
        wp_die( 'Fatal error, '. $Ex->getMessage() );
    }
