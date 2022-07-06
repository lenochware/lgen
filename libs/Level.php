<?php 

class Level extends Entity
{
  protected $number = 1;

  protected $width = 5;
  protected $height = 5;

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

  function connect()
  {
    // for (let r of this.rooms) {
    //   r.connected = [];
    //   r.isConnected = false;
    // }

    foreach($this->sectors as $sector) {
      $sector->reset();
    }

    foreach($this->sectors as $sector) {
      $sector->randomConnect();
    }
  }

  function isConnected()
  {
    return true;
  }

  protected function nextBiom($id)
  {
    $bioms = $this->config['bioms'];

    $biom = $bioms[$id];
    if (!$biom) return $id;

    return $this->random->pick($biom);
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
        $this->sectors[$y * $this->height + $x] = $sector;
        $sector->create($this->number, $this->bioms[$x][$y]);
      }
    }
  }

  function getBiom($x, $y)
  {
    return $this->bioms[$x][$y];
  }

  function getSector($x, $y)
  {
    if ($x < 0 or $y < 0 or $x >= $this->width or $y >= $this->height) return null;
    return $this->sectors[$y * $this->width + $x];
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

      //print $this->getSector($x, $y)->strConnected();
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