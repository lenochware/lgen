<?php 

class HellRoom extends Room
{

  function create()
  {
  	$this->createDefault();
  }

  function createDefault()
  {
    $this->clear();
    $this->fill('room-floor', 'grass');
  }

}

?>