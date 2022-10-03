<?php 

class CityLevel extends Level
{
  function create()
  {
  	$this->init(5,5);

    for ($x=0; $x < $this->width; $x++) {
      for ($y=0; $y < $this->height; $y++)
      { 
        $sector = $this->getSector($x, $y);
        $sector->room->clear(['floor', '', '', 'outside']);

        if ($x==1 and $y==1) {
          $this->addShop($sector);
          continue;
        }

        if ($x==4 and $y==0) {
          $this->addDungeonEntrace($sector);
          continue;
        }

        if ($x==0 and $y==2 and rbet(0.3)) {
          $this->addRiver($sector);
          continue;
        }

        if ($this->isOutside($x, $y)) $this->addOutside($sector);
        else $this->addInside($sector);

      }
    }

    $this->addTownWalls();
  }

  function isOutside($x, $y)
  {
    return ($x == 0 or $y == 0 or $x == $this->width - 1 or $y == $this->height - 1);
  }

  function addOutside($sector)
  {
    if (rbet(0.5)) return;
    $this->addForest($sector);
  }

  function addInside($sector)
  {
    if (rbet(0.5)) return;
    if (rbet(0.1)) { $this->addFountain($sector); return; }
    $this->addShop($sector);
  }

  function addShop($sector)
  {
    $sector->room->init('shop');
    $sector->room->rectangleLayout();
    
    $sector->room->fill('outside', rfunc(['dirt','floor'], 'i2'));
    $p = new Painter($this, $sector->position());
    $p->copySize($sector->room);
    $p->points([[0,0.5]], 'door');
  }

  function addTownWalls()
  {
    $p = new Painter($this, $this->getSector(1,1)->position());
    $p->x = $p->y = 0;
    $p->width = ($this->width-2) * $this->sectorWidth;
    $p->height = ($this->height-2) * $this->sectorHeight;

    $p->rect(0.5, 0.5, 1, 1, rfunc(['wall', 'wall-moss'], 'i2'));
    $p->points([[1,0.5],[1,0.45],[1,0.57]], 'door');
  }

  function addDungeonEntrace($sector)
  {
    $p = $this->painter($sector);
    $p->fill(0,0,1,1,'room-floor');
    $p->pattern([[0,0,1],[0,0,0]], 'wall');

    $this->addExit($sector->room, ['stairs-down', 'cellars-1']);
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