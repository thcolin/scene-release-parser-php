# Scene Release Parser

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

## Tests
Use PHPUnit, there is a script to generate the json data for the tests in the folder ```/utils```, it will take the release names from the ```releases.txt``` file in the same folder. Use it to generate the data needed for the tests, but before testing, make sure all the datas generated are valid, if not this would be useless.
