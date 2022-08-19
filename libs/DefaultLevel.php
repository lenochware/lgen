<?php 

class DefaultLevel extends Level
{
  public $bioms;

  function __construct($number)
  {
    parent::__construct();
    $this->number = $number;
    $this->app->db->indexLevel($number);

    $this->width = 5;
    $this->height = 5;
  }

  function init()
  {
    $id = $this->random->get($this->db->list('level'));
    $this->config = $this->db->get($id);
    $this->config['room-size'] = [5,8];
  }

  function create()
  {
    $this->init();

    for ($x=0; $x < $this->width; $x++) { 
      for ($y=0; $y < $this->height; $y++)
      { 
        /*

        //$biom = $this->random->get(['dungeon', 'forest', 'rocks', 'desert', 'water', 'hell']);
        $type = $this->random->get(['destruct', 'pit', 'treasure', 'wet']);

        $room = new Room($type, $sectorX, $sectorY);
        $room->defaultLayout($width, $height);
        $this->add($room);


        */

        /* vytvoreni sectoru presunout do initu */
        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);
        $sector->init($this->number, 'dungeon');
      }
    }

    while (!$this->isConnected()) {
      $this->connect();
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