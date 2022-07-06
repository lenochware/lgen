<?php 

class WildRoom extends Room
{

  function create()
  {
  	$this->createDefault();
  }

  function createDefault()
  {
  	$this->clear();
  	$this->fill('floor', 'grass');
  }

}

?>