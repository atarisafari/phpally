<?php

use CidiLabs\PhpAlly\PhpAlly;
use CidiLabs\PhpAlly\PhpAllyIssue;

class PhpAllyTest extends PhpAllyTestCase {
    public function testCheckOne()
    {
        $ally = new PhpAlly();
        $report = $ally->checkOne($this->getLinkHtml(), 'AnchorMustContainText');
        $issues = $report->getIssues();
        $issue = reset($issues);

        $this->phpAllyReportTest($report);
        $this->phpAllyIssueTest($issue);
    }

    public function testGetRuleIds() {
        $ally = new PhpAlly();
        $rules = $ally->getRuleIds();
        // print_r($rules);
    }

    public function testCheckMany() 
    {
        $ally = new PhpAlly();
        $html = '<p>
        <a href="https://www.youtube.com/watch?v=MJ4DtLnTPvY" target="_blank" rel="noopener">https://www.youtube.com/watch?v=MJ4DtLnTPvY</a>
    </p>
    <p>&nbsp;</p>
    <p>
        <a href="https://www.youtube.com/watch?v=vFF0uV9AOB8" target="_blank" rel="noopener">https://www.youtube.com/watch?v=vFF0uV9AOB8</a>
    </p>
    <p>
        <a href="https://www.youtube.com/watch?v=vFF0uV9AOB8" target="_blank" rel="noopener">https://www.youtube.com/watch?v=vFF0uV9AOB8</a>
    </p>
    <p>&nbsp;</p>
    <p>
        <a href="https://www.youtube.com/watch?v=QJMaAVAUpIs" target="_blank" rel="noopener">https://www.youtube.com/watch?v=QJMaAVAUpIs</a>
    </p>
    <p>These are some cool videos that I enjoy.&nbsp;</p>
    <p>&nbsp;</p>
    <p>hiiiiiiiiiii</p>';
        $report = $ally->checkMany($html, $ally->getRuleIds());
        $issues = $report->getIssues();
        // print_r($issues);
        // $issue = reset($issues);

        $this->phpAllyReportTest($report);
        $this->phpAllyIssueTest($issue);
    }

    
    protected function phpAllyReportTest($report)
    {
        $issues = $report->getIssues();
        $this->assertCount(1, $issues, 'AnchorMustContainText test has two issues.');

        $this->assertCount(0, $report->getErrors(), 'AnchorMustContainText test has no errors');
        $report->setError('Testing error');
        $this->assertCount(1, $report->getErrors(), 'Report now has an error.');
    }

    protected function phpAllyIssueTest(PhpAllyIssue $issue)
    {
        $this->assertEquals(PhpAllyIssue::class, get_class($issue), 'Issue is a PhpAllyIssue object.');
        $this->assertEquals(DOMElement::class, get_class($issue->getElement()), 'Issue returns a DomElement with getElement()');
        $this->assertEquals(DOMElement::class, get_class($issue->getPreviewElement()), 'Issue return DomElement for getPreviewElement()');
    }

}