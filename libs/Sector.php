<?php 

class Sector extends Entity
{
	public $level;
	protected $x;
	protected $y;

	protected $width = 10;
	protected $height = 10;

	public $room;
	public $biom;
	public $connected;

  function __construct(Level $level, $x, $y)
  {
  	parent::__construct();
    $this->level = $level;
    $this->x = $x;
    $this->y = $y;
    $this->reset();
  }

  function create($lvl, $biom)
  {
  	$this->biom = $biom;
  	$room = $this->createRoom($lvl, $biom);
    $this->add($room);
  	$room->create();
  }

  function add(Room $room)
  {
    $this->room = $room;
    $room->setSector($this);
  }

  function reset()
  {
  	$this->tunnels = [];
  	$this->connected = false;
  }

  function randomConnect()
  {
  	$nb = $this->getNeighbours();
  	if (!$nb) return;


 		$this->connected = $this->random->get($nb);
  }

	protected function getNeighbours()
	{
		$neighbours = [];

		$addnb = function($cur, $x, $y) use (&$neighbours)
		{
			$sec = $cur->level->getSector($x, $y);
			if (!$sec) return;
			if ($sec->connected == $cur) return;

			$neighbours[] = $sec;
		};

		$addnb($this, $this->x-1, $this->y);
		$addnb($this, $this->x+1, $this->y);
		$addnb($this, $this->x, $this->y-1);
		$addnb($this, $this->x, $this->y+1);

		return $neighbours;
	}

	function strConnected()
	{
		if (!$this->connected) return 'none';
		$px = $this->connected->x - $this->x;
		$py = $this->connected->y - $this->y;

		if ($px > 0) return 'R';
		if ($px < 0) return 'L';
		if ($py > 0) return 'D';
		if ($py < 0) return 'U';
	}

  function createRoom($lvl, $biom)
  {
    if ($biom == 'dungeon') $room = new DungeonRoom($lvl, $biom);
    elseif ($biom == 'hell') $room = new HellRoom($lvl, $biom);
    elseif (in_array($biom, ['forest', 'rocks', 'desert', 'water'])) $room = new WildRoom($lvl, $biom);
    else throw new Exception('Unknown biom.');

    $room->setSize(rint([5,8]), rint([5,8]));

    return $room;
  }

}

?>