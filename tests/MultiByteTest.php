<?php namespace Ssddanbrown\HtmlDiff\Tests;

use PHPUnit\Framework\TestCase;
use Ssddanbrown\HtmlDiff\Diff;

class MultiByteTest extends TestCase
{
    public function test_chinese_content_support()
    {
        $output = Diff::excecute('这个是中文内容, PHP is the bast', '这是中国语内容，PHP is the best language.');
        $this->assertEquals('这<del class="diffdel">个</del>是中<del class="diffmod">文</del><ins class="diffmod">国语</ins>内容<del class="diffmod">, </del><ins class="diffmod">，</ins>PHP is the <del class="diffmod">bast</del><ins class="diffmod">best language.</ins>', $output);
    }

    public function test_space_changes_around_multibyte_characters()
    {
        $output = Diff::excecute('test測試', 'test 測試');
        $this->assertEquals('<del class="diffmod">test測</del><ins class="diffmod">test 測</ins>試', $output);
    }
}