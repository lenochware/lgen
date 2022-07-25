<?php 

class CityLevel extends Level
{
  function __construct()
  {
    parent::__construct();

    $this->app->db->indexLevel($this->number);

    $this->width = 6;
    $this->height = 6;
  }

  function create()
  {
    for ($x=0; $x < $this->width; $x++) { 
      for ($y=0; $y < $this->height; $y++)
      { 
        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);
        $sector->init($this->number, 'dungeon');
      }
    }

    $this->addStairs();

    foreach ($this->sectors as $sector) {
      $sector->create();
    }
  }

  function addStairs()
  {
  	$n = count($this->sectors);
    $this->sectors[rint(0,$n-1)]->room->addTag('stairs-down');  	
  }

}

?>