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
    $start = $this->start->position() + $this->start->pivot();
    $fin = $this->fin->position() + $this->fin->pivot();

    $xlen = $fin[0] - $start[0];
    $ylen = $fin[1] - $start[1];

    $pos = $start;
    while($pos != $fin)
    {
      $n = rint(1, $xlen);
      $pos = $this->line($pos, [$pos[0] + $n, $pos[1]], 'floor');
      $xlen -= $n;

      $n = rint(1, $ylen);
      $pos = $this->line($pos, [$pos[0], $pos[1] + $n], 'floor');
      $ylen -= $n;
    }
  }


  protected function line($from, $to, $id)
  {

    for($i = 0; $i < $len; $i++) {
      $this->level->set($x + $i, $y, $id);
    }

    for($i = 0; $i < $len; $i++) {
      $this->level->set($x, $y + $i, $id);
    }
    
    return $to;
  }

}

?>