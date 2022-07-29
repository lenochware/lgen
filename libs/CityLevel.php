<?php 

class CityLevel extends Level
{
  protected $inside = [];
  protected $outside = [];

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
        if ($x == 0 or $y == 0 or $x == $this->width - 1 or $y == $this->height - 1) {
          $this->outside[] = count($this->sectors);
        }
        else {
          $this->inside[] = count($this->sectors);
        }

        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);
        $room = $this->getEmpty();
        $sector->add($room);
      }
    }

    $this->addStairs();
    $this->addShops();
    $this->addTownWalls();

  }

  function addShops()
  {
    foreach($this->inside as $i) {
      if (rbet(0.5)) $this->addShop($this->sectors[$i]);
    }
  }

  function addShop(Sector $sector)
  {
    $room = new DungeonRoom(1);
    $room->init(rint([5,8]), rint([5,8]));
    $sector->add($room);
    $room->createShop();
  }

  function addTownWalls()
  {
    $p = new Painter($this, $this->getSector(1,1)->position());
    $p->x = $p->y = 0;
    $p->width = ($this->width-2) * $this->sectorWidth;
    $p->height = ($this->height-2) * $this->sectorHeight;

    $p->rect(0.5, 0.5, 1, 1, rfunc('i2',['wall', 'wall-moss']));
    $p->fill(0.8, 0.8, .05, .05, 'tree');
    $p->points([[1,0.5],[1,0.45],[1,0.57]], 'door');   
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
    $i = rget($this->outside);
    $sector = $this->sectors[$i];

    $p = $this->painter($sector);
    $p->pattern([[0,0,1],[0,0,0]], 'wall');

    $room = $sector->room;
    $room->spread('floor', 'stairs-down', 1);

  }

  protected function painter(Sector $sector)
  {
    $p = new Painter($this, $sector->position());
    $p->copySize($sector);
    return $p;
  }

}

?>