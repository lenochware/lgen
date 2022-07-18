<?php 

class WildRoom extends Room
{

  function create()
  {
    $this->type = $this->random->get(['desert', 'forest', /*'swamp',*/ 'rocks']);
    //$this->type = 'forest';

    $this->callCreate($this->type);    
  }

  protected function replaceOutsideFunc($id)
  {
    $func = function($level, $x, $y) use($id) {
      $tile = $level->get($x, $y);
      if (!$tile) return 'none';
      if ($tile[3] == 'outside' or $tile[3] == 'tunnel') return $id;
      return 'none';
    };

    return $func;
  }

  function createForest()
  {
  	//$this->clear(['floor', '', '', 'outside']);
  	$this->fill('granite-wall', 'floor');
  	$this->spread('outside', 'tree', 20);
  	$this->spread('room-floor', rfunc('', ['mud','grass']), rint(0,10));
    
  	//pool
    if ($this->random->pass($this->lvl, [1=>.1])) {
      $id = rbet(0.1)? 'mud' : 'water'; //'wet-floor'
      $this->pool(rint(0,12), rint(0,12), $this->replaceOutsideFunc($id), rint(5,15));
      if (rbet(0.3)) $this->spread('outside', 'water', rint(0,5));
    }

    $this->spread('floor', rfunc('i2', 'animal'), rint(0,4)); 
    $this->spread('water', rfunc('', 'water-list'), rint(0,5));
  }

  // function createSwamp()
  // {
  // 	$this->fill('room-floor', 'grass');
  // }

  function createRocks()
  {
  	$this->fill('room-floor', 'rocks');
  }

  function createDesert()
  {
    $this->fill('room-floor', 'sandstone');
  }

}

?>