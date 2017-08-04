# Scene Release Parser

[![Build Status](https://travis-ci.org/thcolin/scene-release-parser.svg?branch=master)](https://travis-ci.org/thcolin/scene-release-parser)
[![Code Climate](https://codeclimate.com/github/thcolin/scene-release-parser/badges/gpa.svg)](https://codeclimate.com/github/thcolin/scene-release-parser)
[![Test Coverage](https://codeclimate.com/github/thcolin/scene-release-parser/badges/coverage.svg)](https://codeclimate.com/github/thcolin/scene-release-parser/coverage)

PHP Library parsing a scene release name to retrieve title and tags (original from [majestixx/scene-release-parser-php-lib](https://github.com/majestixx/scene-release-parser-php-lib)).

The library is made of one class : `thcolin\SceneReleaseParser\Release`, the constructor will try to extract all the tags from the release name and creates `getters` for each one, remaining parts will construct the title of the media (movie or tv show).

## Installation
Install with composer :
```
composer require thcolin/scene-release-parser dev-master
```

## Release Example :
Easiest way to start using the lib is to instantiating a new `Release` object with a scene release name as first argument, it will retrieve all the tags and the name :

```php
use thcolin\SceneReleaseParser\Release;

// Optionals arguments
$strict = true; // if no tags found, it will throw an exception
$defaults = []; // defaults values for : language, resolution and year

$Release = new Release("Mr.Robot.S01E05.PROPER.VOSTFR.720p.WEB-DL.DD5.1.H264-ARK01", $strict, $defaults);

// TYPE
echo($Release -> getType()); // tvshow (::TVSHOW)

// TITLE
echo($Release -> getTitle()); // Mr Robot

// LANGUAGE
echo($Release -> getLanguage()); // VOSTFR

// YEAR
echo($Release -> getYear()); // null (no tag in the release name)
echo($Release -> guessYear()); // 2015 (year of the system)
echo($Release -> guess() -> getYear()); // 2015 (year of the system)

// RESOLUTION
echo($Release -> getResolution()); // 720p

// SOURCE
echo($Release -> getSource()); // WEB

// DUB
echo($Release -> getDub()); // null (no tag in the release name)

// ENCODING
echo($Release -> getEncoding()); // h264

// GROUP
echo($Release -> getGroup()); // ARK01

// FLAGS
print_r($Release -> getFlags()); // [PROPER, DD5.1]

// SCORE
echo($Release -> getScore()); // 7 (bigger is better, max : 7)

// ONLY TVSHOW
echo($Release -> getSeason()); // 1
echo($Release -> getEpisode()); // 5
```

## Guess
Unknown informations of a current `Release` can be guessed :

```php
use thcolin\SceneReleaseParser\Release;

$Release = new Release("Bataille a Seattle BDRip", false, [
  'language' => 'FRENCH' // default to Release::LANGUAGE_DEFAULT (VO)
]);

$Release -> guess();

// LANGUAGE
echo($Release -> guessLanguage()); // FRENCH

// RESOLUTION
echo($Release -> guessResolution()); // SD

// YEAR
echo($Release -> guessYear()); // 2017 (current year)
```

## Analyze
For best results, you can directly analyze a `file`, the method will use `mediainfo` :

```php
use thcolin\SceneReleaseParser\Release;

// Mhor\MediaInfo::setConfig arguments (default to empty)
$mediainfo = [
  // Optional, just for example
  'command' => '/usr/local/bin/mediainfo'
];

$Release = Release::analyze('/home/downloads/Bataille a Seattle BDRip.avi', $mediainfo);

// RELEASE
echo($Release -> getRelease(Release::GENERATED_RELEASE)): // Bataille.A.Seattle.FRENCH.720p.BDRip.x264-NOTEAM

// RESOLUTION
echo($Release -> getResolution()); // 720p

// ENCODING
echo($Release -> getEncoding()); // x264

// LANGUAGE
echo($Release -> getLanguage()); // FRENCH
```

## Bin
Inside `bin` folder, you got a `scene-release-renamer` executable, which require a `<path>` argument (default to current working directory). It will scan `<path>`, searching for video files (avi, mp4, mkv, mov) and folders to rename (if dirty) with valid generated scene release name. Scene release name will be constructed with current file name and `mediainfo` parsed informations (if available). If errors comes up, you'll be able to fix them manually.

### Usage
```
php bin/scene-release-renamer <path> [--non-verbose] [--non-interactive] [--non-invasive] [--mediainfo=/usr/local/bin/mediainfo] [--default-(language|resolution|year)=value]
```

### Results :
| Original | Generated |
| -------- | --------- |
| Benjamin Button [x264] [HD 720p] [LUCN] [FR].mp4 | Benjamin.Button.FRENCH.720p.HDRip.x264-NOTEAM.mp4 |
| Jamais entre amis (2015) [1080p] MULTI (VFQ-VOA) Bluray x264 AC3-PopHD (Sleeping with Other People).mkv | Jamais.Entre.Amis.2015.MULTI.1080p.BLURAY.x264.AC3-PopHD.mkv |
| La Vie rêvée de Walter Mitty [1080p] MULTi 2013 BluRay x264-Pop (The Secret Life Of Walter Mitty) .mkv | La.Vie.Rêvée.De.Walter.Mitty.2013.MULTI.1080p.BLURAY.x264-Pop.mkv |
| Le Nouveau Stagiaire (2015) The Intern - Multi 1080p - x264 AAC 5.1 - CCATS.mkv | Le.Nouveau.Stagiaire.2015.MULTI.1080p.x264-CCATS.mkv |
| Le prestige (2006) (The Prestige) 720p x264 AAC 5.1 MULTI [NOEX].mkv | Le.Prestige.2006.MULTI.720p.x264-NOTEAM.mkv |
| Les 4 Fantastiques 2015 Truefrench 720p x264 AAC PIXEL.mp4 | Les.4.Fantastiques.2015.TRUEFRENCH.720p.x264-NOTEAM.mp4 |
| One.For.the.Money.2012.1080p.HDrip.French.x264 (by kimo).mkv | One.For.The.Money.2012.FRENCH.1080p.HDRip.x264-NOTEAM.mkv |
| Tower Heist [1080p] MULTI 2011 BluRay x264-Pop  .Le casse De Central Park. .mkv | Tower.Heist.2011.MULTI.1080p.BLURAY.x264-Pop.mkv |

## Tests
Use PHPUnit, there is a script to generate the json data for the tests in the folder `/utils`, it will take the release names from the `releases.txt` file in the same folder. Use it to generate the data needed for the tests, but before testing, make sure all the datas generated are valid, if not this would be useless.

## Bugs
| Original | Generated |
| -------- | --------- |
| The Shawshank Redemption (1994) MULTi (VFQ-VO-VFF) 1080p BluRay x264-PopHD  (Les Évadés) | The.Shawshank.1994.MULTI.1080p.BLURAY.x264-NOTEAM |
| La ligne Verte (1999) MULTi-VF2 [1080p] BluRay x264-PopHD (The Green Mile) | La.Ligne.1999.MULTI.1080p.BLURAY.x264-PopHD |

## TODO
* `Release->guessResolution()` should consider `$Release->source`
* Add `Release::LANGUAGE_*` constants
  * Use them in `ReleaseTest`
  * And `README ## Guess`
* Add `boolean $flags` for `Release::__toString`
  * implement option in `Release::getRelease` too
  * if `true` will add `Release->flags` to generated release name
* Resolve CodeCoverage issues
<!-- * Up to date ! -->
