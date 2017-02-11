<?php

  namespace thcolin\SceneReleaseParser\Command;

  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputArgument;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  use thcolin\SceneReleaseParser\Release;
  use Exception;

  class RenamerCommand extends ContainerAwareCommand{

    protected function configure(){
      $this
        -> setName('renamer')
        -> setDescription('Rename scene releases with "mediainfo" informations')
        -> addArgument('path', InputArgument::REQUIRED, 'Path of your scene releases/movies you want to rename');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
      $path = realpath($input -> getArgument('path'));

      if(!$path){
        throw new Exception('Incorrect path');
      }

      $scandir = scandir($path);
      foreach($scandir as $filename){
        $file = $path.'/'.$filename;
        $ext = substr($filename, -3, 3);
        if(in_array($ext, ['mp4', 'avi', 'mkv'])){
          try{
            $release = new Release(substr($filename, 0, -4));
            if($filename != $release -> __toString().'.'.$ext){
              rename($file, dirname($file).'/'.$release -> __toString().'.'.$ext);
              $output -> writeln('<comment>'.$filename.'</comment> renamed to <info>'.$release -> __toString().'.'.$ext.'</info>');
            } else{
              $output -> writeln('<error>'.$filename.'</error> already renamed to "correct" scene release name : <comment>ignored</comment>');
            }
          } catch(Exception $e){
            $exceptions[] = $filename;
          }
        }
      }

      if(count($exceptions)){
        throw new Exception("Invalid release name :\n".implode("\n", $exceptions));
      }
    }

  }

?>
