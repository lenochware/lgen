<?php 

class Room extends Entity
{
  protected $data = [];

  public $sectorWidth = 16;
  public $sectorHeight = 16;

  protected $sector;
  protected $level;

  protected $lvl;
  protected $type;

  protected $cache = [];

  protected $pivotPos = [1,1];

  function __construct($lvl)
  {
    parent::__construct();
    $this->lvl = $lvl;
  }

  function setSector(Sector $sector)
  {
    $this->sector = $sector;
    $this->level = $sector->level;
  }

  function create($what)
  {
    if ($what == 'layout') {
      if (method_exists($this, 'layout' . ucfirst($this->type)))
        call_user_func([$this, 'layout' . ucfirst($this->type)]);
      else
        $this->rectangleLayout();
    }

    else if ($what == 'populate') {
      $this->createStairs();
      call_user_func([$this, 'populate' . ucfirst($this->type)]);
      $this->each([$this, 'onSpawn']);
    }

    else throw new Exception('Unknown create command.');
  }

  function createStairs()
  {
    $found = $this->find('room-floor');
    $this->random->shuffle($found);

    if ($this->is('stairs-up')) $this->put(array_pop($found), ['stairs', 'stairs-up']);
    if ($this->is('stairs-down')) $this->put(array_pop($found), ['stairs', 'stairs-down']);
    $this->cache['room-floor'] = null;
  }

  //zavolej nad vsemi tiles func - markov-chain
  function each($func, $where = null)
  {
    foreach ($this->data as $i => $tile)
    {  
      [$x, $y] = $this->pos($i);

      if ($where and $tile[$this->getTypeId($where)] != $where) continue;

      if (!empty($tile[0])) $func($this, $x, $y, $tile[0]);
      if (!empty($tile[1])) $func($this, $x, $y, $tile[1]);
      if (!empty($tile[2])) $func($this, $x, $y, $tile[2]);
      if (!empty($tile[3])) $func($this, $x, $y, $tile[3]);
    }
  }

  function pos($i)
  {
    return [$i % $this->sectorWidth, floor($i / $this->sectorWidth)];
  }

  function size()
  {
    return ($this->width-1)*($this->height-1);
  }

  function position()
  {
    return $this->sector->position();
  }

  function pivot()
  {
    return $this->pivotPos;
  }

  protected function getTypeId($id)
  {
    $types = ['ground' => 0, 'item' => 1, 'actor' => 2, 'meta' => 3];

    $obj = $this->db->get($id);
    $x = $obj['family'][0];
    return $types[$x];
  }

  function set($x, $y, $id, $rel = false)
  {
  	if ($id == 'none') return;

    if ($rel) {
      $x += $this->x;
      $y += $this->y;
    }

  	if ($x < 0 or $y < 0 or $x >= $this->sectorWidth or $y >= $this->sectorHeight) return;

    //$this->data[$y * $this->sectorWidth + $x][$this->getTypeId($id)] = $id;
    $this->put($y * $this->sectorWidth + $x, $id);
  }

  function get($x, $y, $rel = false)
  {
    if ($rel) {
      $x += $this->x;
      $y += $this->y;
    }

  	if ($x < 0 or $y < 0 or $x >= $this->sectorWidth or $y >= $this->sectorHeight) return ['','','','none'];

    return $this->data[$y * $this->sectorWidth + $x];
  }

  // i pro level sectors? i pro libovolny rect nad levelem (celkove predvyplneni tiles)?
  function fill($where, $func)
  {
    if (is_string($func) or is_array($func)) {
      $func = fn() => $func;
    }

    $found = $this->find($where);
    if (!$found) return;

    foreach ($found as $i) {
      $id = $func($this->data[$i]);
      if ($id == 'none') continue;
      $this->put($i, $id);
    }
  }

  function spread($where, $func, $n)
  {
    if (is_string($func) or is_array($func)) {
      $func = fn() => $func;
    }
    
    $found = $this->find($where);
    if (!$found) return;
    
    if (count($found)<$n) {
      while(count($found)<$n) $found = array_merge($found, $found);
    }

    $this->random->shuffle($found);
    $idx = array_slice($found, 0, $n);

    foreach($idx as $i) {
      $id = $func($this->data[$i]);
      if ($id == 'none') continue;
      $this->put($i, $id);
    }
  }

