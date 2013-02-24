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


