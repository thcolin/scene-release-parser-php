<?php

  namespace thcolin\SceneReleaseParser;

  use Mhor\MediaInfo\MediaInfo;
  use thcolin\SceneReleaseParser\Release;

  use Exception;
  use InvalidArgumentException;

  class Parser{

    protected $mediainfo;
    protected $defaultLanguage = Release::LANGUAGE_DEFAULT;

    public function __construct(MediaInfo $mediainfo){
      $this -> mediainfo = $mediainfo;
    }

    public function parse($filepath){
      if(!is_file($filepath)){
        throw new Exception("File '".$filepath."' not found");
      }

      $basename = pathinfo($filepath, PATHINFO_FILENAME);
      $release = new Release($basename);
      $container = $this -> mediainfo -> getInfo($filepath);

      foreach($container -> getVideos() as $video){
        // CODEC
        if(!$release -> getEncoding()){
          if($codec = $video -> get('codec_cc')){
            switch($codec){
              case 'DIVX':
                $release -> setEncoding(Release::ENCODING_DIVX);
              break;
              case 'XVID':
                $release -> setEncoding(Release::ENCODING_XVID);
              break;
            }
          } else if($codec = $video -> get('encoded_library_name')){
            switch($codec){
              case 'x264':
                $release -> setEncoding(Release::ENCODING_X264);
              break;
              case 'x265':
                $release -> setEncoding(Release::ENCODING_X265);
              break;
            }
          } else if($codec = $video -> get('internet_media_type')){
            switch($codec){
              case 'video/H264':
                $release -> setEncoding(Release::ENCODING_H264);
              break;
            }
          }
        }

        // RESOLUTION
        if(!$release -> getResolution()){
          $height = $video -> get('height') -> getAbsoluteValue();
          $width = $video -> get('width') -> getAbsoluteValue();

          if($height >= 1000 || $width >= 1900){
            $release -> setResolution(Release::RESOLUTION_1080P);
          } else if($height >= 700 || $width >= 1200){
            $release -> setResolution(Release::RESOLUTION_720P);
          }
        }
      }

      // LANGUAGE
      $audios = $container -> getAudios();

      if(!$release -> getLanguage()){
        if(count($audios) > 1){
          $release -> setLanguage(Release::LANGUAGE_MULTI);
        } else{
          $languages = $audios[0] -> get('language');
          if($languages && $languages[1] != 'English'){
            $release -> setLanguage(strtoupper($languages[1]));
          } else{
            $release -> setLanguage($this -> getDefaultLanguage());
          }
        }
      }

      return $release;
    }

    public function getDefaultLanguage(){
      return $this -> defaultLanguage;
    }

    public function setDefaultLanguage($language){
      foreach(Release::$languageStatic as $default => $languages){
        $languages = (is_array($languages) ? $languages:[$languages]);

        if(in_array($language, $languages) || $language == $default){
          $this -> defaultLanguage = $default;
          return $this;
        }
      }

      throw new InvalidArgumentException('Check Release::languageStatic for valid argument');
    }

  }

?>
