<?php 

class HellRoom extends Room
{

  function init()
  {
    parent::init();
    $this->type = 'default';
  }	

  function populateDefault()
  {
    $this->fill('room-floor', 'grass');
  }

}

?>