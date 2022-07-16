<?php 

class HomeController extends BaseController
{

function indexAction()
{
  $this->app->random->seed = 1657871118;

  print "Seed: ".$this->app->random->seed . '<br>';

  $level = new Level(1);
  $level->create();
  $level->draw();
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


  $level->draw();
}



}

 ?>