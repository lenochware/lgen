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
    return $this;
  }

  function getPos($x, $y)
  {
    $xpos = $x + $this->x + $this->position[0];
    $ypos = $y + $this->y + $this->position[1];
    return [$xpos, $ypos];
  }

  function pool($x, $y, $func, $size)
  {     
    $this->x = $x;
    $this->y = $y;
    $this->width = $size;
    $this->height = $size;

    if (is_string($func) or is_array($func)) {
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

    for($x = $start; $x < $end; $x++)
    {
      $pos = $this->getPos($x, $y);

      $id = $func($this->level->get($pos[0], $pos[1]));
      $this->level->set($pos[0], $pos[1], $id);
    }
  }

  function pattern($pattern, $func)
  {
    $px = count($pattern[0]);
    $py = count($pattern);

    if (is_string($func) or is_array($func)) {
      $func = fn() => $func;
    }    

    for ($y = 0; $y < $this->height; $y++)
      for($x = 0; $x < $this->width; $x++) {
        $pos = $this->getPos($x, $y);
        
        if ($pattern[$y % $py][$x % $px]) {
          $id = $func($this->level->get($pos[0], $pos[1]));
          $this->level->set($pos[0], $pos[1], $id);
        }
      }
  }

  /***/

  function expand($x, $y)
  {
    $this->x -= $x;
    $this->y -= $y;
    $this->width += 2*$x;
    $this->height += 2*$y;
    return $this;    
  }

  function shrink($z)
  {
    return $this->expand(-$z, -$z);
  }

  function vline($x, $y, $len, $func)
  {
    if (is_string($func) or is_array($func)) {
      $func = fn() => $func;
    }

    $x = round($x * ($this->width - 1));
    $y = round($y * ($this->height - 1));
    $len = round($len * $this->height);

    for($i = 0; $i < $len; $i++) {

      $pos = $this->getPos($x, $y + $i);

      $id = $func($this->level->get($pos[0], $pos[1]));
      $this->level->set($pos[0], $pos[1], $id);
    }
  }

  function hline($y, $x, $len, $func)
  {
    if (is_string($func) or is_array($func)) {
      $func = fn() => $func;
    }

    $x = round($x * ($this->width - 1));
    $y = round($y * ($this->height - 1));
    $len = round($len * $this->width);

    for($i = 0; $i < $len; $i++) {

      $pos = $this->getPos($x + $i, $y);

      $id = $func($this->level->get($pos[0], $pos[1]));
      $this->level->set($pos[0], $pos[1], $id);
    }
  }

  function rect($x, $y, $sx, $sy, $func)
  {
    if (is_string($func) or is_array($func)) {
      $func = fn() => $func;
    }

    $x0 = round(($x - $sx/2) * ($this->width - 1));
    $y0 = round(($y - $sy/2) * ($this->height - 1));

    $x1 = round(($x + $sx/2) * ($this->width - 1));
    $y1 = round(($y + $sy/2) * ($this->height - 1));

    for($i = $x0; $i <= $x1; $i++) {
      
      $pos = $this->getPos($x0 + $i, $y0);
      $id = $func($this->level->get($pos[0], $pos[1]));
      $this->level->set($pos[0], $pos[1], $id);

      $pos = $this->getPos($x0 + $i, $y1);
      $id = $func($this->level->get($pos[0], $pos[1]));
      $this->level->set($pos[0], $pos[1], $id);
    }   

    for($i = $y0; $i <= $y1; $i++) {
      
      $pos = $this->getPos($x0, $y0 + $i);
      $id = $func($this->level->get($pos[0], $pos[1]));
      $this->level->set($pos[0], $pos[1], $id);

      $pos = $this->getPos($x1, $y0 + $i);
      $id = $func($this->level->get($pos[0], $pos[1]));
      $this->level->set($pos[0], $pos[1], $id);
    }
  }


}


?>