<?php 


class DungeonBase implements \pclib\IService
{
  protected $data;
  public $index;
  protected $lvl;

  function __construct($path)
  {
    $this->load($path);
  }

  function get($id)
  {
    if (empty($this->data[$id])) {
      throw new Exception("Object '$id' not found.");
    };

    return $this->data[$id];
  }

  function has($id)
  {
    return !empty($this->data[$id]);
  }

  function set($id, $obj)
  {
    $this->data[$id] = $obj;
  }

  // function add($id, $obj = [])
  // {
  //   if ($obj['extends']) {
  //     $obj = array_merge($this->get($obj['extends']), $obj);      
  //   }

  //   $this->set($id, $obj);
  // }

  function list($id)
  {
    $obj = $this->data[$id] ?? null;
    if ($obj and $obj['list']) {
      return $obj['list'];
    }

    if (empty($this->index[$id])) {
      throw new Exception("Index '$id' not found.");
    };

    return $this->index[$id];
  }

  protected function removeComments($s)
  {
    return preg_replace('/\/\/.*/', '', $s);
  }

  function load($dir)
  {
    $loader = new Loader($dir);
    $objects = $loader->loadObjects();
    $this->data = array_merge($objects['tiles'], $objects['items'], $objects['actors'], $objects['levels']);
  }

  //BIOMY ??

  function indexLevel($lvl)
  {
    $this->lvl = $lvl;
    $this->index = [];
    foreach($this->data as $id => $obj) {
      if (empty($obj['lvl'])) continue;
      if ($lvl-2 > $obj['lvl'] or $lvl+2 < $obj['lvl']) continue;
      $this->index[$obj['family'][1]][] = $id;
      $this->index[$obj['family'][0]][] = $id;
    }

    $cmp = fn($a, $b) => $this->rarity($a) <=> $this->rarity($b);

    foreach($this->index as $id => $list) {
      usort($this->index[$id], $cmp);
    }

    //dump($this->rarity('rat'), $this->rarity('scorpion'), $this->index);
  }

  protected function rarity($id)
  {
    $x = $this->lvl - $this->get($id)['lvl'];
    return $x > 2? $x-2 : 2-$x;
  }

}

 ?>