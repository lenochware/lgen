<?php 

class Tunnel extends Entity
{
  protected $start;
  protected $fin;
  protected $level;

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

    $this->line($start, $fin);
  }

  protected function line($a, $b)
  {
    if ($a == $b) return $b;

    $len = $b[0] - $a[0];
    for($i = 0; $i < $len*sign($len); $i++) {
      $this->dig($a[0] + $i*sign($len), $a[1]);
    }

    $len = $b[1] - $a[1];
    for($i = 0; $i < $len*sign($len); $i++) {
      $this->dig($b[0], $a[1] + $i*sign($len));
    }
    
    return $b;
  }

  protected function dig($x, $y)
  {
    static $door, $outside = false;

    $tile = $this->level->get($x, $y);

    if ($tile[3] == 'room-wall')
    {
      if ($door and $outside) return;
      if ($door and !$outside) {
        $this->level->set($door[0], $door[1], 'wall');
      };

      $id =  'door';
      $door = [$x, $y];
    }
    else $id = 'floor';


    if ($tile[3] == 'outside') {
      $this->level->set($x, $y, 'tunnel');
      $outside = true;
      $door = null;
    }

    $this->level->set($x, $y, $id);
  }

}

?>