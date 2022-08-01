<?php 

class Entity
{
  protected $random;
  protected $app;
  protected $db;

  protected $tags = [];

  public $x = 0;
  public $y = 0;
  public $width = 1;
  public $height = 1;

  function __construct()
  {
    global $pclib;
    $this->app = $pclib->app;
    $this->random = $pclib->app->random;
    $this->db = $pclib->app->db;
  }

  function addTag($id)
  {
  	$this->tags[$id] = 1;
  }

  function is($id)
  {
  	return isset($this->tags[$id]);
  }

  function __toString()
  {
    return "($this->x,$this->y) ($this->width,$this->height) [".implode(',', array_keys($this->tags))."]";
  }

}

 ?>