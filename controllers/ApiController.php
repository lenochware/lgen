<?php 

class ApiController extends BaseController
{

function indexAction($id)
{
  //$this->seed(1658993076);
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

function cityAction()
{
  //$this->seed(1659099490);
  $this->app->setSession('seed', $this->seed());

  print "Seed: ". $this->seed() . '<br>';

  $level = new CityLevel();
  $level->create();

  return $this->template('tpl/home/level.tpl', ['map' => $level->html()]);
}



}

 ?>