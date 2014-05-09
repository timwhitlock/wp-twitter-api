<?php
/**
 * @group utils
 * @group utf8
 */
class UnicodeTest extends PHPUnit_Framework_TestCase {
    
    public function testAsciiPassThrough(){
        $ints = twitter_api_utf8_array( 'abc' );
        $this->assertEquals( array(97,98,99), $ints );
    }
    
    public function testAsciiPassthroughReverse(){
        $chr = twitter_api_utf8_chr( 97 );
        $this->assertEquals( 'a', $chr );
    }
    

    public function testTwoByteCharacter(){
        // U+00A9 copyright symbol
        $text = "\xC2\xA9";
        $ints = twitter_api_utf8_array( $text );
        $this->assertCount( 1, $ints );
        $this->assertEquals( 0x00A9, $ints[0] );
    }    
    
    
    public function testTwoByteCharacterReverse(){
        $chr = twitter_api_utf8_chr( 0x00A9 );
        $this->assertEquals( "\xC2\xA9", $chr );
    }


    public function testThreeByteCharacter(){
        // U+2122 trademark symbol
        $text = "\xE2\x84\xA2";
        $ints = twitter_api_utf8_array( $text );
        $this->assertCount( 1, $ints );
        $this->assertEquals( 0x2122, $ints[0] );
    }    
    

    public function testThreeByteCharacterReverse(){
        $chr = twitter_api_utf8_chr( 0x2122 );
        $this->assertEquals( "\xE2\x84\xA2", $chr );
    }
    
    
}
