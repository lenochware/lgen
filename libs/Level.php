<?php 

class Level extends Entity
{
  protected $number = 1;

  public $sectorWidth = 16;
  public $sectorHeight = 16;  

  public $sectors = [];
  public $config;

  protected $exits = [];

  function __construct($number)
  {
    parent::__construct();
    $this->number = $number;
    $this->app->db->indexLevel($number);

    $id = $this->random->get($this->db->list('level'));
    $this->config = $this->db->get($id);
  }

  function init($width, $height)
  {
    $this->width = $width;
    $this->height = $height;

    for ($x=0; $x < $width; $x++) { 
      for ($y=0; $y < $height; $y++)
      { 
        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);
      }
    }
  }

  function create()
  {
    throw new Exception('Method not implemented.');
  }

  function tunnel()
  {
    foreach ($this->sectors as $sector) {
      if ($sector->connected) $sector->room->tunnel($sector->connected->room);
      
      $nb = $sector->getConnected();
      if ($nb) $sector->addTag(count($nb).'-door');
    }
  }

  function addExit($room, $param)
  {
    $exit = [
      'id' => $param[0],
      'levelId' => $param[1],
      'type' => 'exit'
    ];

    $room->addTag('exit');

    $i = rget($room->find('room-floor'));
    $room->put($i, ['exit', $exit['id']]);
    $room->cacheClear('room-floor');

    [$x, $y] = vec_add($room->position(), $room->pos($i));

    $exit['pos'] = ['x' => $x, 'y' => $y];

    $this->exits["$x,$y"] = $exit;
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
    $sector = $this->getSector($sx, $sy);
    if (!$sector) return ['','','','none'];

    return $sector->room->get($x % $this->sectorWidth, $y % $this->sectorHeight);
  }

  function set($x, $y, $id)
  {
    $sx = floor($x / $this->sectorWidth);
    $sy = floor($y / $this->sectorHeight);
    $sector = $this->getSector($sx, $sy);
    if (!$sector) return;

    $sector->room->set($x % $this->sectorWidth, $y % $this->sectorHeight, $id);
  }


  protected function connectSectors()
  {
    foreach($this->sectors as $sector) {
      $sector->reset();
    }

    foreach($this->sectors as $sector) {
      $sector->randomConnect();
    }
  }

  protected function isConnected()
  {
    $visited = [];

    $this->visit($visited, $this->getSector(0,0));

    if (count($visited) == count($this->sectors)) return true;
    else return false;
  }

  function connect()
  {
    while (!$this->isConnected()) {
      $this->connectSectors();
    }    
  }

  private function visit(&$visited, $sec)
  {
    if (in_array($sec, $visited, true)) return;
    $visited[] = $sec;

    foreach($sec->getConnected() as $next) {
      $this->visit($visited, $next);
    }
  }

protected function htmlTile($x, $y)
{
    $tile = $this->get($x, $y);
  $title = '';
  
  for($i = 0; $i < 3; $i++) {
    $id = $tile[$i];
    if (!$id) continue;
    $obj = $this->db->get($id);
    if ($title) $title .= ', ';
    $title .= $id;
    $render = $obj['render'];
  }

  $title .= ', '. $tile[3];

  $click = "loadInfo($x,$y);";

  return paramStr('<span style="color:{color}" title="'.$title.'" onclick="'.$click.'">{char}</span>', $render);
}

function html()
{
  //$this->drawBioms();

  $s = '';

  for ($y=0; $y < $this->sectorHeight*$this->height; $y++) {
    for ($x=0; $x < $this->sectorWidth*$this->width; $x++) { 
      $s .= $this->htmlTile($x, $y);
    }

    $s .= "<br>";
  }

  return "<code style=\"font-size:14px\">$s</code>";    
}

function build(Room $room)
{
  $name = 'build'.ucfirst($room->type);
  
  if (!method_exists($this, $name)) {
    $room->rectangleLayout();
    return false;
  }

  call_user_func([$this, $name], $room);
}

function populate(Room $room)
{
  $name = 'populate'.ucfirst($room->type);
  if (!method_exists($this, $name)) return false;
  call_user_func([$this, $name], $room);
}

function toArray()
{
  $data = [
    'width' => $this->sectorWidth*$this->width,
    'height' => $this->sectorHeight*$this->height,
    'exits' => $this->exits,
    'tiles' => [],
  ];

  for ($y=0; $y < $this->sectorHeight*$this->height; $y++) {
    for ($x=0; $x < $this->sectorWidth*$this->width; $x++) { 
      $data['tiles'][] = $this->get($x, $y);
    }
  }

  return $data;
}

}

?>