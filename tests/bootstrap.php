<?php

use \Charcoal\Admin\AdminModule as AdminModule;
use \Charcoal\Charcoal as Charcoal;

session_start();

// Composer autoloader for Charcoal's psr4-compliant Unit Tests
$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->add('Charcoal\\Admin\\', __DIR__.'/../src/');
$autoloader->add('Charcoal\\Admin\\Tests\\', __DIR__);


// This var needs to be set automatically, for now
Charcoal::init();
$admin_module = new AdminModule();
//Charcoal::config()['ROOT'] = '';
