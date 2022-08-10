<?php 

class ApiController extends BaseController
{

function indexAction()
{
  //$this->seed(1658993076);
  $this->app->setSession('seed', $this->seed());

  $level = new DefaultLevel(1);
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