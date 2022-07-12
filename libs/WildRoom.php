<?php 

class WildRoom extends Room
{

  function create()
  {
  	$this->createDefault();
  }

  function createDefault()
  {
  	$this->fill('room-floor', 'grass');
  }

}

?>