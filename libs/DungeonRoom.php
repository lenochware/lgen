<?php 

class DungeonRoom extends Room
{
  function init()
  {
    parent::init();

    if ($this->random->chance(0.8))
    {
      $this->type = 'default';

    } else
    {
      $this->type = $this->random->get(['destruct', 'pit', 'treasure', 'wet']);
    }

    $this->addTag($this->type);
  }

  function populateDefault()
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

  }

 // function layoutWet()
 // {

 // }

  function populateWet()
  {
    $this->fill('room-floor', 'water');
    $this->spread('room-floor', 'wall-moss', rint(1,5));
    $this->spread('room-floor', rfunc('', ['wet-floor','floor']), rint(1,5));
    $this->spread('water', rfunc('', 'water-list'), rint(0,2));
    $this->spread('tunnel', rfunc('', ['wet-floor']), rint(1,5));
  }

  function populateDestruct()
  {
    $this->spread('room-floor', rfunc('', ['wall','small-rock']), rint(1,5));
    $this->spread('room-wall', rfunc('', ['dirt','floor']), rint(1,5));
    $this->spread('tunnel', rfunc('', ['small-rock']), rint(1,5));
  }

  function populatePit()
  {
    $obj = dbget(rget('actor'));

    $this->fill('room-floor', rfunc('', $obj['family'][1]));
    $this->spread('tunnel', rfunc('', ['blood-floor', 'bones']), rint(1,5));
    //$this->fill('room-floor', rget('actor'));
  }

  // function layoutTreasure()
  // {
  //   $this->clear(['granite-wall', '', '', 'outside']);
  //   $this->pool(1,1, ['floor','room-floor'], 8);
  //   $this->each([$this, 'createWalls'], 'room-floor');
  //   $this->setPivot();
  // }

  function populateTreasure()
  {
    $this->spread('room-floor', rfunc('', ['copper-coins','silver-coins']), rint(1,5));
  }

  function populateShop()
  {
    $this->fill('outside', rfunc('i2', ['dirt','floor']));
    $p = new Painter($this->level, $this->sector->position());
    $p->copySize($this);
    $p->points([[0,0.5]], 'door');
  }

}

?>