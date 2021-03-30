<?php

namespace CidiLabs\PhpAlly\Rule;

use DOMElement;
use CidiLabs\PhpAlly;
use CidiLabs\PhpAlly\Vimeo;
use CidiLabs\PhpAlly\Youtube;
use GuzzleHttp\Client;

/**
*	Videos with manual captions should have some that match the course language
*/
class VideoCaptionsMatchCourseLanguage extends BaseRule
{
    
    public function id()
    {
        return self::class;
    }

    public function check()
    {
        $search_youtube = '/(youtube|youtu.be)/';
		$search_vimeo = '/(vimeo)/';

		$this->console_log("testing");

		foreach ($this->getAllElements(array('a', 'embed', 'iframe')) as $video) {
			$this->console_log($video);
			$attr = ($video->tagName == 'a') ? 'href' : 'src';
			if ($video->hasAttribute($attr)) {
				$attr_val = $video->getAttribute($attr);
				if ( preg_match($search_youtube, $attr_val) ) {
					$service = new Youtube(new Client());
				}
				elseif ( preg_match($search_vimeo, $attr_val) ) {
					$service = new Vimeo(new Client());
				}
				if (isset($service)) {
                    $captionState = $service->captionsLanguage($attr_val);
					if($captionState != 2) {
						$this->setIssue($video);
					}
				}
			}
		}
        
        return count($this->issues);
    }

	function console_log($output, $with_script_tags = true) {
		$js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
	');';
		if ($with_script_tags) {
			$js_code = '<script>' . $js_code . '</script>';
		}
		echo $js_code;
	}
}