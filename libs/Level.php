<?php 

class Level extends Entity
{
  protected $number = 1;

  protected $width = 5;
  protected $height = 5;

  public $sectorWidth = 16;
  public $sectorHeight = 16;  

  public $sectors = [];
  public $config;

  public $bioms;

  function __construct($number)
  {
    parent::__construct();
    $this->number = $number;
    $this->app->db->indexLevel($number);
  }

  function init()
  {
    $id = $this->random->get($this->db->list('level'));
    $this->config = $this->db->get($id);
    $this->config['room-size'] = [5,8];

    // $this->config['room-size'] = [
    //   'min' => $this->random->int2(3,15),
    //   'max' => $this->random->int2(15,30),
    // ];
  }

  function size()
  {
    return $this->width * $this->height;
  }

  //build bioms
  function build($x, $y, $prev)
  {
    if (!empty($this->bioms[$x][$y])) return;
    if ($x < 0 or $x > $this->width - 1) return;
    if ($y < 0 or $y > $this->height - 1) return;

    $biom = $this->nextBiom($prev);
    $this->bioms[$x][$y] = $biom;

    $this->build($x-1, $y, $biom);
    $this->build($x+1, $y, $biom);
    $this->build($x, $y-1, $biom);
    $this->build($x, $y+1, $biom);
  }

  private function nextBiom($id)
  {
    $bioms = $this->config['bioms'];

    $biom = $bioms[$id];
    if (!$biom) return $id;

    return $this->random->pick($biom);
  }

  function connect()
  {
    foreach($this->sectors as $sector) {
      $sector->reset();
    }

    foreach($this->sectors as $sector) {
      $sector->randomConnect();
    }
  }

  function isConnected()
  {
    $visited = [];

    $this->visit($visited, $this->getSector(0,0));

    if (count($visited) == count($this->sectors)) return true;
    else return false;

  }

  private function visit(&$visited, $sec)
  {
    if (in_array($sec, $visited)) return;
    $visited[] = $sec;

    foreach($sec->getConnected() as $next) {
      $this->visit($visited, $next);
    }
  }

  function create()
  {
    $this->init();

    //$biom = $this->random->get(['dungeon', 'forest', 'rocks', 'desert', 'water', 'hell']);

    $this->build(1,1, 'dungeon');

    for ($x=0; $x < $this->width; $x++) { 
      for ($y=0; $y < $this->height; $y++)
      { 
        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);
        $sector->init($this->number, $this->getBiom($x, $y));
      }
    }

    $this->connect();

    foreach ($this->sectors as $sector) {
      if ($sector->connected) $sector->room->tunnel($sector->connected->room);
    }

    foreach ($this->sectors as $sector) {
      $sector->create();
    }
  }


  function getBiom($x, $y)
  {
    return $this->bioms[$x][$y];
  }

  function setSector($x, $y, Sector $sector)
  {
    $this->sectors[$y * $this->height + $x] = $sector;
  }

  function getSector($x, $y)
  {
    if ($x < 0 or $y < 0 or $x >= $this->width or $y >= $this->height) return null;
    return $this->sectors[$y * $this->width + $x];
  }

  function get($x, $y)
  {
    $sx = floor($x / $this->sectorWidth);
    $sy = floor($y / $this->sectorHeight);
    $room = $this->getSector($sx, $sy)->room;
    return $room->get($x % $this->sectorWidth, $y % $this->sectorHeight);
  }

  function set($x, $y, $id)
  {
    $sx = floor($x / $this->sectorWidth);
    $sy = floor($y / $this->sectorHeight);
    $room = $this->getSector($sx, $sy)->room;
    $room->set($x % $this->sectorWidth, $y % $this->sectorHeight, $id);
  }


protected function drawTile($name)
{
  $t = [
    'dungeon' => 'd',
    'forest' => '<font color="green">T</font>',
    'water' => '<font color="blue">~</font>',
    'rocks' => '<font color="gray">^</font>',
    'desert' => '<font color="orange">_</font>',
    'hell' => '<font color="red">H</font>',
  ];

  print $t[$name];
}

function draw()
{
  print "<code style=\"font-size:24px\">";
  for ($y=0; $y < $this->height; $y++) {
    for ($x=0; $x < $this->width; $x++) { 
      $this->drawTile($this->getBiom($x, $y));

      //print $this->getSector($x, $y)->strConnection();
    }

    print "<br>";
  }
  print "</code>";

  foreach ($this->sectors as $sector) {
    //$sector->room->draw();
  }

}

}

?>