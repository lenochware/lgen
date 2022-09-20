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
  if ($id == 'debug') {
    $seed = $this->app->getSession('seed');
    $this->seed($seed);

    $form = new pclib\Form('tpl/editor/level.tpl', 'level-form');
    $id = $form->values['level'];
  }

  if (!$id) $id = 'cellars-1';

  $level = $this->newLevel($id);
  $json = $level->toArray();

  $selected = $this->app->getSession('selected');
  if ($selected) $json['player-pos'] = $selected;

  $this->outputJson($json);
}

function saveAction($id)
{
  $this->loader->save($id);
	$this->outputJson(['status' => 'ok']);
}

function loadAction($id)
{
  $json = $this->loader->load($id);
  $this->outputJson($json);
}

function newLevel($id)
{
  [$name, $number] = explode('-', $id);

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