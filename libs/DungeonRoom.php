<?php 

class DungeonRoom extends Room
{

  function create()
  {
  	parent::create();
  	
    if ($this->random->chance(0.8))
    {
      $this->type = 'default';

    } else
    {
      $this->type = $this->random->get(['destruct', 'pit', 'treasure', 'wet']);
    }

    $this->addTag($this->type);
    $this->callCreate($this->type);    
  }

  function createDefault()
  {
    $n = $this->random->int2(0, $this->size() / 2);
    $this->spread('room-floor', rfunc('i2', 'actor'), $n);

    if (rbet(0.2))
      $this->spread('room-floor', rfunc('', ['mud','dirt']),  $this->random->int(1,10));


    if (rbet(xtr($this->lvl, [1, 10], [0.1, 0.5]) )) {
      $this->fill('room-floor', 'dirt');
    }

    // if (rbet(xtr($this->lvl, [1, 10], [0.1, 0.5]) ))
    // {
    //   $obj = dbget(rget('actor'));

    //   $this->fill('room-floor', rfunc('', $obj['family'][1]));
    //   //$this->fill('room-floor', rget('actor'));
    // }

    $this->each([$this, 'onSpawn']);
    //$this->each([$this, 'tunnelWalls'], 'tunnel');
  }


  function tunnelWalls($obj, $x, $y, $id)
  {
    if ($id != 'tunnel') return;
    foreach ([[-1,0],[1,0],[0,1],[0,-1]] as $pos) {
      $tile = $obj->get($x + $pos[0], $y + $pos[1]);
      if ($tile[3] == 'outside')
        $obj->set($x + $pos[0], $y + $pos[1], ['tunnel-wall', 'wall-moss']);
    }
  }


  function createWet()
  {
    $this->fill('room-floor', 'water');
    $this->spread('room-floor', 'wall-moss', rint(1,5));
    $this->spread('room-floor', rfunc('', ['wet-floor','floor']), rint(1,5));
    $this->spread('water', rfunc('', 'water-list'), rint(0,2));
    $this->spread('tunnel', rfunc('', ['wet-floor']), rint(1,5));
  }

  function createDestruct()
  {
    $this->spread('room-floor', rfunc('', ['wall','small-rock']), rint(1,5));
    $this->spread('room-wall', rfunc('', ['dirt','floor']), rint(1,5));
    $this->spread('tunnel', rfunc('', ['small-rock']), rint(1,5));
  }

  function createPit()
  {
    $obj = dbget(rget('actor'));

    $this->fill('room-floor', rfunc('', $obj['family'][1]));
    $this->spread('tunnel', rfunc('', ['blood-floor', 'bones']), rint(1,5));
    //$this->fill('room-floor', rget('actor'));
  }

  function createTreasure()
  {
    $this->spread('room-floor', rfunc('', ['copper-coins','silver-coins']), rint(1,5));
  }

  function createShop()
  {
    $this->fill('outside', 'floor');
    $p = new Painter($this->level, $this->sector->position());
    $p->copySize($this);
    $p->points([[0,0.5]], 'door');
  }

}

?>