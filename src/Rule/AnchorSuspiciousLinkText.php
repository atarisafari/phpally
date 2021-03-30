<?php

namespace CidiLabs\PhpAlly\Rule;

use DOMElement;

/**
*  Suspicious link text.
*  a (anchor) element cannot contain any of the following text (English): \"click here\""
*	@link http://quail-lib.org/test-info/aSuspiciousLinkText
*/
class AnchorSuspiciousLinkText extends BaseRule
{
    
    var $strings = array('en' => array('click here', 'click', 'more', 'here'),
    'es' => array('clic aqu&iacute;', 'clic', 'haga clic', 'm&aacute;s', 'aqu&iacute;'));
    
    public function id()
    {
        return self::class;
    }

    public function check()
    {
        $this->debug_to_console("hello from anchor sus");
        foreach ($this->getAllElements('a') as $a) {
            if (in_array(strtolower(trim($a->nodeValue)), $this->translation()) || $a->nodeValue == $a->getAttribute('href'))
				$this->setIssue($a);
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