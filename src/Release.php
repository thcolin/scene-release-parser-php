<?php

	namespace thcolin\SceneReleaseParser;

	use Exception;

	class Release{

		const MOVIE = 'movie';
		const TVSHOW = 'tvshow';

		const ORIGINAL_RELEASE = 1;
		const GENERATED_RELEASE = 2;

		protected static $sourceStatic = [
			'DVDRip' => [
				'dvdrip',
				'dvd-rip'
			],
			'DVDScr' => [
				'dvdscr',
				'dvd-scr',
				'dvdscreener'
			],
			'WEB-DL' => [
				'webtv',
				'web-tv',
				'webdl',
				'web-dl',
				'webrip',
				'webhd',
				'web'
			],
			'BRRip' => [
				'brrip',
				'br-rip'
			],
			'BDRip' => [
				'bdrip',
				'bd-rip'
			],
			'DVD-R' => [
				'dvd',
				'dvd-r'
			],
			'R5' => [
				'r5'
			],
			'HDRip' => [
				'hdtv',
				'hdtvrip',
				'hdtv-rip',
				'hdrip',
				'hdlight',
				'mhd',
				'hd'
			],
			'BLURAY' => [
				'bluray',
				'blu-ray'
			],
			'PDTV' => [
				'pdtv'
			],
			'SDTV' => [
				'sdtv'
			]
		];

		protected static $encodingStatic = [
			'DivX' => [
				'divx'
			],
			'XviD' => [
				'xvid'
			],
			'x264' => [
				'x264',
				'x.264'
			],
			'h264' => [
				'h264',
				'h.264'
			]
		];

		protected static $resolutionStatic = [
			'SD' => [
				'sd'
			],
			'720p' => [
				'720p'
			],
			'1080p' => [
				'1080p'
			]
		];

		protected static $dubStatic = [
			'DUBBED' => [
				'dubbed'
			],
			'AC3' => [
				'ac3.dubbed',
				'ac3'
			],
			'MD' => [
				'md',
				'microdubbed',
				'micro-dubbed'
			],
			'LD' => [
				'ld',
				'linedubbed',
				'line-dubbed'
			]
		];

		protected static $languageStatic = [
			'FRENCH' => [
				'FRENCH',
				'FR'
			],
			'TRUEFRENCH' => 'TRUEFRENCH',
			'VOSTFR' => 'VOSTFR',
			'SUBFRENCH' => 'SUBFRENCH',
			'PERSIAN' => 'PERSIAN',
			'AMHARIC' => 'AMHARIC',
			'ARABIC' => 'ARABIC',
			'CAMBODIAN' => 'CAMBODIAN',
			'CHINESE' => 'CHINESE',
			'CREOLE' => 'CREOLE',
			'DANISH' => 'DANISH',
			'DUTCH' => 'DUTCH',
			'ENGLISH' => 'ENGLISH',
			'ESTONIAN' => 'ESTONIAN',
			'FILIPINO' => 'FILIPINO',
			'FINNISH' => 'FINNISH',
			'FLEMISH' => 'FLEMISH',
			'GERMAN' => 'GERMAN',
			'GREEK' => 'GREEK',
			'HEBREW' => 'HEBREW',
			'INDONESIAN' => 'INDONESIAN',
			'IRISH' => 'IRISH',
			'ITALIAN' => 'ITALIAN',
			'JAPANESE' => 'JAPANESE',
			'KOREAN' => 'KOREAN',
			'LAOTIAN' => 'LAOTIAN',
			'LATVIAN' => 'LATVIAN',
			'LITHUANIAN' => 'LITHUANIAN',
			'MALAY' => 'MALAY',
			'MALAYSIAN' => 'MALAYSIAN',
			'MAORI' => 'MAORI',
			'NORWEGIAN' => 'NORWEGIAN',
			'PASHTO' => 'PASHTO',
			'POLISH' => 'POLISH',
			'PORTUGUESE' => 'PORTUGUESE',
			'ROMANIAN' => 'ROMANIAN',
			'RUSSIAN' => 'RUSSIAN',
			'SPANISH' => 'SPANISH',
			'SWAHILI' => 'SWAHILI',
			'SWEDISH' => 'SWEDISH',
			'SWISS' => 'SWISS',
			'TAGALOG' => 'TAGALOG',
			'TAJIK' => 'TAJIK',
			'THAI' => 'THAI',
			'TURKISH' => 'TURKISH',
			'UKRAINIAN' => 'UKRAINIAN',
			'VIETNAMESE' => 'VIETNAMESE',
			'WELSH' => 'WELSH',
			'MULTI' => 'MULTI'
		];

		protected static $flagsStatic = [
			'PROPER' => 'PROPER',
			'FASTSUB' => 'FASTSUB',
			'LIMITED' => 'LIMITED',
			'SUBFORCED' => 'SUBFORCED',
			'LIMITED' => 'LIMITED',
			'EXTENDED' => 'EXTENDED',
			'THEATRICAL' => 'THEATRICAL',
			'WORKPRINT' => 'WORKPRINT',
			'FANSUB' => 'FANSUB',
			'REPACK' => 'REPACK',
			'UNRATED' => 'UNRATED',
			'NFOFIX' => 'NFOFIX',
			'NTSC' => 'NTSC',
			'PAL' => 'PAL',
			'INTERNAL' => 'INTERNAL',
			'FESTIVAL' => 'FESTIVAL',
			'STV' => 'STV',
			'LIMITED' => 'LIMITED',
			'RERIP' => 'RERIP',
			'RETAIL' => 'RETAIL',
			'REMASTERED' => 'REMASTERED',
			'UNRATED' => 'UNRATED',
			'RATED' => 'RATED',
			'CHRONO' => 'CHRONO',
			'HDLIGHT' => 'HDLIGHT',
			'UNCUT' => 'UNCUT',
			'UNCENSORED' => 'UNCENSORED',
			'DUBBED' => 'DUBBED',
			'SUBBED' => 'SUBBED',
			'DUAL' => 'DUAL',
			'FINAL' => 'FINAL',
			'COLORIZED' => 'COLORIZED',
			'DOLBY DIGITAL' => 'DOLBY DIGITAL',
			'DTS' => 'DTS',
			'AAC' => 'AAC',
			'DTS-HD' => 'DTS-HD',
			'DTS-MA' => 'DTS-MA',
			'TRUEHD' => 'TRUEHD',
			'3D' => '3D',
			'HSBS' => 'HSBS',
			'DOC' => 'DOC',
			'RERIP' => [
				'rerip',
				're-rip'
			],
			'DD5.1' => [
				'dd5.1',
				'dd51',
				'dd5-1',
				'5.1',
				'5-1'
			],
			'READNFO' => [
				'READ.NFO',
				'READ-NFO',
				'READNFO'
			]
		];

		protected $release;
		protected $type;
		protected $title;
		protected $year;
		protected $language;
		protected $resolution;
		protected $source;
		protected $dub;
		protected $encoding;
		protected $group;
		protected $flags;

		protected $season;
		protected $episode;

		public function __construct($release){
			$this -> release = $release;

		// Clean
			$this -> release = str_replace(['[', ']', '(', ')', ',', ';', ':', '!'], '', $this -> release);
			$this -> release = preg_replace('#[\s]+#', ' ', $this -> release);
			$this -> release = str_replace(' ', '.', $this -> release);
			$this -> title = $this -> release;

			// Positions
			$this -> positions = explode('.', $this -> release);

			// SOURCE, ENCODING, RESOLUTION, DUB, LANGUAGE (unique)
			foreach(['source', 'encoding', 'resolution', 'dub', 'language'] as $attribute){
				$attributes = $attribute.'Static';

				foreach(Release::$$attributes as $key => $patterns){
					if(!is_array($patterns)){
						$patterns = [$patterns];
					}

					foreach($patterns as $pattern){
						$this -> title = preg_replace('#[\.|\-]'.preg_quote($pattern).'([\.|\-])?#i', '$1', $this -> title, 1, $replacements);
						if($replacements > 0){
							$this -> $attribute = $key;
						}
					}

					if(isset($this -> $attribute)){
						break;
					}
				}
			}

			// YEAR
			$this -> title = preg_replace_callback('#[\.|\-](\d{4})([\.|\-])?#', function($matches){
				$this -> year = $matches[1];
				return (isset($matches[2]) ? $matches[2]:'');
			}, $this -> title, 1);

			// FLAGS (multiple)
			foreach(Release::$flagsStatic as $key => $patterns){
				if(!is_array($patterns)){
					$patterns = [$patterns];
				}

				foreach($patterns as $pattern){
					$this -> title = preg_replace('#[\.|\-]'.preg_quote($pattern).'([\.|\-])?#i', '$1', $this -> title, 1, $replacements);
					if($replacements > 0){
						$this -> flags[] = $key;
					}
				}
			}

			// TYPE
			$this -> title = preg_replace_callback('#[\.\-]S(\d+)[\.\-]?(E(\d+))?([\.\-])#i', function($matches){
				$this -> type = self::TVSHOW;
				// 01 -> 1 (numeric)
				$this -> season = intval($matches[1]);

				if($matches[3]){
					$this -> episode = intval($matches[3]);
				}
				return $matches[4];
			}, $this -> title, 1, $count);

			if($count == 0){

				// Not a Release
				if(
					!$this -> resolution &&
					!$this -> source &&
					!$this -> dub &&
					!$this -> encoding
				){
					throw new Exception('The string "'.$this -> release.'" is not a correct Scene Release name');
				}

				// movie
				$this -> type = self::MOVIE;
			}

			// GROUP
			$this -> title = preg_replace_callback('#\-([a-zA-Z0-9_\.]+)$#', function($matches){
				if(strlen($matches[1]) > 12){
					preg_match('#(\w+)#', $matches[1], $matches);
				}
				$this -> group = preg_replace('#^\.+|\.+$#', '', $matches[1]);
				return '';
			}, $this -> title);

			// Create and clean title by positions
			$title = [];
			$this -> title = preg_replace('#\.+#', '.', $this -> title);
			foreach(array_intersect($this -> positions, explode('.', $this -> title)) as $key => $value){
				$last = isset($last) ? $last:0;

				if($key - $last > 1){
					$this -> title = implode(' ', $title);
					break;
				}

				$title[] = $value;
				$this -> title = implode(' ', $title);
				$last = $key;
			}

			// TITLE
			$this -> title = ucwords(strtolower($this -> title));
			$this -> title = trim($this -> title);
		}

		public function __toString(){
			$arrays = [];
			foreach([
				$this -> getTitle(),
				$this -> getYear(),
				($this -> getSeason() ? 'S'.sprintf('%02d', $this -> getSeason()):'').
				($this -> getEpisode() ? 'E'.sprintf('%02d', $this -> getEpisode()):''),
				$this -> getLanguage(),
				$this -> getResolution(),
				$this -> getSource(),
				$this -> getEncoding(),
				$this -> getDub()
			] as $array){
				if(is_array($array)){
					$arrays[] = implode('.', $array);
				} else if($array){
					$arrays[] = $array;
				}
			}
			return preg_replace('#\s+#', '.', implode('.', $arrays)).'-'.($this -> getGroup() ? $this -> getGroup():'NOTEAM');
		}

		public function getRelease($mode = self::ORIGINAL_RELEASE){
			switch($mode){
				case self::GENERATED_RELEASE:
					return $this -> __toString();
				break;
				default:
					return $this -> release;
				break;
			}
			return $this -> release;
		}

		public function getType(){
			return $this -> type;
		}

		public function getTitle(){
			return $this -> title;
		}

		public function getSeason(){
			return $this -> season;
		}

		public function getEpisode(){
			return $this -> episode;
		}

		public function getLanguage(){
			return $this -> language;
		}

		public function guessLanguage(){
			return 'VO';
		}

		public function getResolution(){
			return $this -> resolution;
		}

		public function guessResolution(){
			return 'SD';
		}

		public function getSource(){
			return $this -> source;
		}

		public function getDub(){
			return $this -> dub;
		}

		public function getEncoding(){
			return $this -> encoding;
		}

		public function getYear(){
			return $this -> year;
		}

		public function guessYear(){
			return date('Y');
		}

		public function getGroup(){
			return $this -> group;
		}

		public function getFlags(){
			return $this -> flags;
		}

		public function guess(){
			if(!isset($this -> year)){
				$this -> year = $this -> guessYear();
			}

			if(!isset($this -> resolution)){
				$this -> resolution = $this -> guessResolution();
			}

			if(!isset($this -> language)){
				$this -> language = $this -> guessLanguage();
			}

			return $this;
		}

	}

?>
