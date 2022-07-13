<?php 

class WildRoom extends Room
{

  function create()
  {

    $this->type = $this->random->get(['desert', 'forest', 'swamp', 'rocks']);
    $this->type = 'forest';

    $this->callCreate($this->type);    
  }

  function createDesert()
  {
  	$this->fill('room-floor', 'grass');
  }

  function createForest()
  {
  	$this->fill('outside', 'floor');
  	$this->spread('outside', 'tree', 20);
  	$this->fill('room-floor', 'grass');
  }

  function createSwamp()
  {
  	$this->fill('room-floor', 'grass');
  }

  function createRocks()
  {
  	$this->fill('room-floor', 'grass');
  }

}

?>