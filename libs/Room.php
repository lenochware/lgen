<?php 

class Room extends Entity
{
	protected $data = [];

	public $x;
	public $y;

	public $width = 1;
	public $height = 1;

	public $sectorWidth = 16;
	public $sectorHeight = 16;

  protected $sector;
  protected $level;

  protected $biom;
  protected $lvl;
  protected $type;

  protected $pivotPos = [1,1];

  function __construct($lvl, $biom)
  {
    parent::__construct();
    $this->biom = $biom;
    $this->lvl = $lvl;
  }

  function setSector(Sector $sector)
  {
    $this->sector = $sector;
    $this->level = $sector->level;
  }

  function setSize($width, $height)
  {
    $this->width = $width;
    $this->height = $height;

    $this->x = rint(0, $this->sectorWidth - $width - 1);
    $this->y = rint(0, $this->sectorHeight - $height - 1);

    $this->pivotPos = [rint(1 ,$width-2), rint(1 ,$height-2)];
  }

  function create()
  {
  }

  //zavolej nad vsemi tiles func - markov-chain
  function each($func)
  {
    foreach ($this->data as $i => $tile) {
      $y = floor($i / $this->sectorWidth);
      $x = $i % $this->sectorWidth;

      if (!empty($tile[0])) $func($this, $x, $y, $tile[0]);
      if (!empty($tile[1])) $func($this, $x, $y, $tile[1]);
      if (!empty($tile[2])) $func($this, $x, $y, $tile[2]);
    }
  }

  function size()
  {
    return ($this->width-1)*($this->height-1);
  }

  function position()
  {
    $pos = $this->sector->position();
    return [$pos[0] + $this->x, $pos[1] + $this->y];
  }

  function pivot()
  {
    return $this->pivotPos;
  }

  protected function getTypeId($id)
  {
    $types = ['ground' => 0, 'item' => 1, 'actor' => 2];

    $obj = $this->db->get($id);
    $x = $obj['family'][0];
    return $types[$x];
  }

  function set($x, $y, $id, $rel = false)
  {
  	if ($rel) {
  		$x += $this->x;
  		$y += $this->y;
  	}
    $this->data[$y * $this->sectorWidth + $x][$this->getTypeId($id)] = $id;
  }

  function get($x, $y, $rel = false)
  {
  	if ($rel) {
  		$x += $this->x;
  		$y += $this->y;
  	}

    return $this->data[$y * $this->sectorWidth + $x];
  }

  // i pro level sectors? i pro libovolny rect nad levelem (celkove predvyplneni tiles)?
  function fill($where, $func)
  {
    if (!is_callable($func)) {
      $func = fn() => $func;
    }

    $found = $this->find($where);
    if (!$found) return;

    foreach ($found as $i) {
      $id = $func($this, $i);
      $this->data[$i][$this->getTypeId($id)] = $id;
    }
  }

	function spread($where, $func, $n)
  {
    $found = $this->find($where);
    if (!$found) return;
    
    if (count($found)<$n) {
      while(count($found)<$n) $found = array_merge($found, $found);
    }

    shuffle($found);
    $idx = array_slice($found, 0, $n);

    foreach($idx as $i) {
      $id = $func($this, $i);
      $this->data[$i][$this->getTypeId($id)] = $id;
    }
  }

  function clear()
  {
  	$n = $this->sectorWidth * $this->sectorHeight;

  	for ($i = 0; $i < $n; $i++) { 
  		$this->data[$i] = ['outer-wall', '', ''];
  	}

    for ($y = 0; $y < $this->height; $y++) {
    	for ($x = 0; $x < $this->width; $x++) {
        $this->set($x, $y, 'floor', true);
    	}
    }

    for ($i = 0; $i < $this->width; $i++) {
      $this->set($i, 0, 'wall', true);
      $this->set($i, $this->height - 1, 'wall', true);
    }

    for ($i = 0; $i < $this->height; $i++) {
      $this->set(0, $i, 'wall', true);
      $this->set($this->width - 1, $i, 'wall', true);
    }

  }

protected function drawTile($tile)
{
  $title = '';
  foreach($tile as $id) {
    if (!$id) continue;
    $obj = $this->db->get($id);
    if ($title) $title .= ', ';
    $title .= $obj['name'];
    $render = $obj['render'];
  }

  print paramStr('<span style="color:{color}" title="'.$title.'">{char}</span>', $render);

  //print '<font color="orange">0</font>';

}

function draw()
{
  print paramStr("Room {0}x{1} ({2})<br>", [$this->width, $this->height,$this->type]);

  print "<code style=\"font-size:24px\">";
  for ($y=0; $y < $this->sectorHeight; $y++) {
    for ($x=0; $x < $this->sectorWidth; $x++) { 
      $this->drawTile($this->get($x, $y));
    }

    print "<br>";
  }
  print "</code>";
}

function find($id)
{
  $found = [];
  foreach ($this->data as $i => $tile) {
    if ($this->data[$i][$this->getTypeId($id)] == $id) $found[] = $i;
  }

  return $found;
}

function onSpawn($room, $x, $y, $id)
{
  $obj = $this->db->get($id);
  if (!isset($obj['on-spawn'])) return;

  foreach($obj['on-spawn'] as $action) {
    $this->execute($action, $room, $x, $y, $id);
  }
}

protected function execute($action, $room, $x, $y, $id)
{
  if ($action['action'] == 'replace') {
     if ($this->random->chance($action['p'])) {
      $room->set($x, $y, $action['id']);
    }
  }
}


}

?>