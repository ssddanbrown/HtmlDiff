# PHP HtmlDiff

[![Build Status](https://github.com/ssddanbrown/htmldiff/workflows/phpunit/badge.svg)](https://github.com/ssddanbrown/htmldiff/actions)

This library will compare two provided HTML string inputs and output HTML, tagged with the differences between the two.

This is a PHP port of the [c# implementation found here](https://github.com/Rohland/htmldiff.net) which is a port of the [ruby implementation found here](https://github.com/myobie/htmldiff). Major credit to [@Rohland](https://github.com/Rohland) and [@myobie](https://github.com/myobie) for their original work which I have just simply translated to PHP.

## Installation

You can install the package via composer:

```bash
composer require ssddanbrown/htmldiff
```

## Usage

The library provides a direct static function to quickly create a HTML diff:

```php
$diff = Ssddanbrown\HtmlDiff\Diff::excecute('<p>Hello there!</p>', '<p>Hi there!</p>');
// $diff = '<p><del class="diffmod">Hello</del><ins class="diffmod">Hi</ins> there!</p>';
```

Alternatively, You can instead create an instance of the `Diff` class to configure a few options first:

```php
use Ssddanbrown\HtmlDiff\Diff;

$diff = new Diff('<p>Hello there!</p>', '<p>Hi there!</p>');
$diff->repeatingWordsAccuracy = 1;
$diff->ignoreWhitespaceDifferences = false;
$diff->orphanMatchThreshold = 0.2;

$output = $diff->build();
```

## Options

#### $diff->repeatingWordsAccuracy

Number, Defaults to `1`. Valid values are from 0 to 1.

Defines how to compare repeating words. This value allows to exclude some words from comparison that eventually reduces the total time of the diff algorithm. 0 means that all words are excluded so the diff will not find any matching words at all. 1 (default value) means that all words participate in comparison so this is the most accurate case. 0.5 means that any word that occurs more than 50% times may be excluded from comparison. This doesn't mean that such words will definitely be excluded but only gives a permission to exclude them if necessary.

#### $diff->ignoreWhitespaceDifferences

Boolean value. Defaults to `true`.

If set to true all whitespace characters, including `&nbsp;`, are treated as being equal.

#### $diff->orphanMatchThreshold

Number, Defaults to `0`. Valid values are from 0 to 1.

If some match is too small and located far from its neighbors then it is considered as orphan and removed. This property defines relative size of the match to be considered as orphan, from 0 to 1. 1 means that all matches will be considered as orphans. 0 (default) means that no match will be considered as orphan. 0.2 means that if match length is less than 20% of distance between its neighbors it is considered as orphan.

#### $diff->addBlockExpression($expression)

Add a regular expression that will be used to 'group' text together so that it's treated as a single block. 

The `$expression` passed to the method must be a valid PHP regex pattern string.

## Development

This package is built to very closely follow the code and structure of the [c#](https://github.com/Rohland/htmldiff.net) library that it was originally ported from. It will remain closely aligned for this original release but may deviate in the future. 

## License

This project, and the projects that this library has been ported from, is licensed under the MIT License. See the [license file](https://github.com/ssddanbrown/htmldiff/blob/master/license.md) for more info.

