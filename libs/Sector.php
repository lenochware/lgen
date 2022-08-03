<?php 

class Sector extends Entity
{
  public $level;
  public $room;
  public $biom;
  public $connected;

  function __construct(Level $level, $x, $y)
  {
    parent::__construct();
    $this->level = $level;
    $this->width = $level->sectorWidth;
    $this->height = $level->sectorHeight;
    $this->x = $x;
    $this->y = $y;
    $this->reset();
  }

  function init($lvl, $biom)
  {
    $this->biom = $biom;
    
    $room = $this->getRoom($lvl, $biom);
    $room->init();
    $this->add($room);
    $room->create('layout');

  }

  function create()
  {
    $this->room->create('populate');
  }

  function position()
  {
    return [$this->x * $this->width, $this->y * $this->height];
  }

  function add(Room $room)
  {
    $this->room = $room;
    $room->setSector($this);
  }

  function reset()
  {
    $this->connected = null;
  }

  function randomConnect()
  {
    $nb = $this->getFreeNeighbours();
    if (!$nb) return;

    $this->connected = $this->random->get($nb);
  }

  protected function getFreeNeighbours()
  {
    $neighbours = [];

    $addnb = function($cur, $x, $y) use (&$neighbours)
    {
      $sec = $cur->level->getSector($x, $y);
      if (!$sec) return;
      if ($sec->connected === $cur) return;
  
      $neighbours[] = $sec;
    };

    $addnb($this, $this->x-1, $this->y);
    $addnb($this, $this->x+1, $this->y);
    $addnb($this, $this->x, $this->y-1);
    $addnb($this, $this->x, $this->y+1);

    return $neighbours;
  }

  function getConnected()
  {
    $x = $this->x;
    $y = $this->y;

    $nb = [
      $this->level->getSector($x-1, $y),
      $this->level->getSector($x+1, $y),
      $this->level->getSector($x, $y-1),
      $this->level->getSector($x, $y+1),
    ];

    $connected = [];
    
    foreach($nb as $sec)
    {
      if (!$sec) continue;
      if (!$sec->isConnectedWith($this)) continue;
      $connected[] = $sec;
    }

    return $connected;
  }

  function isConnectedWith(Sector $sec)
  {
    return ($sec->connected === $this or $this->connected === $sec);
  }

  function strConnection()
  {
      if (!$this->connected) return 'none';
      $px = $this->connected->x - $this->x;
      $py = $this->connected->y - $this->y;

      if ($px > 0) return 'R';
      if ($px < 0) return 'L';
      if ($py > 0) return 'D';
      if ($py < 0) return 'U';
  }

  function getRoom($lvl, $biom)
  {
    if ($biom == 'dungeon') $room = new DungeonRoom($lvl);
    elseif ($biom == 'hell') $room = new HellRoom($lvl);
    elseif (in_array($biom, ['forest', 'rocks', 'desert', 'water'])) $room = new WildRoom($lvl);
    else throw new Exception('Unknown biom.');
    return $room;
  }

  function addTag($id)
  {
    $this->room->addTag($id);
  }

  function is($id)
  {
    return $this->room->is($id);
  }

}

?>