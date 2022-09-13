<?php 

class ApiController extends BaseController
{
  protected $loader;

function __construct($app) {
  parent::__construct($app);
  $this->loader = new Loader('data');
}

function objectsAction()
{
  $objects = $this->loader->loadObjects();
  $this->outputJson($objects);
}

function templatesAction()
{
  $templates = $this->loader->loadTemplates();
  $this->outputJson($templates);  
}

function levelAction($id)
{
  $level = $this->newLevel();
  $this->outputJson($level->toArray());
}

function newLevel()
{
  $seed = $this->app->getSession('seed');
  $this->seed($seed);

  $form = new pclib\Form('tpl/home/level.tpl', 'level-form');
  [$name, $number] = explode('-', $form->values['level'] ?: 'cellars-1');

  if ($name == 'cellars')
    $level = new CellarsLevel($number);
  elseif ($name == 'city')  
    $level = new CityLevel('city', $number);
  else
    throw new Exception('Unknown level.');
  
  $level->create();
  return $level;  
}


}

 ?>