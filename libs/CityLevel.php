<?php 

class CityLevel extends Level
{
  function __construct()
  {
    parent::__construct();

    $this->app->db->indexLevel($this->number);

    $this->width = 5;
    $this->height = 5;
  }

  function create()
  {
    for ($x=0; $x < $this->width; $x++) { 
      for ($y=0; $y < $this->height; $y++)
      { 
        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);

        if ($x > 0 and $x < 4 and $y > 0 and $y < 4) {
          $room = new DungeonRoom(1);
          $room->init(5,5);
          $room->createShop();
        }
        else {
          $room = $this->getEmpty();
        }

        $sector->add($room);
      }
    }

    $p = new Painter($this, $this->getSector(0,0)->position());
    $p->x = $p->y = 0;
    $p->width = $this->width * $this->sectorWidth;
    $p->height = $this->height * $this->sectorHeight;

    $p->rect(0.5, 0.5, .75, .75, 'tree');

    $this->addStairs();
  }

  function getEmpty()
  {
    $empty = new Room(1);
    $empty->setSize(1,1);
    $empty->clear(['floor', '', '', 'outside']);
    return $empty;
  }

  function addStairs()
  {
    $i = rint(0, $this->width * $this->height - 1);
    $room = $this->sectors[$i]->room;
    $room->spread('floor', 'stairs-down', 1);

  }

}

?>