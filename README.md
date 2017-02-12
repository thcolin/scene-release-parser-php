# Scene Release Parser

[![Build Status](https://travis-ci.org/thcolin/scene-release-parser.svg?branch=master)](https://travis-ci.org/thcolin/scene-release-parser)
[![Code Climate](https://codeclimate.com/github/thcolin/scene-release-parser/badges/gpa.svg)](https://codeclimate.com/github/thcolin/scene-release-parser)
[![Test Coverage](https://codeclimate.com/github/thcolin/scene-release-parser/badges/coverage.svg)](https://codeclimate.com/github/thcolin/scene-release-parser/coverage)

PHP Library to parse scene release names to get their tags and title (original from [majestixx/scene-release-parser-php-lib](https://github.com/majestixx/scene-release-parser-php-lib)).

The library contains one classe, **thcolin\SceneReleaseParser\Release**, the constructor will try to extract all the tags from the Release name and creates an object with easy access to all information, remaining parts will construct the title of the media (movie or tv show).

I added another class : **thcolin\SceneReleaseParser\Parser**, constructed with a **Mhor\MediaInfo\MediaInfo** object. It can ```parse``` a file to a ```Release``` by his path. It add unavailable informations with **MediaInfo** about the file.

## Installation
Install with composer :
```
composer require thcolin/scene-release-parser dev-master
```

## Release Example :
Create a new object with the release name and retrieve all the tags and the name :
```php
use thcolin\SceneReleaseParser\Release;

$Release = new Release("Mr.Robot.S01E05.PROPER.VOSTFR.720p.WEB-DL.DD5.1.H264-ARK01");

// TYPE
echo($Release -> getType()); // tvshow (::TVSHOW)

// TITLE
echo($Release -> getTitle()); // Mr Robot

// LANGUAGE
echo($Release -> getLanguage()); // VOSTFR

// YEAR
echo($Release -> getYear()); // null (no tag in the release name)
echo($Release -> guessYear()); // 2015 (year of the system)

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
Unknown informations can be guessed :
* Year is guessed with the year of the system
* Resolution can be guessed with the source of the Release
* Language return 'VO' if there is no tag

## Parser Example :
Parse a file to a Release and correct resolution, encoding and language with mediainfo informations :
```php
use thcolin\SceneReleaseParser\Parser;
use thcolin\SceneReleaseParser\Release;
use Mhor\MediaInfo\MediaInfo;

$MediaInfo = new MediaInfo();
$Parser = new Parser($MediaInfo);

$Parser -> setDefaultLanguage('FRENCH');
$Release = $Parser -> parse('/home/downloads/Bataille a Seattle BDRip FR.avi');

// RELEASE
echo($Release -> getRelease(Release::GENERATED_RELEASE)): // Bataille.A.Seattle.FRENCH.720p.BDRip.x264-NOTEAM

// RESOLUTION
echo($Release -> getResolution()); // 720p

// ENCODING
echo($Release -> getEncoding()); // x264

// LANGUAGE
echo($Release -> getLanguage()); // FRENCH (default)
```

## Bin
Inside of `bin` folder, you got a `scene-release-renamer` executable to rename video file with approximately good scene release name.

### How to use
Rename all the video files (avi, mp4, mkv) of the path :
```
php bin/scene-release-renamer <path>
```

### Exemple :
| Original | Generated |
| -------- | --------- |
| Benjamin Button [x264] [HD 720p] [LUCN] [FR].mp4 | Benjamin.Button.FRENCH.720p.HDRip.x264-NOTEAM.mp4 |
| Jamais entre amis (2015) [1080p] MULTI (VFQ-VOA) Bluray x264 AC3-PopHD (Sleeping with Other People).mkv | Jamais.Entre.Amis.2015.MULTI.1080p.BLURAY.x264.AC3-PopHD.mkv |
| La Vie rêvée de Walter Mitty [1080p] MULTi 2013 BluRay x264-Pop (The Secret Life Of Walter Mitty) .mkv | La.Vie.Re?ve?e.De.Walter.Mitty.2013.MULTI.1080p.BLURAY.x264-Pop.mkv |
| Le Nouveau Stagiaire (2015) The Intern - Multi 1080p - x264 AAC 5.1 - CCATS.mkv | Le.Nouveau.Stagiaire.2015.MULTI.1080p.x264-CCATS.mkv |
| Le prestige (2006) (The Prestige) 720p x264 AAC 5.1 MULTI [NOEX].mkv | Le.Prestige.2006.MULTI.720p.x264-NOTEAM.mkv |
| Les 4 Fantastiques 2015 Truefrench 720p x264 AAC PIXEL.mp4 | Les.4.Fantastiques.2015.TRUEFRENCH.720p.x264-NOTEAM.mp4 |
| One.For.the.Money.2012.1080p.HDrip.French.x264 (by kimo).mkv | One.For.The.Money.2012.FRENCH.1080p.HDRip.x264-NOTEAM.mkv |
| Tower Heist [1080p] MULTI 2011 BluRay x264-Pop  .Le casse De Central Park. .mkv | Tower.Heist.2011.MULTI.1080p.BLURAY.x264-Pop.mkv |

## Tests
Use PHPUnit, there is a script to generate the json data for the tests in the folder ```/utils```, it will take the release names from the ```releases.txt``` file in the same folder. Use it to generate the data needed for the tests, but before testing, make sure all the datas generated are valid, if not this would be useless.

## Bugs
* The Shawshank Redemption (1994) MULTi (VFQ-VO-VFF) 1080p BluRay x264-PopHD  (Les Évadés) - The.Shawshank.1994.MULTI.1080p.BLURAY.x264-NOTEAM
* La ligne Verte (1999) MULTi-VF2 [1080p] BluRay x264-PopHD (The Green Mile) - La.Ligne.1999.MULTI.1080p.BLURAY.x264-PopHD

## TODO
* Refacto `README`
* Review (before possible refacto) `Parser` class
* Add tests on `Renamer`, `Command/RenamerCommand` and `bin/scene-release-renamer`
* Check if by default the `Release` class `guess()` unknowns informations when `__toString` is called
  * And use `mediainfo` to get unknowns informations
* Refacto `Command/RenamerCommand` :
  * Use current path by default
  * By default, ask the user if he want rename each file
    * Enable/Disable with an option (like `--skip-X`)
    * Allow the user to change `Release` informations
  * Display a message and don't throw an exception at the end
* Resolve CodeCoverage issues
<!-- * Up to date ! -->
