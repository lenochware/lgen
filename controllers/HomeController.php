<?php 

class HomeController extends BaseController
{

function indexAction()
{
  //$this->app->random->seed = 1658154042;

  print "Seed: ".$this->app->random->seed . '<br>';

  $level = new Level(1);
  $level->create();
  
  return $this->template('tpl/home/level.tpl', ['map' => $level->html()]);
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



}

 ?>