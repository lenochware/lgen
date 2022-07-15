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
  	$this->fill('granite-wall', 'floor');
  	$this->spread('outside', 'tree', 20);
  	$this->fill('room-floor', 'grass');

  	$func = function($level, $x, $y) {
  		$tile = $level->get($x, $y);
  		if (!$tile) return 'none';
  		if ($tile[3] == 'outside' or $tile[3] == 'tunnel') return 'water';
  		return 'none';
  	};

    if (rbet(.5))
    	$this->pool(rint(0,12), rint(0,12), rfunc('', ['tree','grass']), rint(5,15));
    else 
    	$this->pool(rint(0,12), rint(0,12), $func, rint(5,15));

    
    // $this->spread('water', 'fish', 20);
    // $this->pool(0, 1, rfunc('', ['tree','grass']));
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