  function put($i, $tile)
  {
    if (is_string($tile)) {
      $this->data[$i][$this->getTypeId($tile)] = $tile;
    }
    else foreach($tile as $id) {
      $this->data[$i][$this->getTypeId($id)] = $id;
    }
  }

  function pool($x, $y, $func, $size)
  {
    $p = new Painter($this->level, $this->sector->position());
    $p->pool($x, $y, $func, $size);
  }

  function pattern($pattern, $func)
  {
    $p = new Painter($this->level, $this->sector->position());
    $p->copySize($this);
    $p->x++; $p->y++; $p->width -= 2; $p->height -= 2;
    $p->pattern($pattern, $func);
  }

  function tunnel(Room $target)
  {
    $tunnel = new Tunnel($this->level, $this, $target);
    $tunnel->create();
  }

  function clear($tile)
  {
    $n = $this->sectorWidth * $this->sectorHeight;

    for ($i = 0; $i < $n; $i++) { 
      $this->data[$i] = $tile;
    } 	
  }

  function init()
  {
    $this->width = rint(5,8);
    $this->height = rint(5,8);
    $this->x = rint(0, $this->sectorWidth - $this->width - 1);
    $this->y = rint(0, $this->sectorHeight - $this->height - 1);
  }

  function rectangleLayout()
  {
    $this->clear(['granite-wall', '', '', 'outside']);
    $this->rect($this->width, $this->height, 'floor', 'wall');
    $this->setPivot();    
  }

  // function paint()
  // {
  //   //$this->pool(1,1, ['floor','room-floor'], 8);

  //   // $p = new Painter($this->level, $this->sector->position());
  //   // $p->copySize($this);
  //   // //$p->grid([2,4,2], [2,4,2], [0,1,0,1,1,1,0,1,0], ['floor','room-floor']);
  //   // $p->grid([2,2], [2,2], [1,0,1,1], ['floor','room-floor']);

  //   // $this->each([$this, 'createWalls'], 'room-floor');
  // }

  protected function setPivot()
  {
    $i = $this->random->get($this->find('room-floor'));
    $this->pivotPos = $this->pos($i);
  }

  function rect($width, $height, $floor, $wall)
  {
    for ($y = 0; $y < $height; $y++) {
      for ($x = 0; $x < $width; $x++) {
        $this->set($x, $y, 'room-floor', true);
        $this->set($x, $y, $floor, true);
      }
    }

    for ($i = 0; $i < $width; $i++) {
      $this->set($i, 0, $wall, true);
      $this->set($i, $height - 1, $wall, true);

      $this->set($i, 0, 'room-wall', true);
      $this->set($i, $height - 1, 'room-wall', true);
    }

    for ($i = 0; $i < $height; $i++) {
      $this->set(0, $i, $wall, true);
      $this->set($width - 1, $i, $wall, true);

      $this->set(0, $i, 'room-wall', true);
      $this->set($width - 1, $i, 'room-wall', true);
    }    
  }

  protected function drawTile($tile)
  {
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
    $cacheable = ['room-floor', 'room-wall', 'tunnel', 'outside'];

    if (isset($this->cache[$id])) return $this->cache[$id];

    $found = [];
    foreach ($this->data as $i => $tile) {
      if ($this->data[$i][$this->getTypeId($id)] == $id) $found[] = $i;
    }

    if (in_array($id, $cacheable)) $this->cache[$id] = $found;

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

  static function createWalls($room, $x, $y, $id)
  {
    foreach ([[-1,0],[1,0],[0,1],[0,-1]] as $pos) {
      $tile = $room->get($x + $pos[0], $y + $pos[1]);
      if ($tile[3] == 'outside' or $tile[3] == 'none') {
        $room->set($x, $y, ['room-wall', 'wall']);
        return;
      }
    }
  }

}

?>