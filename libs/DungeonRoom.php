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
    
    call_user_func([$this, 'create' . ucfirst($this->type)]);
  }

  function createDefault()
  {
    //vytvorit zdi a outer? obj. ProceduralRoom() n. TemplateRoom()
    $this->clear();

    //musi byt pred prepsanim floor...
    $n = $this->random->int2(0, $this->size() / 2);
    $this->spread('floor', rfunc('i2', 'actor'), $n);

    if (rbet(0.2))
      $this->spread('floor', rfunc('', ['mud','dirt']),  $this->random->int(1,10));


    if (rbet(xtr($this->lvl, [1, 10], [0.1, 0.5]) )) {
      $this->fill('floor', 'dirt');
    }


    if (rbet(xtr($this->lvl, [1, 10], [0.1, 0.5]) ))
    {
      $obj = dbget(rget('actor'));

      $this->fill('floor', rfunc('', $obj['family'][1]));
      //$this->fill('floor', rget('actor'));
    }

    //$this->fill('floor', pass('grass'));

    // $this->fill('wall', $this->random->func('', ['granite-wall','decor-wall']));
    //outer?

    $this->each([$this, 'onSpawn']);
  }

  function createWet()
  {
    $this->clear();
  }

  function createDestruct()
  {
    $this->clear();    
  }

  function createPit()
  {
    $this->clear();
  }

  function createTreasure()
  {
    $this->clear();
  }

  function createFortress()
  {
    $this->clear();    
  }

}

?>