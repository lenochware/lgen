<?php 

function rfunc($type, $list)
{
    global $app;

    if (!is_array($list)) {
        $list = dblist($list);
    }
    
    return $app->random->func($type, $list);
}

function rget($list)
{
    global $app;

    if (!is_array($list)) {
        $list = dblist($list);
    }

    return $app->random->get($list);
}

function rint($a, $b = null)
{
    global $app;
    return $app->random->int($a, $b);
}

function rbet($p)
{
    global $app;
    return $app->random->chance($p);
}

function dblist($id)
{
    global $app;
    return $app->db->list($id);
}

function dbget($id)
{
    global $app;
    return $app->db->get($id);
}

function sign($x)
{
    return $x <=> 0;
}

function xtr($x, $from, $to)
{
    return lerp($to[0], $to[1], smoothstep($from[0], $from[1], $x));
}

function lerp($a, $b, $t)
{
    return $a + $t * ($b - $a);
}

function smoothstep ($min, $max, $x)
{
   if ($x < $min) return 0;
   if ($x >= $max) return 1;

   // Scale/bias into [0..1] range
   $x = ($x - $min) / ($max - $min);

   return $x * $x * (3 - 2 * $x);
}

function vec_add($a, $b)
{
    foreach($a as $i => $it) { 
        $a[$i] += $b[$i]; 
    }

    return $a;
}

function replace_func($from, $to)
{
    return fn($tile) => in_array($from, $tile)? $to : 'none';
}

// static clamp(x, a, b)
// {
//  return Math.max(a, Math.min(x, b));
// }


 ?>