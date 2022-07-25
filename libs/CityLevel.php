<?php 

class CityLevel extends Level
{
  function __construct()
  {
    parent::__construct();

    $this->app->db->indexLevel($this->number);

    $this->width = 6;
    $this->height = 6;
  }

  function create()
  {
    $empty = new Room(1);
    $empty->setSize(1,1);
    $empty->clear(['floor', '', '', 'outside']);

    for ($x=0; $x < $this->width; $x++) { 
      for ($y=0; $y < $this->height; $y++)
      { 
        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);

        if ($x > 1 and $x < 4 and $y > 1 and $y < 4) {
          $room = new DungeonRoom(1);
          $room->init(5,5);
          $room->createDefault();
          $sector->add($room);
        }
        else {
          $sector->add($empty);
        }
        
      }
    }

    $this->addStairs();

    foreach ($this->sectors as $sector) {
      //$sector->create();
    }
  }

  function addStairs()
  {
  	$n = count($this->sectors);
    $this->sectors[rint(0,$n-1)]->room->addTag('stairs-down');  	
  }

}

?>