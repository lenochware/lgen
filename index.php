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
$app->db = new DungeonBase('data');
$app->setLayout('tpl/website.tpl');

/* Nastaveni defaultniho controlleru. */
if (!$app->controller) $app->controller = 'editor';

$app->run();
$app->out();

?>