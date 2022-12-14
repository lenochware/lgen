<?php 

class Tunnel extends Entity
{
  protected $start;
  protected $fin;
  protected $level;

  public $useMidPoint = false;

  function __construct(Level $level, Room $start, Room $fin)
  {
    parent::__construct();

    $this->level = $level;
    $this->start = $start;
    $this->fin = $fin;
  }


  function create()
  {
    $start = vec_add($this->start->position(), $this->start->pivot());
    $fin = vec_add($this->fin->position(), $this->fin->pivot());

    if ($this->useMidPoint) {
	    $found = $this->start->find('outside');
	    $s = $this->start;
	    while(1) {
	      $pos = $this->start->pos(rget($found));
	      if ($pos[0] == $s->x or  $pos[1] == $s->y) continue;
	      if ($pos[0] == $s->x+$s->width-1 or  $pos[1] == $s->y+$s->height-1) continue;
	      break;
	    }
	    
	    $mid = vec_add($this->start->position(), $pos);

	    $this->line($start, $mid);
	    $this->line($mid, $fin);    	
    }
    else {
        $this->line($start, $fin);
    }
  }

  protected function line($a, $b)
  {
    if ($a == $b) return $b;

    $len = $b[0] - $a[0];
    $prevX = $a[0];
    
    for($i = 0; $i < abs($len); $i++) {
      $x = $a[0] + $i*sign($len);
      $this->dig($x, $a[1], $prevX, $a[1]);
      $prevX = $x;
    }

    $len = $b[1] - $a[1];
    $prevY = $a[1];
    
    for($i = 0; $i < abs($len); $i++) {
      $y = $a[1] + $i*sign($len);
      $this->dig($b[0], $y, $b[0], $prevY);
      $prevY = $y;
    }
    
    return $b;
  }

  protected function dig($x, $y, $prevX, $prevY)
  {
    $tile = $this->level->get($x, $y);
    $prev = $this->level->get($prevX, $prevY);

    if ($tile[3] == 'outside') {
      $this->level->set($x, $y, 'tunnel');
    }

    $this->level->set($x, $y, 'floor');

    if (($prev[3] == 'room-floor' or $prev[3] == 'tunnel') and $tile[3] == 'room-wall') {
      $this->level->set($x, $y, 'door');

      if ($prev[3] == 'room-floor') {
      	$this->start->addDoor($x, $y, $this->fin);
      }
      else {
      	$this->fin->addDoor($x, $y, $this->start);
      }
    }
  }

  static function createWalls($room, $x, $y, $id)
  {
    if ($id != 'tunnel') return;
    foreach ([[-1,0],[1,0],[0,1],[0,-1]] as $pos) {
      $tile = $room->get($x + $pos[0], $y + $pos[1]);
      if ($tile[3] == 'outside')
        $room->set($x + $pos[0], $y + $pos[1], ['tunnel-wall', 'wall']);
    }
  }


}

?>