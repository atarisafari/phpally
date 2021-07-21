<?php

use CidiLabs\PhpAlly\Rule\VideoCaptionsMatchCourseLanguage;

class VideoCaptionsMatchCourseLanguageTest extends PhpAllyTestCase {
    public function testCheckOneIssueWrongLanguage()
    {
        $html = '<div><a href="https://www.youtube.com/watch?v=vFF0uV9AOB8">Valid Link</a></div>';
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadHTML($html);
        $options = [
            'vimeoApiKey' => 'test',
            'youtubeApiKey' => 'test',
            'kalturaApiKey' => 'test',
            'kalturaUsername' => 'test'
        ];

        $ruleMock = $this->getMockBuilder(VideoCaptionsMatchCourseLanguage::class)
            ->setConstructorArgs([$dom, $options])
            ->setMethods(array('getCaptionState'))
            ->getMock();

        $ruleMock->expects($this->once())
            ->method('getCaptionState')
            ->will($this->returnValue(0));

        $this->assertEquals(1, $ruleMock->check());
    }

    public function testCheckNoIssuesRightLanguage()
    {
        $html = '<div><a href="https://www.youtube.com/watch?v=vFF0uV9AOB8">Valid Link</a></div>';
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadHTML($html);
        $options = [
            'vimeoApiKey' => 'test',
            'youtubeApiKey' => 'test',
            'kalturaApiKey' => 'test',
            'kalturaUsername' => 'test'
        ];

        $ruleMock = $this->getMockBuilder(VideoCaptionsMatchCourseLanguage::class)
            ->setConstructorArgs([$dom, $options])
            ->setMethods(array('getCaptionState'))
            ->getMock();

        $ruleMock->expects($this->once())
            ->method('getCaptionState')
            ->will($this->returnValue(2));

        $this->assertEquals(0, $ruleMock->check());
    }

    public function testCheckNoIssuesVimeo()
    {
        $html = '<div><a href="https://vimeo.com/205755088">Valid Link</a></div>';
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadHTML($html);
        $options = [
            'vimeoApiKey' => 'bef37736cfb26b6dc52986d8f531d0ad',
            'youtubeApiKey' => 'AIzaSyB5bTf8rbYwiM73k1rj8dDnwEalwTqdz_c'
        ];
        $ruleMock = $this->getMockBuilder(VideoCaptionsMatchCourseLanguage::class)
            ->setConstructorArgs([$dom, $options])
            ->setMethods(array('getCaptionState'))
            ->getMock();

        $ruleMock->expects($this->once())
            ->method('getCaptionState')
            ->will($this->returnValue(2));

        $this->assertEquals(0, $ruleMock->check());
    }

    public function testCheckNoIssuesKaltura()
    {
        $html = '<div><a href="https://cdnapisec.kaltura.com/p/4183983">Valid Link</a></div>';
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadHTML($html);
        $options = [
            'vimeoApiKey' => 'bef37736cfb26b6dc52986d8f531d0ad',
            'youtubeApiKey' => 'AIzaSyB5bTf8rbYwiM73k1rj8dDnwEalwTqdz_c'
        ];
        $ruleMock = $this->getMockBuilder(VideoCaptionsMatchCourseLanguage::class)
            ->setConstructorArgs([$dom, $options])
            ->setMethods(array('getCaptionState'))
            ->getMock();

        $ruleMock->expects($this->once())
            ->method('getCaptionState')
            ->will($this->returnValue(2));

        $this->assertEquals(0, $ruleMock->check());
    }

    public function testCheckNoIssuesUnsupportedSite()
    {
        $html = '<div><a href="https://fakewebsite.com/p/4183983">Valid Link</a></div>';
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadHTML($html);
        $options = [
            'vimeoApiKey' => 'bef37736cfb26b6dc52986d8f531d0ad',
            'youtubeApiKey' => 'AIzaSyB5bTf8rbYwiM73k1rj8dDnwEalwTqdz_c'
        ];
        $ruleMock = $this->getMockBuilder(VideoCaptionsMatchCourseLanguage::class)
            ->setConstructorArgs([$dom, $options])
            ->setMethods(array('getCaptionState'))
            ->getMock();

        $ruleMock->expects($this->once())
            ->method('getCaptionState')
            ->will($this->returnValue(2));

        $this->assertEquals(0, $ruleMock->check());
    }

    // public function testKaltura()
    // {
    //     $html = '<iframe id="kaltura_player_1626379517" src="https://cdnapisec.kaltura.com/p/4183983/sp/418398300/embedIframeJs/uiconf_id/48252953/partner_id/4183983?iframeembed=true&playerId=kaltura_player_1626379517&entry_id=1_qgxxsknz" width="400" height="333" allowfullscreen webkitallowfullscreen mozAllowFullScreen allow="autoplay *; fullscreen *; encrypted-media *" frameborder="0"></iframe>';
    //     $dom = new \DOMDocument('1.0', 'utf-8');
    //     $dom->loadHTML($html);

    //     $options = [
    //         'vimeoApiKey' => 'test',
    //         'youtubeApiKey' => 'test',
    //         'kalturaApiKey' => '6f6981febe62b5714eff624f52a92453',
    //         'kalturaUsername' => 'ethan@cidilabs.com'
    //     ];

    //     $rule = new VideoCaptionsMatchCourseLanguage($dom, $options);

    //     $this->assertEquals(0, $rule->check());
    // }

    
}