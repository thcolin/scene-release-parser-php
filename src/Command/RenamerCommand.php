<?php

  namespace thcolin\SceneReleaseParser\Command;

  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputOption;
  use Symfony\Component\Console\Input\InputArgument;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  use Symfony\Component\Console\Question\Question;
  use Symfony\Component\Console\Question\ChoiceQuestion;
  use thcolin\SceneReleaseParser\Release;
  use Exception;

  class RenamerCommand extends ContainerAwareCommand{

    protected function configure(){
      $this
        -> setName('renamer')
        -> setDescription('Rename scene releases media files with "mediainfo" informations')
        -> addOption('non-verbose', null, InputOption::VALUE_NONE, "The app will not show you ignored targets")
        -> addOption('non-interactive', null, InputOption::VALUE_NONE, "The app will not ask you to correct unhandleable targets")
        -> addOption('non-invasive', null, InputOption::VALUE_NONE, "The app will not really rename targets")
        -> addOption('default-language', null, InputOption::VALUE_REQUIRED, "Default language to use")
        -> addOption('default-resolution', null, InputOption::VALUE_REQUIRED, "Default resolution to use")
        -> addOption('default-year', null, InputOption::VALUE_REQUIRED, "Default year to use")
        -> addOption('mediainfo', null, InputOption::VALUE_REQUIRED, "Mediainfo bin path")
        -> addArgument('path', InputArgument::OPTIONAL, 'Path you want to analyze (default is current working directory)', '.');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
      $verbose = !$input -> getOption('non-verbose');
      $interactive = !$input -> getOption('non-interactive');
      $invasive = !$input -> getOption('non-invasive');

      $path = realpath($input -> getArgument('path'));

      if(!$path){
        throw new Exception('Incorrect path : <options=underscore>"'.$input -> getArgument('path').'"</>');
      } else if(!is_writable($path)){
        throw new Exception('Unwritable path : <options=underscore>"'.$input -> getArgument('path').'"</> (look at permissions)');
      }

      $mediainfo = [];

      if($input -> getOption('mediainfo')){
        $mediainfo['command'] = $input -> getOption('mediainfo');
      }

      $defaults = [];

      foreach(['language', 'resolution', 'year'] as $key){
        $value = $input -> getOption('default-'.$key);

        if($value){
          $defaults[$key] = $value;
        }
      }

      $output -> write('Scanning : <options=underscore>'.$path.'</>');

      $scandir = scandir($path);
      $targets = [];
      $ignored = [];
      $results = [
        'renamed' => 0,
        'untouched' => 0,
        'errors' => 0
      ];

      foreach($scandir as $key => $filename){
        $filepath = $path.'/'.$filename;
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(in_array($filename, ['.', '..']) || substr($filename, 0, 1) === '.'){
          unset($scandir[$key]);
          continue;
        }

        if(is_file($filepath) && in_array($ext, ['mp4', 'avi', 'mkv', 'mov'])){
          $targets[$basename] = $filepath;
        } else if(is_dir($filepath)){
          $targets[$basename] = $filepath;
        } else{
          $ignored[$basename] = $filepath;
        }
      }

      $output -> write("\x0D\x1B[2K");
      $output -> writeln('Scanning : <options=underscore>'.$path.'</> ('.count($targets).'/'.count($scandir).')');

      if($verbose && count($ignored)){
        $output -> writeln('');
        $output -> writeln('<comment>Ignoring</comment> untargeted files and folders :');

        foreach($ignored as $basename => $path){
          $output -> writeln('  * '.$basename);
        }
      }

      if(!count($targets)){
        throw new Exception('No scene release target (file or folder) found !');
      }

      $output -> writeln('');

      foreach($targets as $target => $path){
        try{
          try{
            $release = Release::analyse($path, $mediainfo);
          } catch(Exception $e){
            $release = new Release($target, true, $defaults);
          }

          $reset = !$release -> getYear();
          $release -> guess();

          if($reset){
            $release -> setYear(null);
          }

          if($target === $release -> __toString()){
            if($verbose){
              $output -> writeln('<comment>Ignoring</comment> valid scene release file : <options=underscore>'.$path.'</>');
            }

            $results['untouched']++;
            continue;
          }

          $question = new Question('Rename <fg=red>'.$target.'</> to <info>'.$release -> __toString().'</info> ? [Y/n'.($interactive ? '/c':'').'] ', 'Y');
          $answer = $this -> getHelper('question') -> ask($input, $output, $question);

          if(in_array($answer, ['n', 'N'])){
            $results['untouched']++;
            continue;
          } else if(in_array($answer, ['c', 'C']) && $interactive){
            $release = $this -> correct($input, $output, $release);
          } else {
            $output -> write(str_repeat("\x1B[1A\x1B[2K", 1));
          }
        } catch(Exception $e){
          if($interactive){
            $output -> writeln('<comment>Unhandleable</comment> target which need manual corrections : <options=underscore>'.$target.'</>');
            $release = new Release($target, false, $defaults);
            $reset = !$release -> getYear();
            $release -> guess();

            if($reset){
              $release -> setYear(null);
            }

            $release = $this -> correct($input, $output, $release);
          } else{
            $results['untouched']++;
            continue;
          }
        }

        if(!is_writable($path)){
          $output -> writeln('Target <fg=red>'.$target.'</> can\'t be renamed because of permissions');
          $results['errors']++;
        } else{
          $output -> writeln('Target <fg=red>'.$target.'</> renamed to <info>'.$release -> __toString().'</info>');

          if($invasive){
            $filepath = dirname($path).'/'.$release -> __toString().(pathinfo($path, PATHINFO_EXTENSION) ? '.'.pathinfo($path, PATHINFO_EXTENSION) : '');
            rename($path, $filepath);
          }

          $results['renamed']++;
        }
      }

      $output -> writeln('');
      $output -> writeln('Results :');
      $output -> writeln('  * <options=bold>'.$results['renamed'].'</> targets <fg=green>renamed</>');
      $output -> writeln('  * <options=bold>'.$results['untouched'].'</> targets <comment>untouched</comment>');
      $output -> writeln('  * <options=bold>'.$results['errors'].'</> targets thrown <comment>errors</comment>');
    }

    protected function correct(InputInterface $input, OutputInterface $output, Release $release){
      $done = false;

      do{
        $output->writeln('');
        $output->writeln('Release : <fg=cyan>'.$release -> __toString().'</> :');

        $choices = [
          'title' => ($release -> getTitle() ? $release -> getTitle() : '<comment>null</comment>'),
          'year' => ($release -> getYear() ? $release -> getYear() : '<comment>null</comment>'),
          'language' => ($release -> getLanguage() ? $release -> getLanguage() : '<comment>null</comment>'),
          'resolution' => ($release -> getResolution() ? $release -> getResolution() : '<comment>null</comment>'),
          'source' => ($release -> getSource() ? $release -> getSource() : '<comment>null</comment>'),
          'dub' => ($release -> getDub() ? $release -> getDub() : '<comment>null</comment>'),
          'encoding' => ($release -> getEncoding() ? $release -> getEncoding() : '<comment>null</comment>'),
          'group' => ($release -> getGroup() ? $release -> getGroup() : '<comment>null</comment>'),
          'season' => ($release -> getSeason() ? $release -> getSeason() : '<comment>null</comment>'),
          'episode' => ($release -> getEpisode() ? $release -> getEpisode() : '<comment>null</comment>')
        ];

        $question = new ChoiceQuestion('Which property do you want to edit ? ', $choices);
        $question -> setValidator(function($answer) use ($choices){
          if(!$answer){
            return null;
          }

          if(!in_array($answer, array_keys($choices))){
            throw new Exception('Select a valid property to edit in : '.implode(', ', array_keys($choices)));
          }

          return $answer;
        });
        $answer = $this->getHelper('question')->ask($input, $output, $question);

        switch($answer){
          case 'title':
            $question = new Question('<question>Replace old title :</question> ');
            $question -> setAutocompleterValues([$release -> getTitle()]);
            $question -> setValidator(function($answer){
              if(strlen($answer) === 0){
                throw new Exception('A title is expected');
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setTitle($value);
          break;
          case 'year':
            $question = new Question('<question>Replace old year :</question> ');
            $question -> setAutocompleterValues([$release -> getYear()]);
            $question -> setValidator(function($answer){
              if(!$answer){
                return null;
              }

              $answer = intval($answer);

              if($answer < 1900){
                throw new Exception('The year should be a 4 digit number');
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setYear($value);
          break;
          case 'language':
            $values = array_keys(Release::$languageStatic);
            $question = new Question('<question>Replace old language :</question> ');
            $question -> setAutocompleterValues($values);
            $question -> setValidator(function($answer) use ($values){
              if(!$answer){
                return null;
              }

              $answer = strtoupper($answer);

              if(!in_array($answer, $values)){
                throw new Exception('The language should be one of : '.implode(', ', $values));
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setLanguage($value);
          break;
          case 'resolution':
            $values = array_keys(Release::$resolutionStatic);
            $question = new ChoiceQuestion('<question>Select resolution :</question> ', $values);
            $question -> setAutocompleterValues($values);
            $question -> setValidator(function($answer) use ($values){
              if(!$answer){
                return null;
              }

              if(!in_array($answer, $values)){
                throw new Exception('The resolution should be one of : '.implode(', ', $values));
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setResolution($value);
          break;
          case 'source':
            $values = array_keys(Release::$sourceStatic);
            $question = new ChoiceQuestion('<question>Select source :</question> ', $values);
            $question -> setAutocompleterValues($values);
            $question -> setValidator(function($answer) use ($values){
              if(!$answer){
                return null;
              }

              if(!in_array($answer, $values)){
                throw new Exception('The source should be one of : '.implode(', ', $values));
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setSource($value);
          break;
          case 'dub':
            $values = array_keys(Release::$dubStatic);
            $question = new ChoiceQuestion('<question>Select dub :</question> ', $values);
            $question -> setAutocompleterValues($values);
            $question -> setValidator(function($answer) use ($values){
              if(!$answer){
                return null;
              }

              if(!in_array($answer, $values)){
                throw new Exception('The dub should be one of : '.implode(', ', $values));
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setDub($value);
          break;
          case 'encoding':
            $values = array_keys(Release::$encodingStatic);
            $question = new ChoiceQuestion('<question>Select encoding :</question> ', $values);
            $question -> setAutocompleterValues($values);
            $question -> setValidator(function($answer) use ($values){
              if(!$answer){
                return null;
              }

              if(!in_array($answer, $values)){
                throw new Exception('The encoding should be one of : '.implode(', ', $values));
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setEncoding($value);
          break;
          case 'group':
            $question = new Question('<question>Replace old group :</question> ');
            $question -> setAutocompleterValues([$release -> getGroup()]);
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setGroup($value);
          break;
          case 'season':
            $question = new Question('<question>Replace old season # :</question> ');
            $question -> setAutocompleterValues([$release -> getSeason()]);
            $question -> setValidator(function($answer){
              if(!$answer){
                return null;
              }

              $answer = intval($answer);

              if($answer === 0){
                throw new Exception('The season should be a number');
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setSeason($value);
          break;
          case 'episode':
            $question = new Question('<question>Replace old episode # :</question> ');
            $question -> setAutocompleterValues([$release -> getEpisode()]);
            $question -> setValidator(function($answer){
              if(!$answer){
                return null;
              }

              $answer = intval($answer);

              if($answer === 0){
                throw new Exception('The episode should be a number');
              }

              return $answer;
            });
            $value = $this->getHelper('question')->ask($input, $output, $question);
            $release -> setEpisode($value);
          break;
          default:
            $done = true;
          break;
        }
      } while(!$done);

      $output -> writeln('');

      return $release;
    }

  }

?>
