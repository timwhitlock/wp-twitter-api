<?php
/**
 * Helper utilities for working with Twitter API data.
 * @author Tim Whitlock
 */




/**
 * Utility for rendering tweet text with clickable links
 * @param string plain text tweet
 * @param string optional target for links, defaults to _blank
 * @param bool optionally specify that passed text is already escaped HTML
 * @return string HTML source of tweet text
 */
function twitter_api_html( $src, $target = '_blank', $alreadyhtml = false ){
    if( ! $alreadyhtml ){
        $src = esc_html( $src );
    }
    // linkify URLs (restricting to 30 chars as per twitter.com)
    $src = preg_replace_callback('!https?://(\S+)!', 'twitter_api_html_linkify_callback', $src );
    if( '_blank' !== $target ){
        $src = str_replace( '"_blank"', '"'.$target.'"', $src );
    }
    // linkify @names
    $src = preg_replace('!@([a-z0-9_]{1,15})!i', '<a class="twitter-screen-name" href="https://twitter.com/\\1" target="'.$target.'">\\0</a>', $src );
    // linkify #hashtags
    $src = preg_replace('/(?<!&)#(\w+)/i', '<a class="twitter-hashtag" href="https://twitter.com/search?q=%23\\1&amp;src=hash" target="'.$target.'">\\0</a>', $src );
    return $src;
} 



/**
 * @internal
 */
function twitter_api_html_linkify_callback( array $r ){
    list( $href, $text ) = $r;
    if( isset($text{30}) ){
        $text = substr_replace( $text, '&hellip;', 30 );
    }
    return '<a href="'.$href.'" target="_blank">'.$text.'</a>';
}





/**
 * Utility converts the date [of a tweet] to relative time descriprion, e.g. about 2 minutes ago
 * 
 */
function twitter_api_relative_date( $strdate ){
    // get universal time now.
    static $t, $y, $m, $d, $h, $i, $s, $o;
    if( ! isset($t) ){
        $t = time();
        sscanf(gmdate('Y m d H i s',$t), '%u %u %u %u %u %u', $y,$m,$d,$h,$i,$s);
    }
    // get universal time of tweet
    $tt = is_int($strdate) ? $strdate : strtotime($strdate);
    if( ! $tt || $tt > $t ){
        // slight difference between our clock and Twitter's clock can cause problem here - just pretend it was zero seconds ago
        $tt = $t;
        $tdiff = 0;
    }
    else {
        sscanf(gmdate('Y m d H i s',$tt), '%u %u %u %u %u %u', $yy,$mm,$dd,$hh,$ii,$ss);
        // Calculate relative date string
        $tdiff = $t - $tt;
    }
    // Less than a minute ago?
    if( $tdiff < 60 ){
        return __('Just now');
    }
    // within last hour? X minutes ago
    if( $tdiff < 3600 ){
        $idiff = (int) floor( $tdiff / 60 );
        return sprintf( _n( '%u minute ago', '%u minutes ago', $idiff ), $idiff );
    }
    // within same day? About X hours ago
    $samey = ($y === $yy) and
    $samem = ($m === $mm) and
    $samed = ($d === $dd);
    if( ! empty($samed) ){
        $hdiff = (int) floor( $tdiff / 3600 );
        return sprintf( _n( 'About an hour ago', 'About %u hours ago', $hdiff ), $hdiff );
    }
    $tf = get_option('time_format') or $tf = 'g:i A';
    // within 24 hours?
    if( $tdiff < 86400 ){
        return __('Yesterday at').date_i18n(' '.$tf, $tt );
    }
    // else return formatted date, e.g. "Oct 20th 2008 9:27 PM" */
    $df = get_option('date_format') or $df= 'M jS Y'; 
    return date_i18n( $df.' '.$tf, $tt );
}   



/**
 * Clean Emoji icons out of tweet text.
 * Wordpress isn't escaping these strings properly for database insertion.
 */
function twitter_api_strip_emoji( $text ){
    // replace all control and private use unicode sequences
    return preg_replace_callback('/\p{C}/u', '_twitter_api_strip_emoji_replace', $text );
}



/**
 * @internal
 */
function _twitter_api_strip_emoji_replace( array $r ){
    // emoticons start at U+1F601 (\xF0\x9F\x98\x81)
    // @todo plain text mappings for common smileys 
    return '';
}



/**
 * Resolve shortened url fields via entities
 * @return string
 */ 
function twitter_api_expand_urls( $text, array $entities ){
    if( isset($entities['urls']) && is_array($entities['urls']) ){
        foreach( $entities['urls'] as $r ){
            $text = str_replace( $r['url'], $r['expanded_url'], $text );
        }
    }
    if( isset($entities['media']) && is_array($entities['media']) ){
        foreach( $entities['media'] as $r ){
            if( 0 === strpos($r['display_url'], 'pic.twitter.com' ) ) {
                $text = str_replace( $r['url'], 'https://'.$r['display_url'], $text );
            }
            else {
                $text = str_replace( $r['url'], $r['expanded_url'], $text );
            }
        }
    }
    return $text;
}        



