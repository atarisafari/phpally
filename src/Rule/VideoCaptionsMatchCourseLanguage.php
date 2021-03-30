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
		
		$this->debug_to_console("hello from videocaptions");

		foreach ($this->getAllElements(array('a', 'embed', 'iframe')) as $video) {
			$this->console_log($video->tagName);
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

	    /**
 * Simple helper to debug to the console
 *
 * @param $data object, array, string $data
 * @param $context string  Optional a description.
 *
 * @return string
 */
function debug_to_console($data, $context = 'Debug in Console') {

    // Buffering to solve problems frameworks, like header() in this and not a solid return.
    ob_start();

    $output  = 'console.info(\'' . $context . ':\');';
    $output .= 'console.log(' . json_encode($data) . ');';
    $output  = sprintf('<script>%s</script>', $output);

    echo $output;
}
}