<?php 

class HomeController extends BaseController
{

function indexAction()
{
  // $this->app->db->indexLevel(1);
  // $room =  new DungeonRoom(1, 'dungeon');
  // $room->setSize(8,8);
  // $room->createDefault();

  // $room->draw();
  // die('aaa');

  //$this->app->random->seed = 19;

  print "Seed: ".$this->app->random->seed . '<br>';


  $level = new Level(1);

  $level->create();

  $r1 = $level->getSector(0,0)->room;
  $r2 = $level->getSector(0,1)->room;

  //$level->draw();

  $r1->draw();
  $r2->draw();

  die('end.');
}


function testAction()
{
  $db = $this->app->db;
  $db->indexLevel(1);
  dump($db->index);

}


}

 ?>