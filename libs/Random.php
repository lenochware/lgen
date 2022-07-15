<?php 

class Random implements \pclib\IService
{

public $seed = 1;

function __construct()
{
  $this->seed = time();
}

function float()
{
  $x = sin($this->seed++) * 10000;
  return $x - floor($x);  
}

function integer($max)
{
  return floor($this->float() * ++$max);
}

function chance($p)
{
  return ($this->float() < $p);
}

function get($list)
{
  return $list[$this->int(0,count($list)-1)];
}

function get2($list)
{
  return $list[$this->int2(0,count($list)-1)];
}

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

function shuffle(&$list)
{
  usort($list, fn() => sign(0.5 - $this->float()));
}

function pick($list, $chances = null)
{
  if ($chances) return $this->getc($list, $chances);
  
  $items = $chances = [];
  $count = count($list);

  for ($i = 0; $i < $count; $i += 2) { 
    $chances[] = $list[$i];
    $items[] = $list[$i+1];
  }

  return $this->getc($items, $chances);
}

function getc($items, $chances)
{ 
  $max = $this->float() * array_sum($chances);

  $sum = 0;
  foreach ($chances as $i => $value) {
    $sum += $value;
    if ($sum > $max) return $items[$i];
  }
}

function func($type, $list /*, $chances = null*/)
{
  if ($type == 'i2') {
    return fn() => $this->get2($list);
  }

  return fn() => $this->get($list);
}

}

 ?>