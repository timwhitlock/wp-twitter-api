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

That's it. Any Wordpress plugin can now make fully authenticated calls to the Twitter API.
