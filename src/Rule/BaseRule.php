<?php

namespace CidiLabs\PhpAlly\Rule;

use CidiLabs\PhpAlly\PhpAllyIssue;
use CidiLabs\PhpAlly\PhpAllyRuleInterface;
use CidiLabs\PhpAlly\HtmlElements;
use DOMDocument;
use DOMElement;

class BaseRule implements PhpAllyRuleInterface {

    protected $dom;
    protected $previewHtml = '';
    protected $css = '';
    protected $issues = [];
    protected $errors = [];
    protected $lang;
    protected $strings = array('en' => '');

    const ALT_TEXT_LENGTH_LIMIT = 125;
    const DOC_LENGTH = 1500;
    const MAX_WORD_COUNT = 3000;

    protected $altTextLengthLimit;
    protected $minDocLengthForHeaders;
    protected $maxWordCount;

    public function __construct(DOMDocument $dom, $options = [])
    {
        $this->dom = $dom;
        $this->options = $options;
        $this->lang = isset($options['lang']) ? $options['lang'] : 'en';
        $this->css = isset($options['css']) ? $options['css'] : '';

        $this->altTextLengthLimit = isset($options['altTextLengthLimit']) 
            ? $options['alttextLengthLimit'] : self::ALT_TEXT_LENGTH_LIMIT;
        $this->minDocLengthForHeaders = isset($options['minDocLengthForHeaders']) 
            ? $options['minDocLengthForHeaders'] : self::DOC_LENGTH;
        $this->maxWordCount = isset($options['maxWordCount']) 
            ? $options['maxWordCount'] : self::MAX_WORD_COUNT;
    }

    public function id()
    {
        return self::class;
    }

    public function getCss()
    {
        return $this->css;
    }

    public function setCss($css)
    {
        $this->css = $css;
    }

    public function check()
    {
        return true;
    }

    public function getPreviewElement(DOMElement $elem = null)
    {
        return null;
    }

    public function getAllElements($tags = null, $options = false, $value = true) {
		if(!is_array($tags)) {
            $tags = array($tags);
        }

		if($options !== false) {
			$temp = new htmlElements();
			$tags = $temp->getElementsByOption($options, $value);
		}
		$result = array();

		if(!is_array($tags)) {
            return array();
        }

		foreach($tags as $tag) {
			$elements = $this->dom->getElementsByTagName($tag);
			if($elements) {
				foreach($elements as $element) {
					$result[] = $element;
				}
			}
        }
        
		if(count($result) == 0) {
            return array();
        }

		return $result;
	}

    public function elementContainsReadableText($element)
    {
        if (is_a($element, 'DOMText')) {
            if (trim($element->wholeText) != '') {
                return true;
            }
        } else {
            if (trim($element->nodeValue) != '' ||
                ($element->hasAttribute('alt') && trim($element->getAttribute('alt')) != '')
            ) {
                return true;
            }
            if (method_exists($element, 'hasChildNodes') && $element->hasChildNodes()) {
                foreach ($element->childNodes as $child) {
                    if ($this->elementContainsReadableText($child)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function setIssue($element)
    {
        $ruleId = str_replace('CidiLabs\\PhpAlly\\Rule\\', '', $this->id());

        if ($element) {
            $elementClasses = $element->getAttribute('class');
            if ($elementClasses && (strpos($elementClasses, 'phpally-ignore') !== false)) {
                return;
            }
        }

        $this->issues[] = new PhpAllyIssue($ruleId, $element, $this->getPreviewElement($element));
    }

    public function getIssues()
    {
        return $this->issues;
    }

    public function setError($errorMsg) 
    {
        $this->errors[] = $errorMsg;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
	*	Returns a translated variable. If the translation is unavailable, English is returned
	*	Because tests only really have one string array, we can get all of this info locally
	*	@return mixed The translation for the object
	*/
    public function translation() {
        if(isset($this->strings[$this->lang])) {
			return $this->strings[$this->lang];
		}
		if(isset($this->strings['en'])) {
			return $this->strings['en'];
		}
		return false;
    }

    public function setLanguage($language) {
        $this->lang = $language;
    }

    /**
	*	To minimize notices, this compares an object's property to the value
	*	and returns true or false. False will also be returned if the object is
	*	not really an object, or if the property doesn't exist at all
	*	@param object $object The object too look at
	*	@param string $property The name of the property
	*	@param mixed $value The value to check against
	*	@param bool $trim Whether the property value should be trimmed
	*	@param bool $lower Whether the property value should be compared on lower case
	**/
	function propertyIsEqual($object, $property, $value, $trim = false, $lower = false) {
		if(!is_object($object)) {
			return false;
		}
		if(!property_exists($object, $property)) {
			return false;
		}
		$property_value = $object->$property;
		if($trim) {
			$property_value = trim($property_value);
			$value = trim($value);
		}
		if($lower) {
			$property_value = strtolower($property_value);
			$value = strtolower($value);
		}
		return ($property_value == $value);
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