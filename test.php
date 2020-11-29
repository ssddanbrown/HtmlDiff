<?php
use HtmlDiff\Diff;

require __DIR__ . '/vendor/autoload.php';

$diff = Diff::excecute("<p>Hello Barry</p>\n<h1>Title</h1>", "");
dd($diff);

function dd($var) {
    var_dump($var);
    exit(0);
}