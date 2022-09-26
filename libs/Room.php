<?php 

class Room extends Entity
{
  protected $data = [];

  public $sectorWidth = 16;
  public $sectorHeight = 16;

  protected $sector;
  protected $level;

  protected $lvl;
  public $type;

  protected $cache = [];

  protected $pivotPos = [1,1];
  public $doors = [];

  protected $objects = [];

  function __construct($lvl)
  {
    parent::__construct();
    $this->lvl = $lvl;
  }

  /** Init room (w x h) and position in sector. */
  function init($type)
  {
  	if ($type) $this->type = $type;
  	$this->addTag($type);
    $this->width = rint(5,8);
    $this->height = rint(5,8);
    $this->x = rint(0, $this->sectorWidth - $this->width - 1);
    $this->y = rint(0, $this->sectorHeight - $this->height - 1);
  }

  function setSector(Sector $sector)
  {
    $this->sector = $sector;
    $this->level = $sector->level;
  }

  function addDoor($x, $y, Room $room)
  {
    $this->doors["$x,$y"] = ['x'=>$x, 'y'=>$y, 'room'=>$room];
  }

  /**
   * Call $func for each object on $where positions.
   * @param string $where object-id such as 'door' or 'room-floor'
   * @param callable $func function(this, x, y, id)
   */
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

  /** Convert (int) pos in sector to [x,y]. */
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

  function cacheClear($key)
  {
  	$this->cache[$key] = null;
  }

  /** Convert type-id to tile-index. */
  protected function getTypeId($id)
  {
    $types = ['ground' => 0, 'item' => 1, 'actor' => 2, 'meta' => 3];

    $obj = $this->db->get($id);
    $x = $obj['family'][0];
    return $types[$x];
  }

  /**
   * Set (x,y) tile ('none' is ignored).
   * @param bool $rel if true, pos is relative to room, not to sector
   * @param string|array $id object-id(s) to be put
   * @return array $tile
   */  
  function set($x, $y, $id, $rel = false)
  {
  	if ($id == 'none') return;

    if ($rel) {
      $x += $this->x;
      $y += $this->y;
    }

  	if ($x < 0 or $y < 0 or $x >= $this->sectorWidth or $y >= $this->sectorHeight) return;

    $this->put($y * $this->sectorWidth + $x, $id);
  }

  /**
   * Get (x,y) tile.
   * @param bool $rel if true, pos is relative to room, not to sector
   * @return array $tile
   */
  function get($x, $y, $rel = false)
  {
    if ($rel) {
      $x += $this->x;
      $y += $this->y;
    }

  	if ($x < 0 or $y < 0 or $x >= $this->sectorWidth or $y >= $this->sectorHeight) return ['','','','none'];

    return $this->data[$y * $this->sectorWidth + $x];
  }

  /**
   * Fill $func objects on $where positions in sector.
   * @param string $where object-id such as 'door' or 'room-floor'
   * @param string|array|callable $func object-id(s) or function returning object-id(s) to be put ('none' is ignored)
   * @return array $idx filled positions (int) in sector
   */
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

    return $found;
  }

  /**
   * Spread randomly $n objects on $where positions in sector.
   * @param string $where object-id such as 'door' or 'room-floor'
   * @param string|array|callable $func object-id(s) or function returning object-id(s) to be put ('none' is ignored)
   * @param int $n number of objects
   * @return array $idx filled positions (int) in sector
   */
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

    return $idx;
  }

  /**
   * Set tile at position $i.
   * @param int $i position in sector
   * @param string|array $tile object-id(s) to be put
   */
  function put($i, $tile)
  {
    $param = $this->level->getPresets();

    if (is_string($tile)) {
      $id = $tile;
      $this->data[$i][$this->getTypeId($id)] = $id;
      if (isset($param[$id])) $this->setParams($i, $id, $param[$id]);
    }
    else foreach($tile as $id) {
      $this->data[$i][$this->getTypeId($id)] = $id;
      if (isset($param[$id])) $this->setParams($i, $id, $param[$id]);
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

  function rectangleLayout()
  {
    $this->clear(['wall', '', '', 'outside']);
    $this->rect($this->width, $this->height, 'floor', 'wall');
    $this->setPivot();    
  }

  function setPivot()
  {
    $i = $this->random->get($this->find('room-floor'));
    $this->pivotPos = $this->pos($i);
  }

  /** Draw (w x h) room rectangle layout with wall-id around, filled with floor-id */
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

  /**
   * Find all occurences of $id in sector.
   * @param string $id object-id such as 'door' or 'room-floor'
   * @return array $found positions (int) in sector
   */
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

  protected function setParams($i, $id, $params)
  {
    if (!isset($this->objects[$i])) $this->objects[$i] = [];
    $this->objects[$i][$this->getTypeId($id)] = $params;
  }

  /** Draw wall between room-floor and outside. Use as each() callback. */
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