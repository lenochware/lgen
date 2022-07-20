<?php 

class HomeController extends BaseController
{

function indexAction()
{
  //$this->seed(1658154042);
  $this->app->setSession('seed', $this->seed());

  print "Seed: ". $this->seed() . '<br>';

  $level = new Level(1);
  $level->create();

  return $this->template('tpl/home/level.tpl', ['map' => $level->html()]);
}

function seed($value = null)
{
  if ($value) $this->app->random->seed = $value;
  return $this->app->random->seed;
}


function testAction()
{
  $level = new Level(1);

  $level->width = 2;
  $level->height = 2;

  $level->create();

  $room =  new DungeonRoom(1, 'dungeon');
  $room->init(8,8);
  $level->getSector(0,0)->add($room);

  $room->createTreasure();
  $room->pattern([[0,1],[1,0]], 'water');


  return $level->html();
}

function infoAction($x, $y)
{
  $this->seed($this->app->getSession('seed'));

  $level = new Level(1);
  $level->create();

  $sx = floor($x / $level->sectorWidth);
  $sy = floor($y / $level->sectorHeight);
  $sector = $level->getSector($sx, $sy);

  $html = "Sector ($sx,$sy)<br>Room " .$sector->room->__toString();

  return $html;
}

}

 ?>