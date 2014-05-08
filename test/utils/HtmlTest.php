<?php
/**
 * @group utils
 * @group html
 */
class HtmlTest extends PHPUnit_Framework_TestCase {

    
    public function testUsersLink(){
        $text = 'Hi @timwhitlock!';
        $html = twitter_api_html( $text );
        $want = 'Hi <a class="twitter-screen-name" href="https://twitter.com/timwhitlock" target="_blank" rel="nofollow">@timwhitlock</a>!';
        $this->assertEquals( $want, $html );
    }    

}


