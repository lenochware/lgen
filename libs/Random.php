<?php 

class Random implements \pclib\IService
{

public $seed = 1;

function __construct()
{
  $this->seed = time();
}

/** Return random float in interval <0,1> */
function float()
{
  $x = sin($this->seed++) * 10000;
  return $x - floor($x);  
}

function integer($max)
{
  return floor($this->float() * ++$max);
}

/** Return true with probability $p, for 50% use chance(0.5) */
function chance($p)
{
  return ($this->float() < $p);
}

/** Get random item from the list. */
function get($list)
{
  return $list[$this->int(0,count($list)-1)];
}

/** Get random item from the list (lower index with higher probability). */
function get2($list)
{
  return $list[$this->int2(0,count($list)-1)];
}

/** Get random integer in interval. */
function int($a, $b = null)
{
  if (is_array($a)) return $this->int($a[0], $a[1]);
  return $this->integer($b - $a) + $a;
}

function int2($a, $b = null)
{
  if (is_array($a)) return $this->int2($a[0], $a[1]);
  return $this->int($a, $this->int($a, $b));
}

/** Shuffle array in place. */
function shuffle(&$list)
{
  usort($list, fn() => sign(0.5 - $this->float()));
}

/** Pick item from list with probability using chances array. Or you can use list [item,chance,item,chance]. */
function pick($list, $chances = null)
{
  if ($chances) return $this->getc($list, $chances);
  
  $items = $chances = [];
  $count = count($list);

  for ($i = 0; $i < $count; $i += 2) { 
    $items[] = $list[$i];
    $chances[] = $list[$i+1];
  }

  return $this->getc($items, $chances);
}

/** Helper for pick() */
function getc($items, $chances)
{ 
  $max = $this->float() * array_sum($chances);

  $sum = 0;
  foreach ($chances as $i => $value) {
    $sum += $value;
    if ($sum > $max) return $items[$i];
  }
}

/** Pick $index item from $list. If $index does not exists, it approximates between list items. */
protected function pickList($index, $list)
{
  $index = (int)$index;
  if (!$list) return 0;

  if (isset($list[$index])) return $list[$index];

  $keys = array_keys($list);
  if ($index < $keys[0]) return $list[0];
  if ($index > $keys[count($keys)-1]) return $list[count($keys)-1];

  foreach($keys as $key) {
    if ($key > $index) return $list[$key];
  }
}

/** chance() with probabilities driven by $list. pass($lvl, [1=>0.1,3=>0.5,6=>0]) */
function pass($index, $list)
{
  return $this->chance($this->pickList($index, $list));
}

/** Return callback for random selection. put(x, $y, func(['wall','wall-moss'])) */
function func($list, $type = '')
{
  if ($type == 'i2') {
    return fn() => $this->get2($list);
  }

  return fn() => $this->get($list);
}

}

 ?>