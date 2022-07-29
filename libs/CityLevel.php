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

    $this->addShops();
    $this->addTownWalls();

    $this->addStairs($this->getSector(4,0));
    $this->addRiver($this->getSector(0,2));
    $this->addForest($this->getSector(0,3));
    $this->addFountain($this->getSector(1,1));

  }

  function addShops()
  {
    foreach($this->inside as $i) {
      if (rbet(0.5)) $this->addShop($this->sectors[$i]);
    }
  }

  function addShop($sector)
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
    $p->points([[1,0.5],[1,0.45],[1,0.57]], 'door');
  }

  function getEmpty()
  {
    $empty = new Room(1);
    $empty->setSize(1,1);
    $empty->clear(['floor', '', '', 'outside']);
    return $empty;
  }

  function addStairs($sector)
  {
    $p = $this->painter($sector);
    $p->pattern([[0,0,1],[0,0,0]], 'wall');

    $room = $sector->room;
    $room->spread('floor', 'stairs-down', 1);
  }

  function addRiver($sector)
  {
    $p = $this->painter($sector);
    $p->fill(.5,.5,1,.45, 'grass');
    $p->fill(.5,.5,1,.3, 'water');

    $room = $sector->room;
    $room->spread('floor', 'grass', rint(1,10));
  }

  function addForest($sector)
  {
    $p = $this->painter($sector);

    $room = $sector->room;
    $room->spread('floor', 'grass', rint(10,30));
    $room->spread('floor', 'tree', rint(5,20));
  }

  function addFountain($sector)
  {
    $p = $this->painter($sector);
    $p->shrink(5);
    $p->fill(0.5, 0.5, .5, .5, 'water');
    $p->points([[0,0],[0,1],[1,0],[1,1]], 'wall-moss');
  }

  protected function painter(Sector $sector)
  {
    $p = new Painter($this, $sector->position());
    $p->copySize($sector);
    return $p;
  }

}

?>