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

  function list($id)
  {
    if (empty($this->index[$id])) {
      throw new Exception("Index '$id' not found.");
    };

    return $this->index[$id];
  }

  protected function removeComments($s)
  {
    return preg_replace('/\/\/.*/', '', $s);
  }

  function load($path)
  {
    $this->data = json_decode($this->removeComments(file_get_contents($path)),true);
  }

  //BIOMY ??

  function indexLevel($lvl)
  {
    $this->lvl = $lvl;
    $this->index = [];
    foreach($this->data as $id => $obj) {
      if (empty($obj['lvl'])) continue;
      if ($lvl < $obj['lvl'][0] or $lvl > $obj['lvl'][1]) continue;
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
    $x = $this->lvl - $this->get($id)['lvl'][0];
    return $x > 2? $x-2 : 2-$x;
  }

}

 ?>