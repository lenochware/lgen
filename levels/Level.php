<?php 

class Level extends Entity
{
  protected $id = '';
  protected $number = 1;

  public $sectorWidth = 16;
  public $sectorHeight = 16;  

  public $sectors = [];
  public $config;

  protected $exits = [];

  protected $objectPresets = [];

  function __construct($id, $number)
  {
    parent::__construct();
    $this->id = $id;
    $this->number = $number;
    $this->app->db->indexLevel($number);
    $this->config = $this->db->get($this->id.'-'.$this->number);
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

    $room->type = 'exit';
    $room->addTag('exit');
    $room->addTag($param[0]);

    $i = rget($room->find('room-floor'));
    $room->put($i, ['exit', $exit['id']]);
    $room->cacheClear('room-floor');

    [$x, $y] = vec_add($room->position(), $room->pos($i));

    $exit['pos'] = ['x' => $x, 'y' => $y];

    $this->exits["$x,$y"] = $exit;
  }

  function setSector($x, $y, Sector $sector)
  {
    $this->sectors[$y * $this->width + $x] = $sector;
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
  $title .= " ($x,$y)";

  $render['id'] = "$x,$y";
  $render['title'] = $title;

  //$click = "loadInfo($x,$y);";

  return paramStr('<span id="{id}" class="tile" style="color:{color}" title="{title}">{char}</span>', $render);
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

function populate(Room $room, $type = null)
{
  $name = 'populate'.ucfirst($type ?: $room->type);
  if (!method_exists($this, $name)) return false;
  $this->objectPresets = [];
  call_user_func([$this, $name], $room);
}

//umoznit obj jako funkci? (generovani variability objektu stejneho typu.)
function preset($id, $obj)
{
  $this->objectPresets[$id] = $obj;
}

function getPresets()
{
  return $this->objectPresets;
}

function toArray()
{
  $data = [
    'id' => $this->id.'-'.$this->number,
    'width' => $this->sectorWidth*$this->width,
    'height' => $this->sectorHeight*$this->height,
    'exits' => $this->exits,
    'player-pos' => [],
    'tiles' => [],
    'objects' => [],
  ];

  for ($y=0; $y < $this->sectorHeight*$this->height; $y++) {
    for ($x=0; $x < $this->sectorWidth*$this->width; $x++) { 
      $data['tiles'][] = $this->get($x, $y);
    }
  }

  foreach($this->sectors as $sector) {
    $data['objects'] += $sector->room->getObjects();
  }

  return $data;
}

}

?>