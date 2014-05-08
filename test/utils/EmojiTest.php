<?php
/**
 * @group utils
 * @group emoji
 */
class EmojiTest extends PHPUnit_Framework_TestCase {


    public function _replace_blank(){
        return '';
    }
    
    
    private function get_all(){
        static $emoji;
        if( ! isset($emoji) ){
            $emoji = include __DIR__.'/inc-emoji.php';
        }
        return $emoji;
    }

    
    /**
     * Match every emoji character with simple replacement callback
     */
    public function testSingleMatches(){
        $blanker = array( $this, '_replace_blank' );
        foreach( $this->get_all() as $key => $raw ){
            $replaced = twitter_api_replace_emoji( 'o'.$raw.'k', $blanker );
            $this->assertEquals( 'ok', $replaced );
        }
    }
    

    /**
     * Match all emoji characters in single block
     */    
    public function testTotalMatch(){
        $blanker = array( $this, '_replace_blank' );
        $splurge = implode( '', $this->get_all() );
        $replaced = twitter_api_replace_emoji( 'o'.$splurge.'k', $blanker );
        $this->assertEquals( 'ok', $replaced );
    }    
    

    /**
     * Convert matched bytes back to unicode string reference
     */    
    public function testAllSequencesResolveUnicode(){
        foreach( $this->get_all() as $key => $raw ){
            $codes = twitter_api_utf8_array( $raw );
            $ucode = twitter_api_implode_unicode( $codes );
            $this->assertEquals( $key, $ucode );
        }
    }
    
    
    /**
     * Test default URL replacement
     */
    public function testDefaultUrlReplacement(){
        foreach( $this->get_all() as $key => $raw ){
            $html = twitter_api_replace_emoji( $raw );
            $want = '<img src="https://abs.twimg.com/emoji/v1/72x72/'.$key.'.png" style="font-size:1em;" class="emoji emoji-'.$key.'" />';
            $this->assertEquals( $want, $html );
        }
    }    


}


