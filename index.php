<?php

/**
 * \file
 * PClib application template.
 */
include 'vendor/autoload.php';
include 'controllers/BaseController.php';
include 'libs/Func.php';

session_start();

$app = new PCApp('lgen');

$pclib->autoloader->addDirectory('libs');

$app->random = new Random;
$app->db = new DungeonBase('data/test.json');
$app->setLayout('tpl/website.tpl');

$app->errorHandler->unregister();

/* Nastaveni defaultniho controlleru. */
if (!$app->controller) $app->controller = 'home';

$app->run();
$app->out();

?>