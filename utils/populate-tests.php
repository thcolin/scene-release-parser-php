<?php

	require __DIR__.'/../vendor/autoload.php';

	use thcolin\SceneReleaseParser\Release;

	$content = file_get_contents(__DIR__.'/releases.txt');
	$elements = [];

	foreach(explode("\n", $content) as $release){
		$release = utf8_encode($release);
		$Release = new Release($release);

		$element = [
			'title' => utf8_encode($Release -> getTitle()),
			'type' => utf8_encode($Release -> getType())
		];

		if($year = $Release -> getYear()){
			$element['year'] = utf8_encode($year);
		} else{
			$element['guess']['year'] = $Release -> guessYear();
		}

		if($language = $Release -> getLanguage()){
			$element['language'] = utf8_encode($language);
		} else{
			$element['guess']['language'] = $Release -> guessLanguage();
		}

		if($resolution = $Release -> getResolution()){
			$element['resolution'] = utf8_encode($resolution);
		} else{
			$element['guess']['resolution'] = $Release -> guessResolution();
		}

		if($source = $Release -> getSource()){
			$element['source'] = utf8_encode($source);
		}

		if($dub = $Release -> getDub()){
			$element['dub'] = utf8_encode($dub);
		}

		if($encoding = $Release -> getEncoding()){
			$element['encoding'] = utf8_encode($encoding);
		}

		if($group = $Release -> getGroup()){
			$element['group'] = utf8_encode($group);
		}

		if($flags = $Release -> getFlags()){
			$element['flags'] = $flags;
		}

		if($season = $Release -> getSeason()){
			$element['season'] = intval($season);
		}

		if($episode = $Release -> getEpisode()){
			$element['episode'] = intval($episode);
		}

		$elements[$release] = $element;
	}

	file_put_contents(__DIR__.'/releases.json', json_encode($elements, JSON_PRETTY_PRINT));

?>
