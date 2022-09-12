<?php 

class CellarsLevel extends Level
{
  function __construct($number)
  {
    parent::__construct('cellars', $number);
  }
  
  function create()
  {
    $this->init(5,5);

    switch ($this->number)  {
      case 1: $types = ['empty', 5, 'wet', 5, 'destruct', 2, 'warehouse', 1]; break;
      case 2: $types = [ 'destruct', 5, 'warehouse', 1]; break;
      case 3: $types = [ 'destruct', 5, 'big', 5, 'warehouse', 1]; break;
      default:
        $types = ['empty', 5, 'wet', 5, 'destruct', 2, 'warehouse', 1];
    }

    $this->connect();

    //if ($this->number > 1 /* and rbet(.5)*/) $this->addTag('big-rooms');

    foreach ($this->sectors as $sector)
    {
      $type = $this->random->pick($types);

      $room = $sector->room;
      $room->init($type);
      $this->build($room);
    }

    $this->tunnel();

    $this->addExits();
    
    foreach ($this->sectors as $sector) {

      $sector->room->spread('room-floor', 'water', rint(1,5));
      $sector->room->spread('tunnel', 'water', rint(1,5));

      if ($sector->is('exit')) {
        $this->populate($sector->room, 'exit');
        continue;
      }

      $this->populate($sector->room);
    }

  }

  function addExits()
  {
    $ex = ['city', 'cellars-1', 'cellars-2', 'cellars-3'];

    $prev = $ex[$this->number-1] ?? '';
    $next = $ex[$this->number+1] ?? '';

    if ($prev) $this->addExit(rget($this->sectors)->room, ['stairs-up', $prev]);
    if ($next) $this->addExit(rget($this->sectors)->room, ['stairs-down', $next]);
  }

  // function buildDestruct($room)
  // {
  //   $room->clear(['granite-wall', '', '', 'outside']);
  //   $room->pool(1,1, ['floor','room-floor'], 8);
  //   $room->each([$room, 'createWalls'], 'room-floor');
  //   $room->setPivot();    
  // }

  function populateExit($room)
  {
    if ($room->is('stairs-down') and rbet(.5)) {
      $room->fill('room-floor', 'rubble');
    }
  }  

  function populateWet($room)
  {
    $room->spread('room-floor', 'wall-moss', rint(1,5));
    $room->spread('room-floor', rfunc('', ['wet-floor','water', 'mud']), rint(1,8));
    $room->spread('room-floor', 'frog', rint(0,1));
    $room->spread('tunnel', rfunc('', ['wet-floor']), rint(1,5));

    if (rbet(.5))
      $room->spread('water', rfunc('', ['rusty-dagger', 'copper-coins', 'dirty-rag']), rint(1,3));

  }

  function populateDestruct($room)
  {
    $room->spread('room-floor', rfunc('', ['wall','small-rock']), rint(1,5));
    $room->spread('room-wall', rfunc('', ['dirt','floor']), rint(1,5));
    $room->spread('tunnel', rfunc('', ['small-rock']), rint(0,2));
    $room->spread('room-floor', rfunc('', ['rat', 'old-bread']), rint(0,3));
  }

  function populateEmpty($room)
  {
    $room->spread('room-floor', rfunc('', ['bones', 'dirty-rag']), rint(0,1));

    if (rbet(.1)) {
      $room->spread('room-floor', 'rat', 1);
      $room->spread('room-floor', rget(['cheese', 'old-bread']) , 1);
    }
  }

  function populateWarehouse($room)
  {
    $p = $this->painter($room);
    $corners = [[0,0],[0,1],[1,0],[1,1]];

    $p->fill(0.5, 0.5, .2, .2, 'wall');

    if (rbet(.3)) $p->points($corners, rfunc('', ['leather-sandals', 'wooden-torch', 'poison', 'light-cure']));
    elseif(rbet(.7)) $p->points($corners, rfunc('', ['dirt', 'wet-floor', 'rubble']));
    else $p->points($corners, rfunc('', ['short-sword', 'rusty-dagger', 'small-shield', 'arrows']));

  }

  function painter($room)
  {
    $p = new Painter($this, $room->position());
    $p->copySize($room)->shrink(1);
    return $p;
  }  

}

?>