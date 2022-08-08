<?php 

class HomeController extends BaseController
{

function indexAction()
{
  //$this->seed(1658993076);
  $this->app->setSession('seed', $this->seed());

  print "Seed: ". $this->seed() . '<br>';

  $starttime = microtime(true);

  $level = new DefaultLevel(1);
  $level->create();

  $time = round((microtime(true) - $starttime)*1000,2);

  return $this->template('tpl/home/level.tpl', ['map' => $level->html(), 'time' => $time]);
}

function cityAction()
{
  //$this->seed(1659099490);
  $this->app->setSession('seed', $this->seed());

  print "Seed: ". $this->seed() . '<br>';

  $level = new CityLevel();
  $level->create();

  return $this->template('tpl/home/level.tpl', ['map' => $level->html()]);
}

function roomAction()
{
  $this->seed(1658326829);

  $level = new DefaultLevel(1);
  print "Seed: ". $this->seed() . '<br>';

  $level->width = 2;
  $level->height = 2;

  $level->create();

  foreach([[0,0],[0,1],[1,0],[1,1]] as $pos) {
    $room =  new DungeonRoom(1);
    $level->getSector($pos[0],$pos[1])->add($room);    
    $room->init();
    $room->create('layout');
  }

  $level->sectors[1]->room->pattern([[0,1],[1,0]], 'water');

  //pavouci v rozich
  $p = $this->painter($level, 0,0);
  $p->copySize($level->sectors[0]->room)->shrink(1);
  $p->fill(0.5, 0.5, .2, .2, 'spider');
  $p->points([[0,0],[0,1],[1,0],[1,1]], 'spider');

  //obdelnik pres 3/4 levelu
  $p->x = $p->y = 0;
  $p->width = $level->width * $level->sectorWidth;
  $p->height = $level->height * $level->sectorHeight;
  $p->rect(0.5, 0.5, .75, .75, 'tree');
  
  //rozdeleni oblastni na tabulku rows x cols a bunky vyplneni 1 nebo 0
  //$p->grid([2,6,2], [2,6,2], [0,1,0,1,1,1,0,1,0], 'mummy');


  // //test replace_func - vykresli se jen pres inner-wall, ne jinde
  // $p->vline(0.5, 0, 1, ['inner-wall','wall-moss']);
  // $p->hline(0.5, 0, 1, ['inner-wall','wall-moss']);
  // $p->vline(0.25, 0, 1, replace_func('inner-wall', 'door'));

  $sector = $level->getSector(1,1);
  $this->paintSplitterRoom($level, $sector);

  return $level->html();
}

//Nekolik mistnosti tesne vedle sebe bez chodeb
function paintSplitterRoom($level, $sector)
{
  $room = $sector->room;
  $room->setSize(0,0,16,16);
  $room->clear(['floor', '', '', 'room-floor']);

  $p = $this->painter($level, 1,1);
  $p->copySize($sector);

  $p->vline(0.3,0,1,'wall');
  $p->hline(0.6,0.3,1,'wall');

  $p->hline(0.3,0,0.3,'wall');

  $p->vline(0.3 + (1-0.3)/3*2,0.6,1,'wall');


  //vytvori dvere na krizeni zdi
  $doorFunc = function($room, $x, $y, $id)
  {
    $i = 0;
    foreach ([[-1,0],[1,0],[0,1],[0,-1]] as $pos) {
      $tile = $room->get($x + $pos[0], $y + $pos[1]);
      if ($tile[0] == 'wall') $i++;
    }

    if ($i == 3)
        $room->set($x, $y, 'door');

  };

  $room->each($doorFunc, 'wall');
}

function infoAction($x, $y)
{
  $this->seed($this->app->getSession('seed'));

  $level = new DefaultLevel(1);
  $level->create();

  $sx = floor($x / $level->sectorWidth);
  $sy = floor($y / $level->sectorHeight);
  $sector = $level->getSector($sx, $sy);

  $html = "Sector ($sx,$sy)<br>Room " .$sector->room->__toString();

  return $html;
}

function seed($value = null)
{
  if ($value) $this->app->random->seed = $value;
  return $this->app->random->seed;
}

function painter($level, $x, $y)
{
  return new Painter($level, $level->getSector($x,$y)->position());
}


}

 ?>