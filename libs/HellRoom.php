<?php 

class HellRoom extends Room
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