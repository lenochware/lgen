<?php 

class CellarsLevel extends Level
{
  function create()
  {
    $this->init(5,5);
    $this->connect();

    foreach ($this->sectors as $sector)
    {
      $type = $this->random->get2(['empty', 'warehouse', 'wet', 'destruct']);

      $room = $sector->room;
      $room->init($type);
      $this->build($room);
    }

    $this->tunnel();

    $this->addExit(rget($this->sectors)->room, ['stairs-up', 'city']);
    $this->addExit(rget($this->sectors)->room, ['stairs-down', 'cellars-2']);

    foreach ($this->sectors as $sector) {
      $this->populate($sector->room);
    }

  }

  // function buildDestruct($room)
  // {
  //   $room->clear(['granite-wall', '', '', 'outside']);
  //   $room->pool(1,1, ['floor','room-floor'], 8);
  //   $room->each([$room, 'createWalls'], 'room-floor');
  //   $room->setPivot();    
  // }

  function populateWet($room)
  {
    //$room->fill('room-floor', 'water');
    $room->spread('room-floor', 'wall-moss', rint(1,5));
    $room->spread('room-floor', rfunc('', ['wet-floor','water']), rint(1,5));
    $room->spread('room-floor', 'frog', rint(0,2));
    //$room->spread('water', rfunc('', 'water-list'), rint(0,2));
    $room->spread('tunnel', rfunc('', ['wet-floor']), rint(1,5));
  }

  function populateDestruct($room)
  {
    $room->spread('room-floor', rfunc('', ['wall','small-rock']), rint(1,5));
    $room->spread('room-wall', rfunc('', ['dirt','floor']), rint(1,5));
    $room->spread('tunnel', rfunc('', ['small-rock']), rint(1,5));
    $room->spread('room-floor', 'rat', rint(1,4));
  }

  function populateEmpty($room)
  {
  }

  function populateWarehouse($room)
  {
    $room->spread('room-floor', 'copper-coins', rint(0,4));
  }  

}

?>