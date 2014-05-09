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
            $want = '<img src="https://abs.twimg.com/emoji/v1/72x72/'.$key.'.png" style="width:1em;" class="emoji emoji-'.$key.'" />';
            $this->assertEquals( $want, $html );
        }
    }    


    /**
     * Test false positives
     */
    public function testFancyQuotesIntact(){
        $test = array ( 
            0x2018, 
            0x2019,
            0x201C,
            0x201D,
        );
        $blanker = array( $this, '_replace_blank' );        
        foreach( $test as $code ){
            $hex = sprintf('%04x', $code );
            $leave = $this->utf8_chr( $code );
            $bytes = $this->utf8_debug_string( $leave );
            $intact = twitter_api_replace_emoji( $leave, $blanker );
            $this->assertEquals( $leave, $intact, 'U+'.$hex.' wrongly matched: '.$bytes );
        }
    }
    

    
    /**
     * split a utf-8 string into a visual representation of single bytes
     */
    private function utf8_debug_string( $raw ){
        $debug = array();
        for( $i = 0; $i < strlen($raw); $i++ ){
            $debug[] = sprintf( '\\x%0X', ord( $raw{$i} ) );
        }
        return implode('',$debug);
    }   
     
    
    
    /**
     * Encode a Unicode code point to a utf-8 encoded string
     * @example functions/enc/utf8_chr.php
     * @param int Unicode code point up to 0x10FFFF
     * @return string multibyte character sequence
     */
    private function utf8_chr( $u ){
        if( 127 === ( $u | 127 ) ){
            // 7-bit ASCII
            return chr( $u );
        }
        // Double byte sequence ( < 0x800 )
        // 00000yyy yyzzzzzz ==> 110yyyyy 10zzzzzz
        // if( $u < 0x800 ) {
        if( 0 === ( $u & 0xFFFFF800 ) ){
            $c = chr( $u & 63 | 128 );            // "10zzzzzz"
            $c = chr( ($u>>=6) & 31 | 192 ) . $c; // "110yyyyy"
        }
        // Triple byte sequence ( < 0x10000 )
        // xxxxyyyy yyzzzzzz ==> 1110xxxx 10yyyyyy 10zzzzzz
        // else if( $u < 0x10000 ) {
        else if( 0 === ( $u & 0xFFFF0000 ) ){
            // Table 3-7 in the Unicode 5.0 standard disalows D800-DFFF:
            //if( $u >= 0xD800 && $u <= 0xDFFF ){
            //  trigger_error("Unicode code point $u is invalid", E_USER_NOTICE );
            //}
            $c = chr( $u & 63 | 128 );            // "10zzzzzz"
            $c = chr( ($u>>=6) & 63 | 128 ) . $c; // "10yyyyyy"
            $c = chr( ($u>>=6) & 15 | 224 ) . $c; // "1110xxxx"
        }
        // Four byte sequence ( < 0x10FFFF )
        // 000wwwxx xxxxyyyy yyzzzzzz ==> 11110www 10xxxxxx 10yyyyyy 10zzzzzz
        // else if( $u <= 0x10FFFF ) {
        else if( 0 === ( $u & 0xE0000000 ) ){
            $c = chr( $u & 63 | 128 );            // "10zzzzzz"
            $c = chr( ($u>>=6) & 63 | 128 ) . $c; // "10yyyyyy"
            $c = chr( ($u>>=6) & 63 | 128 ) . $c; // "10xxxxxx"
            $c = chr( ($u>>=6) &  7 | 240 ) . $c; // "11110www"
        }
        else {
            // integer too big 
            trigger_error("Unicode code point too large, $u", E_USER_NOTICE );
            $c = '?';
        }
        return $c;
    }    

}


