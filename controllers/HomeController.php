<?php 

class HomeController extends BaseController
{

function indexAction()
{
  // $this->app->db->indexLevel(1);
  // $room =  new DungeonRoom(1, 'dungeon');
  // $room->createDefault();
  // $room->draw();
  // die('aaa');

  $level = new Level(1);

  $level->create();
  $level->connect();

  //dump($level->sectors[0]);

  $level->draw();
  $level->sectors[0]->room->draw();
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