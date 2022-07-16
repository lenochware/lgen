<?php 

class Painter extends Entity
{
  protected $level;
  protected $position;

  function __construct(Level $level, $position)
  {
    parent::__construct();
    $this->level = $level;
    $this->position = $position;
  }

  function copySize(Entity $e)
  {
    $this->x = $e->x;
    $this->y = $e->y;
    $this->width = $e->width;
    $this->height = $e->height;
  }

  function pool($x, $y, $func, $size)
  {     
    $this->x = $x;
    $this->y = $y;
    $this->width = $size;
    $this->height = $size;

    if (is_string($func)) {
      $func = fn() => $func;
    }

    $n = floor($this->width/2);
    for($i = 0; $i < $this->height; $i++) {
      $this->poolLine($i, abs($n - $i), $func);
    }
  }

  protected function poolLine($y, $n, $func)
  {
    $start = $n? rint(max($n-2,1), $n) : 0;
    $end = $n? $this->width - rint(max($n-2,1), $n): $this->width;

    for($x = $start; $x < $end; $x++) {
      $xpos = $x + $this->x + $this->position[0];
      $ypos = $y + $this->y + $this->position[1];

      $id = $func($this->level, $xpos, $ypos);
      $this->level->set($xpos, $ypos, $id);
    }
  }

  function pattern($pattern, $func)
  {
    $px = count($pattern[0]);
    $py = count($pattern);

    if (is_string($func)) {
      $func = fn() => $func;
    }    

    for ($y = 0; $y < $this->height; $y++)
      for($x = 0; $x < $this->width; $x++) {
        $xpos = $x + $this->x + $this->position[0];
        $ypos = $y + $this->y + $this->position[1];
        
        if ($pattern[$y % $py][$x % $px]) {
          $id = $func($this->level, $xpos, $ypos);
          $this->level->set($xpos, $ypos, $id);
        }
      }
  }  
}

?>