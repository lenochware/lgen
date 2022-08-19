<?php 

class DefaultLevel extends Level
{
  function create()
  {
    $this->init(5,5);
    $this->connect();

    foreach ($this->sectors as $sector)
    {
      //$biom = $this->random->get(['dungeon', 'forest', 'rocks', 'desert', 'water', 'hell']);
      $type = $this->random->get(['destruct', 'pit', 'treasure', 'wet']);

      $room = $sector->room;
      $room->init($type);
      $this->build($room);
    }

    $this->tunnel();

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
    $room->fill('room-floor', 'water');
    $room->spread('room-floor', 'wall-moss', rint(1,5));
    $room->spread('room-floor', rfunc('', ['wet-floor','floor']), rint(1,5));
    $room->spread('water', rfunc('', 'water-list'), rint(0,2));
    $room->spread('tunnel', rfunc('', ['wet-floor']), rint(1,5));
  }  

}

?>