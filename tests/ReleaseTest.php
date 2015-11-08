<?php
	
	use thcolin\SceneReleaseParser\Release;
	
	class ReleaseTest extends PHPUnit_Framework_TestCase{
		
		public function setUp(){
			
			$json = file_get_contents(__DIR__.'/../utils/releases.json');
			$array = json_decode($json, true);
			
			foreach($array as $key => $element){
				
				$element['object'] = new Release($key);
				$this -> elements[] = $element;
				
			}
			
		}
		
		public function testGetType(){
			
			foreach($this -> elements as $element)
				
				$this -> assertEquals($element['type'], $element['object'] -> getType());
			
		}
		
		public function testGetTitle(){
			
			foreach($this -> elements as $element)
				
				$this -> assertEquals($element['title'], $element['object'] -> getTitle());
			
		}
		
		public function testGetYear(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['year']))
				
					$this -> assertEquals($element['year'], $element['object'] -> getYear());
				
			}
			
		}
		
		public function testGetLanguage(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['language']))
				
					$this -> assertEquals($element['language'], $element['object'] -> getLanguage());
				
			}
			
		}
		
		public function testGetResolution(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['resolution']))
				
					$this -> assertEquals($element['resolution'], $element['object'] -> getResolution());
				
			}
			
		}
		
		public function testGetSource(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['source']))
				
					$this -> assertEquals($element['source'], $element['object'] -> getSource());
				
			}
			
		}
		
		public function testGetDub(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['dub']))
				
					$this -> assertEquals($element['dub'], $element['object'] -> getDub());
				
			}
			
		}
		
		public function testGetEncoding(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['encoding']))
				
					$this -> assertEquals($element['encoding'], $element['object'] -> getEncoding());
				
			}
			
		}
		
		public function testGetGroup(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['group']))
				
					$this -> assertEquals($element['group'], $element['object'] -> getGroup());
				
			}
			
		}
		
		public function testGetFlags(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['flags']))
				
					$this -> assertEquals($element['flags'], $element['object'] -> getFlags());
				
			}
			
		}
		
		public function testGetSeason(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['season']))
				
					$this -> assertEquals($element['season'], $element['object'] -> getSeason());
				
			}
			
		}
		
		public function testGetEpisode(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['episode']))
				
					$this -> assertEquals($element['episode'], $element['object'] -> getEpisode());
				
			}
			
		}
		
		public function testGuess(){
			
			foreach($this -> elements as $element){
				
				if(isset($element['guess'])){
					
					foreach($element['guess'] as $guess => $value){
						
						switch($guess){
							
							case 'year':
								$this -> assertEquals($value, $element['object'] -> guessYear());
							break;
							
							case 'language':
								$this -> assertEquals($value, $element['object'] -> guessLanguage());
							break;
							
							case 'resolution':
								$this -> assertEquals($value, $element['object'] -> guessResolution());
							break;
							
						}
						
					}
					
				}
				
			}
			
		}
		
	}
	
?>