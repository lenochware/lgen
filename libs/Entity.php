<?php 


class Entity
{
  protected $random;
  protected $app;
  protected $db;

  function __construct()
  {
    global $pclib;
    $this->app = $pclib->app;
    $this->random = $pclib->app->random;
    $this->db = $pclib->app->db;
  }

}

 ?>