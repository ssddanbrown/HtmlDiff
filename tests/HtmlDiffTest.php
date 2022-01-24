<?php namespace Ssddanbrown\HtmlDiff\Tests;

use Ssddanbrown\HtmlDiff\Diff;
use PHPUnit\Framework\TestCase;

class HtmlDiffTest extends TestCase
{
    public function test_empty_new_and_old_provides_empty_result()
    {
        $output = Diff::excecute('', '');
        $this->assertEquals('', $output);
    }

    public function test_it_diffs_text()
    {
        $output = Diff::excecute('a word is here', 'a nother word is there');
        $this->assertEquals('a<ins class="diffins">&nbsp;nother</ins> word is <del class="diffmod">here</del><ins class="diffmod">there</ins>', $output);
    }

    public function test_it_should_insert_a_letter_and_a_space()
    {
        $output = Diff::excecute('a c', 'a b c');
        $this->assertEquals('a <ins class="diffins">b </ins>c', $output);
    }

    public function test_it_should_remove_a_letter_and_a_space()
    {
        $output = Diff::excecute('a b c', 'a c');
        $this->assertEquals('a <del class="diffdel">b </del>c', $output);
    }

    public function test_it_should_change_a_letter()
    {
        $output = Diff::excecute('a b c', 'a d c');
        $this->assertEquals('a <del class="diffmod">b</del><ins class="diffmod">d</ins> c', $output);
    }



    public function test_it_should_support_img_tags_insertion()
    {
        $output = Diff::excecute('a b c', 'a b <img src="some_url" /> c');
        $this->assertEquals('a b <ins class="diffins"><img src="some_url" /> </ins>c', $output);
    }

    public function test_it_should_support_img_tags_deletion()
    {
        $output = Diff::excecute('a b <img src="some_url" /> c', 'a b c');
        $this->assertEquals('a b <del class="diffdel"><img src="some_url" /> </del>c', $output);
    }

    public function test_it_highlights_tag_only_changes()
    {
        $output = Diff::excecute('Some plain text', 'Some <strong><i>plain</i></strong> text');
        $this->assertEquals('Some <strong><i><ins class="mod">plain</ins></i></strong> text', $output);

        $output = Diff::excecute('Some <strong><i>formatted</i></strong> text', 'Some formatted text');
        $this->assertEquals('Some <ins class="mod">formatted</ins> text', $output);
    }

    public function test_blocksize_algorithm_works_as_expected()
    {
        $output = Diff::excecute(
            'This is a longer piece of text to ensure the new blocksize algorithm works',
            'This is a longer piece of text to <strong>ensure</strong> the new blocksize algorithm works decently'
        );
        $this->assertEquals('This is a longer piece of text to <strong><ins class="mod">ensure</ins></strong> the new blocksize algorithm works<ins class="diffins">&nbsp;decently</ins>', $output);
    }

    public function test_entities_are_kept_as_separate_words()
    {
        $output = Diff::excecute('entities are separate amp;words','entities are&nbsp;separate &amp;words');
        $this->assertEquals('entities are&nbsp;separate <del class="diffmod">amp;</del><ins class="diffmod">&amp;</ins>words', $output);
    }

    public function test_whitespace_differences_are_ignored_by_default()
    {
        $output = Diff::excecute('tom jerry','tom&nbsp;jerry');
        $this->assertEquals('tom&nbsp;jerry', $output);
    }

    public function test_whitespace_differences_option_shows_differences_in_output()
    {
        $diff = new Diff('tom jerry','tom&nbsp;jerry');
        $diff->ignoreWhitespaceDifferences = false;
        $output = $diff->build();
        $this->assertEquals('tom<del class="diffmod">&nbsp;</del><ins class="diffmod">&nbsp;</ins>jerry', $output);
    }

    public function test_orphan_match_threshold_changes_output_as_expected()
    {
        $diff = new Diff('one a word is somewhere', 'two a nother word is somewhere');
        $diff->orphanMatchThreshold = 0.2;
        $output = $diff->build();

        $this->assertEquals('<del class="diffmod">one a</del><ins class="diffmod">two a nother</ins> word is somewhere', $output);

        $diff = new Diff('one a word is somewhere', 'two a nother word is somewhere');
        $diff->orphanMatchThreshold = 0.1;
        $output = $diff->build();

        $this->assertEquals('<del class="diffmod">one</del><ins class="diffmod">two</ins> a<ins class="diffins">&nbsp;nother</ins> word is somewhere', $output);
    }

    public function test_custom_block_grouping_expressions_are_correctly_used()
    {
        $diff = new Diff('This is a date 1 Jan 2016 that will change', 'This is a date 22 Feb 2017 that did change');
        $diff->addBlockExpression('/[\d]{1,2}[\s]*(Jan|Feb)[\s]*[\d]{4}/');
        $output = $diff->build();

        $this->assertEquals('This is a date<del class="diffmod"> 1 Jan 2016</del><ins class="diffmod"> 22 Feb 2017</ins> that <del class="diffmod">will</del><ins class="diffmod">did</ins> change', $output);
    }
}