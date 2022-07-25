<?php 

class Level extends Entity
{
  protected $number = 1;

  public $sectorWidth = 16;
  public $sectorHeight = 16;  

  public $sectors = [];
  public $config;

  function create()
  {
    for ($x=0; $x < $this->width; $x++) { 
      for ($y=0; $y < $this->height; $y++)
      { 
        $sector = new Sector($this, $x, $y);
        $this->setSector($x, $y, $sector);
        $sector->init($this->number, 'dungeon');
      }
    }

    $this->addStairs();

    foreach ($this->sectors as $sector) {
      $sector->create();
    }
  }

  function addStairs()
  {
  	$n = count($this->sectors);
    $this->sectors[rint(0,$n-1)]->room->addTag('stairs-down');  	
  }

  function setSector($x, $y, Sector $sector)
  {
    $this->sectors[$y * $this->height + $x] = $sector;
  }

  function getSector($x, $y)
  {
    if ($x < 0 or $y < 0 or $x >= $this->width or $y >= $this->height) return null;
    return $this->sectors[$y * $this->width + $x];
  }

  function get($x, $y)
  {
    $sx = floor($x / $this->sectorWidth);
    $sy = floor($y / $this->sectorHeight);
    $sector = $this->getSector($sx, $sy);
    if (!$sector) return ['','','','none'];

    return $sector->room->get($x % $this->sectorWidth, $y % $this->sectorHeight);
  }

  function set($x, $y, $id)
  {
    $sx = floor($x / $this->sectorWidth);
    $sy = floor($y / $this->sectorHeight);
    $sector = $this->getSector($sx, $sy);
    if (!$sector) return;

    $sector->room->set($x % $this->sectorWidth, $y % $this->sectorHeight, $id);
  }


  function connect()
  {
    foreach($this->sectors as $sector) {
      $sector->reset();
    }

    foreach($this->sectors as $sector) {
      $sector->randomConnect();
    }
  }

  function isConnected()
  {
    $visited = [];

    $this->visit($visited, $this->getSector(0,0));

    if (count($visited) == count($this->sectors)) return true;
    else return false;

  }

  private function visit(&$visited, $sec)
  {
    if (in_array($sec, $visited)) return;
    $visited[] = $sec;

    foreach($sec->getConnected() as $next) {
      $this->visit($visited, $next);
    }
  }

protected function htmlTile($x, $y)
{
	$tile = $this->get($x, $y);
  $title = '';
  
  for($i = 0; $i < 3; $i++) {
    $id = $tile[$i];
    if (!$id) continue;
    $obj = $this->db->get($id);
    if ($title) $title .= ', ';
    $title .= $id;
    $render = $obj['render'];
  }

  $title .= ', '. $tile[3];

  $click = "loadInfo($x,$y);";

  return paramStr('<span style="color:{color}" title="'.$title.'" onclick="'.$click.'">{char}</span>', $render);
}

function html()
{
  //$this->drawBioms();

  $s = '';

  for ($y=0; $y < $this->sectorHeight*$this->height; $y++) {
    for ($x=0; $x < $this->sectorWidth*$this->width; $x++) { 
      $s .= $this->htmlTile($x, $y);
    }

    $s .= "<br>";
  }

  return "<code style=\"font-size:14px\">$s</code>";	
}

}

?>