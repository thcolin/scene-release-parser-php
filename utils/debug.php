<?php

  require __DIR__.'/../vendor/autoload.php';

  use thcolin\SceneReleaseParser\Release;

  $Release = new Release($argv[1]);
  $Release->generated = $Release->__toString();
  var_dump($Release);
