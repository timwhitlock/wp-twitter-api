<?php
/*
Plugin Name: Latest Tweets Widget
Plugin URI: https://github.com/timwhitlock/wp-twitter-api/blob/master/latest-tweets-widget.php
Description: Provides a sidebar widget showing latest tweets
Author: Tim Whitlock
Version: 1
Author URI: http://timwhitlock.info/
*/



/**
 * Pull latest tweets with some caching of raw data.
 * @param string account whose tweets we're pulling
 * @param int number of tweets to get and display
 * @return array blocks of html expected by the widget
 */
function latest_tweets_render( $screen_name, $count ){
    if( ! function_exists('twitter_api_get') ){
        throw new Exception('Twitter API plugin is not enabled');
    }
    if( function_exists('apc_fetch') ){
        // We could cache the rendered HTML, but this tests the twitter_api cache functions
        twitter_api_enable_cache( 300 );
    }
    try {
        // Note that exluding replies and RTs may mean we get less than $count tweets.
        // So we'll get twice as many as we want and trim the result.
        $tweets = twitter_api_get('statuses/user_timeline', array (
            'count' => 2 * $count,
            'exclude_replies' => 'true',
            'include_rts' => 'true',
            'trim_user' => 'true',
            'screen_name' => $screen_name,
        ) );
        if( isset($tweets[$count]) ){
            array_splice( $tweets, 0, $count );
        }
    }
    catch( Exception $Ex ){
        return array( '<p><strong>Error:</strong> '.esc_html($Ex->getMessage()).'</p>' );
    }
    // render each tweet as a blocks of html for the widget list items
    twitter_api_include('utils');
    $rendered = array();
    foreach( $tweets as $tweet ){
        extract( $tweet );
        $link = esc_html( 'http://twitter.com/'.$screen_name.'/status/'.$id_str);
        $date = esc_html( twitter_api_relative_date($created_at) );
        $rendered[] = '<p class="text">'.twitter_api_html($text).'</p>'.
                      '<p class="details"><a href="'.$link.'" target="_blank"><time datetime="'.$created_at.'">'.$date.'</time></a></p>';
    }
    return $rendered;
} 


 
  
/**
 * Example latest tweets widget class
 */
class Latest_Tweets_Widget extends WP_Widget {
    
    /** @see WP_Widget::__construct */
    public function __construct( $id_base = false, $name = 'Latest Tweets', $widget_options = array(), $control_options = array() ){
        $this->options = array(
            array (
                'name'  => 'title',
                'label' => 'Widget title',
                'type'  => 'text'
            ),
            array (
                'name'  => 'screen_name',
                'label' => 'Twitter handle',
                'type'  => 'text'
            ),
            array (
                'name'  => 'num',
                'label' => 'Number of tweets',
                'type'  => 'text'
            ),
        );
        parent::__construct( $id_base, $name, $widget_options, $control_options );  
    }    
    
    /** @see WP_Widget::form */
    public function form( $instance ) {
        if ( empty($instance) ) {
            $instance['title']  = 'Latest Tweets';
            $instance['screen_name'] = '';
            $instance['num'] = '5';
        }
        foreach ( $this->options as $val ) {
            $label = '<label for="'.$this->get_field_id($val['name']).'">'.$val['label'].'</label>';
            echo '<p>'.$label.'<br />';
            echo '<input class="widefat" id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="text" value="'.esc_attr($instance[$val['name']]).'" /></p>';
        }
    }

    /** @see WP_Widget::widget */
    public function widget( array $args, $instance ) {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'], '<div class="latest-tweets">';  
        echo $args['before_title'], $instance['title'], $args['after_title'];
        echo '<ul class="latest-tweets">';
        foreach( latest_tweets_render( $instance['screen_name'], $instance['num'] ) as $tweet ){
            echo '<li class="latest-tweet">',$tweet,'</li>';
        }
        echo '</ul>';
        echo '</div>',$args['after_widget'];
    }
    
}
 


add_action( 'widgets_init',  function(){ return register_widget('Latest_Tweets_Widget'); } );

