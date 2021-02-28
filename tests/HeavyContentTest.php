<?php namespace Ssddanbrown\HtmlDiff\Tests;

use PHPUnit\Framework\TestCase;
use Ssddanbrown\HtmlDiff\Diff;

class HeavyContentTest extends TestCase
{

    public function test_large_attribute_content()
    {
        $start = time();
        $strToEncode = '';
        for ($i = 0; $i < 10000; $i++) {
            $strToEncode .= 'cattestingstring';
        }
        $a = '<p data-test="' . base64_encode($strToEncode) . '">contnent</p>';
        $b = '<p data-test="' . base64_encode($strToEncode) . 'cat">contnent2</p>';

        $output = Diff::excecute($a, $b);
        $this->assertNotEmpty($output);
        $this->assertLessThan(3, time() - $start);
    }

}