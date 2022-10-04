<?php 

class CellarsLevel extends Level
{
  function __construct($number)
  {
    parent::__construct('cellars', $number);
  }

  function initLevel()
  {
    $this->init(5,5);

    if ($this->number == 1)  {
      $types = ['empty', 5, 'wet', 1, 'destruct', 1, 'warehouse', 1];
    } else {
      $types = ['empty', 5, 'wet', 2, 'destruct', 5, 'warehouse', 1];
    }

    $this->config['room-types'] = $types;

    if ($this->number > 1 and rbet(.1) ) $this->addTag('wet-level');

    if ($this->number == 3) {
      $room = rget($this->sectors)->room;
      $room->addTag('boss');
      $room->type = 'boss';
    };

  }
  
  function create()
  {
    $this->initLevel();
    $this->connect();

    foreach ($this->sectors as $sector)
    {
      $room = $sector->room;
      $type = empty($room->type)? $this->random->pick($this->config['room-types']) : null;
      $room->init($type);

      $this->build($room);
    }

    $this->tunnel();

    $this->addExits();
    
    foreach ($this->sectors as $sector)
    {
      if ($this->is('wet-level')) {
        $sector->room->spread('room-floor', 'water', rint(1,5));
        $sector->room->spread('tunnel', 'water', rint(1,5));  
      }
 
      $this->populate($sector->room);
    }

  }

  function addExits()
  {
    $ex = ['city-1', 'cellars-1', 'cellars-2', 'cellars-3'];

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
    if ($room->is('stairs-up')) {
      $this->preset('rubble', ['spawn' => 'rat']);
      $i = $room->spread('room-floor', 'rubble', 1);
      $this->preset('trigger', ['click' => $room->levelPos($i[0]) ]);   
      $room->spread('door', 'trigger', 3);
    }
    
    if ($room->is('stairs-down') and rbet(.5)) {
      $room->fill('room-floor', 'rubble');
      $room->spread('rubble', rfunc(['copper-coins', 'rat', 'dirty-rag']), rint(0,3)); //hidden in rubble
    }
  }  

  function buildBoss(Room $room)
  {
    $room->setSize(0,0,16,16);
    $room->rectangleLayout();
  }

  function populateBoss($room)
  {
  }  

  function populateWet($room)
  {
    $room->spread('room-floor', 'wall-moss', rint(1,5));
    $room->spread('room-floor', rfunc(['wet-floor','water', 'mud']), rint(1,8));
    $room->spread('room-floor', 'frog', rint(0,1));
    $room->spread('tunnel', rfunc(['wet-floor']), rint(1,5));

    if (rbet(.5))
      $room->spread('water', rfunc(['rusty-dagger', 'copper-coins', 'dirty-rag']), rint(1,3));

  }

  function populateDestruct($room)
  {
    $room->spread('room-floor', rfunc(['wall','small-rock']), rint(1,5));
    $room->spread('room-wall', rfunc(['dirt','floor']), rint(1,5));
    $room->spread('tunnel', rfunc(['small-rock']), rint(0,2));
    $room->spread('room-floor', rfunc(['rat', 'old-bread']), rint(0,3));

    if ($this->number > 1) {
      $room->spread('tunnel', 'rat', rint(0,3));
    }
  }

  function populateEmpty($room)
  {
    $room->spread('room-floor', rfunc(['bones', 'dirty-rag']), rint(0,1));

    $items = ['cheese', 'old-bread'];
    if ($this->number > 1) $items += ['slime', 'beer'];
    if ($this->number > 2) $items += ['rat-meat'];

    if (rbet(.1)) {
      $room->spread('room-floor', 'rat', rint(1, $this->number));
      $room->spread('room-floor', rfunc($items) , rint(1, $this->number));
    }
  }

  function populateWarehouse($room)
  {
    $p = $this->painter($room);
    $corners = [[0,0],[0,1],[1,0],[1,1]];

    $p->fill(0.5, 0.5, .2, .2, 'wall');

    if (rbet(.3)) $p->points($corners, rfunc(['leather-sandals', 'wooden-torch', 'poison', 'light-cure']));
    elseif(rbet(.7)) $p->points($corners, rfunc(['dirt', 'wet-floor', 'rubble']));
    else $p->points($corners, rfunc(['short-sword', 'rusty-dagger', 'small-shield', 'arrows']));

  }

  function painter($room)
  {
    $p = new Painter($this, $room->position());
    $p->copySize($room)->shrink(1);
    return $p;
  }  

}

?>