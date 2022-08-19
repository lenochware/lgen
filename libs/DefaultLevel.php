<?php 

class DefaultLevel extends Level
{
  public $bioms;

  function __construct($number)
  {
    parent::__construct();
    $this->number = $number;
    $this->app->db->indexLevel($number);

    $id = $this->random->get($this->db->list('level'));
    $this->config = $this->db->get($id);


    $this->width = 5;
    $this->height = 5;
  }

  function init()
  {
    for ($x=0; $x < $this->width; $x++) { 
      for ($y=0; $y < $this->height; $y++)
      { 
        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);
      }
    }

  }

  function create()
  {
    $this->init();

    while (!$this->isConnected()) {
      $this->connect();
    }

    foreach ($this->sectors as $sector)
    {
      $room = $sector->room;
      $room->init();
      $room->rectangleLayout();
      /*

      //$biom = $this->random->get(['dungeon', 'forest', 'rocks', 'desert', 'water', 'hell']);
      $type = $this->random->get(['destruct', 'pit', 'treasure', 'wet']);

      */
    }

    foreach ($this->sectors as $sector) {
      if ($sector->connected) $sector->room->tunnel($sector->connected->room);
    }

    foreach ($this->sectors as $sector) {
      $nb = $sector->getConnected();
      if ($nb) $sector->addTag(count($nb).'-door');

      
      /* 

        $this->populate($room); //vola metodu $this->pop{$room->type}

       */
      //$sector->create();
    }

  }

}

?>