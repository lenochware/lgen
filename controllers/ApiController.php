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
  $id = 'cellars-1'; //testing

  $this->app->setSession('seed', $this->seed());

  switch($id) {
  	case 'city': $level = new CityLevel(1); break;
  	case 'cellars-1': $level = new CellarsLevel(1); break;
  	case 'cellars-2': $level = new CellarsLevel(2); break;
  	default: throw new Exception('Level not found.');
  }

  $level->create();
  $this->outputJson($level->toArray());
}

}

 ?>