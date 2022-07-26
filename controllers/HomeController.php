<?php 

class HomeController extends BaseController
{

function indexAction()
{
  //$this->seed(1658326829);
  $this->app->setSession('seed', $this->seed());

  print "Seed: ". $this->seed() . '<br>';

  $level = new DefaultLevel(1);
  $level->create();

  return $this->template('tpl/home/level.tpl', ['map' => $level->html()]);
}

function cityAction()
{
  //$this->seed(1658326829);
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
    $room->init(8,8);
    $level->getSector($pos[0],$pos[1])->add($room);    
  }

  $level->sectors[1]->room->pattern([[0,1],[1,0]], 'water');

  $p = $this->painter($level, 0,0);
  $p->copySize($level->sectors[0]->room)->shrink(1);
  $p->fill(0.5, 0.5, .2, .2, 'spider');
  $p->points([[0,0],[0,1],[1,0],[1,1]], 'spider');

  $p->x = $p->y = 0;
  $p->width = $level->width * $level->sectorWidth;
  $p->height = $level->height * $level->sectorHeight;
  $p->rect(0.5, 0.5, .75, .75, 'tree');


  // replace_func
  // $p->vline(0.5, 0, 1, ['inner-wall','wall-moss']);
  // $p->hline(0.5, 0, 1, ['inner-wall','wall-moss']);
  //$p->vline(0.75, 0, 1, replace_func('inner-wall', 'door'));  

  return $level->html();
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