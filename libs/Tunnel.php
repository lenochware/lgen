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

    $xlen = $fin[0] - $start[0];
    $ylen = $fin[1] - $start[1];

    $pos = $start;
    while($pos != $fin)
    {
      //$n = rint(0, $xlen);
      $n = $xlen;
      $pos = $this->line($pos, [$pos[0] + $n, $pos[1]]);
      $xlen -= $n;

      //$n = rint(0, $ylen);
      $n = $ylen;
      $pos = $this->line($pos, [$pos[0], $pos[1] + $n]);
      $ylen -= $n;
    }
  }


  protected function line($from, $to)
  {
    if ($from == $to) return $to;

    $len = $to[0] - $from[0];
    for($i = 0; $i < $len*sign($len); $i++) {
      $this->dig($from[0] + $i*sign($len), $from[1]);
    }

    $len = $to[1] - $from[1];
    for($i = 0; $i < $len*sign($len); $i++) {
      $this->dig($to[0], $from[1] + $i*sign($len));
    }
    
    return $to;
  }

  protected function dig($x, $y)
  {
    $prev = $this->level->get($x, $y);

    $id = 'floor';

    if ($prev[3] == 'room-wall') {
      $id = 'door';
    }

    if ($prev[3] == 'outside') $this->level->set($x, $y, 'tunnel');
    $this->level->set($x, $y, $id);
  }

}

?>