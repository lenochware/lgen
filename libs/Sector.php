<?php 

class Sector extends Entity
{
  public $level;
  public $room;
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

    $room = new Room(1);
    $this->add($room);
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