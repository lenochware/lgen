<?php 

class DungeonRoom extends Room
{

  function create()
  {
    if ($this->random->chance(0.8))
    {
      $this->type = 'default';

    } else
    {
      $this->type = $this->random->get(['destruct', 'pit', 'treasure', 'wet', 'fortress']);
    }

    $this->callCreate($this->type);    
  }

  function createDefault()
  {
    $n = $this->random->int2(0, $this->size() / 2);
    $this->spread('room-floor', rfunc('i2', 'actor'), $n);

    //$this->fill('tunnel', 'water', 10);

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
  }

  function createWet()
  {
    $this->fill('room-floor', 'water');
    $this->spread('room-floor', 'wall-moss', rint(1,5));
    $this->spread('room-floor', rfunc('', ['wet-floor','floor']), rint(1,5));
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

  function createFortress()
  {
  }

}

?>