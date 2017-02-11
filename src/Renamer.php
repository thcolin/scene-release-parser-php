<?php

  namespace thcolin\SceneReleaseParser;

  use Symfony\Component\Console\Application as Console;
  use Symfony\Component\Console\Input\InputInterface;

  class Renamer extends Console{

      protected function getCommandName(InputInterface $input){
        return 'renamer';
      }

      protected function getDefaultCommands(){
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new Command\RenamerCommand();

        return $defaultCommands;
      }

      public function getDefinition(){
        $inputDefinition = parent::getDefinition();
        $inputDefinition -> setArguments();

        return $inputDefinition;
      }

  }

?>
