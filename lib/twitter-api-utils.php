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
    // linkify URLs    
    $src = preg_replace('!https?://\S+!', '<a href="\\0" target="'.$target.'">\\0</a>', $src );
    // linkify @names
    $src = preg_replace('!@([a-z0-9_]{1,15})!i', '<a class="twitter-screen-name" href="https://twitter.com/\\1" target="'.$target.'">\\0</a>', $src );
    // linkify #hashtags
    $src = preg_replace('/(?<!&)#(\w+)/i', '<a class="twitter-hashtag" href="https://twitter.com/search?q=%23\\1&amp;src=hash" target="'.$target.'">\\0</a>', $src );
    return $src;
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
        return sprintf( _n( 'About %u hour ago', 'About %u hours ago', $hdiff ), $hdiff );
    }
    // within 24 hours?
    if( $tdiff < 86400 ){
        return __('Yesterday at ').twitter_api_date_format('g:i A', $tt );
    }
    // else return formatted date, e.g. "Oct 20th 2008 9:27 PM GMT" */
    return twitter_api_date_format('M jS Y g:i A', $tt );
}   



/**
 * Abstraction of date formatting for older PHP versions
 * - DateTime class requires PHP >= 5.2
 * - DateTime::setTimestamp requires PHP >= 5.3
 * @internal
 */
function twitter_api_date_format( $f, $t ){
    static $oldphp;
    if( ! isset($oldphp) ){
        $oldphp = 0 > version_compare(PHP_VERSION, '5.3');
    }
    if( $oldphp ){
        return date( $f, $t );
    }
    // using DateTime instance to avoid empty timezone warnings and such
    static $dt;
    if( ! isset($dt) ){
        $tz = ini_get('date.timezone') or $tz = 'Europe/London';
        $tz = new DateTimeZone( $tz );
        $dt = new DateTime;
        $dt->setTimezone( $tz );
    }
    $dt->setTimestamp( $t );
    return $dt->format( $f );
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


