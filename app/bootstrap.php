<?php

require_once __DIR__.'/../vendor/autoload.php'; 

$app = new Silex\Application();

use Knp\Provider\ConsoleServiceProvider;

$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'MyApplication',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__.'/..'
));


return $app;