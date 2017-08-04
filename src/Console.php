<?php

  namespace thcolin\SceneReleaseParser;

  use Symfony\Component\Console\Application as SymfonyConsole;
  use Symfony\Component\Console\Input\InputInterface;

  class Console extends SymfonyConsole{

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
