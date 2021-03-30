<?php

namespace CidiLabs\PhpAlly;

use DOMElement;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Vimeo {

	private $regex = '@vimeo\.com/[^0-9]*([0-9]{7,9})@i';
    private $search_url = 'https://api.vimeo.com/videos/';
    private $client;
	private $language;
    private $api_key = 'bef37736cfb26b6dc52986d8f531d0ad';
    
    public function __construct($client, $language = 'en')
    {
        $this->client = $client;
		$this->language = $language;
    }
    
    /**
	*	Checks to see if a video is missing caption information in Vimeo
	*	@param string $link_url The URL to the video or video resource
	*   @return int 0 if captions are missing, 1 if video is private, 2 if captions exist or not a video
	*/
	function captionsMissing($link_url)
	{
		$url = $this->search_url;

		// If the API key is blank, flag the video for manual inspection
		$key_trimmed = trim($this->api_key);
		if( empty($key_trimmed) ){
			return 1;
		}

		if( $vimeo_id = $this->isVimeoVideo($link_url) ) {
			$url = $url.$vimeo_id.'/texttracks';
            $response = $this->client->request('GET', $url, ['headers' => [
                'Authorization' => "Bearer $this->api_key"
            ]]);

			$body = json_decode($response->getBody());

			// Response header code is used to determine if video exists, doesn't exist, or is unaccessible
			// 400 means a video is private, 404 means a video doesn't exist, and 200 means the video exists
			if($response->getStatusCode() === 400 || $response->getStatusCode() === 404) {
				return 1;
			} else if(($response->getStatusCode() === 200) && $body->total === 0) {
				return 0;
			}
		}

		return 2;
	}

	/**
	*	Checks to see if a video is missing caption information in YouTube
	*	@param string $link_url The URL to the video or video resource
	*	@return int 0 if captions are manual and wrong language, 1 if video is private, 2 if there are no captions or if manually generated and correct language
	*/
	function captionsLanguage($link_url)
	{
		$url = $this->search_url;
		$api_key = constant( 'VIMEO_API_KEY' );

		// If the API key is blank, flag the video for manual inspection
		$key_trimmed = trim($this->api_key);
		if( empty($key_trimmed) ){
			return 1;
		}
		// If for whatever reason course_locale is blank, set it to English
		$course_locale = $this->language;
		if($course_locale === '' || is_null($course_locale)) {
			$course_locale = 'en';
		}

		if( $vimeo_id = $this->isVimeoVideo($link_url) ) {
			$url = $url.$vimeo_id.'/texttracks';
			$response = $this->client->request('GET', $url, ['headers' => [
                'Authorization' => "Bearer $this->api_key"
			]]);
			
			$body = json_decode($response->getBody());

			// Response header code is used to determine if video exists, doesn't exist, or is unaccessible
			// 400 means a video is private, 404 means a video doesn't exist, and 200 means the video exists
			if($response->getStatusCode() === 400 || $response->getStatusCode() === 404) {
				return 1;
			} else if(($response->getStatusCode() === 200) && $body->total === 0) {
				return 2;
			}

			foreach ($body->data as $track) {
				if( substr($track->language,0,2) === $course_locale ) {
					return 2;
				}
			}

			return 0;
		}

		return 2;
	}

	/**
	*	Checks to see if the provided link URL is a Vimeo video. If so, it returns
	*	the video code, if not, it returns null.
	*	@param string $link_url The URL to the video or video resource
	*	@return mixed FALSE if it's not a Vimeo video, or a string video ID if it is
	*/
	private function isVimeoVideo($link_url)
	{
		$matches = null;
		if(preg_match($this->regex, trim($link_url), $matches)) {
			return $matches[1];
		}
		return false;
    }
}