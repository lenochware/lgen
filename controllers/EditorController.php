<?php 

class EditorController extends BaseController
{

protected $form;

function init()
{
  parent::init();
  $this->form = new pclib\Form('tpl/editor/level.tpl', 'level-form');
}

function newLevel()
{
  [$name, $number] = explode('-', $this->form->values['level'] ?: 'cellars-1');

  if ($name == 'cellars')
    $level = new CellarsLevel($number);
  elseif ($name == 'city')  
    $level = new CityLevel('city', $number);
  else
    throw new Exception('Unknown level.');
  
  $level->create();
  return $level;  
}  

function indexAction()
{
  $seed = $this->form->values['seed'] ?: $this->seed();
  $this->seed($seed);
  $this->app->setSession('seed', $seed);

  $starttime = microtime(true);

  $level = $this->newLevel();

  $time = round((microtime(true) - $starttime)*1000,2);

  $this->form->values += ['map' => $level->html(), 'time' => $time, 'current_seed' => $seed];
  return $this->form;
}

function updateAction()
{
  $this->form->saveSession();
  $this->app->deleteSession('selected');
  $this->app->redirect('editor');
}

function playAction()
{
  $this->app->redirect(['url' => '../js/styx']);
}

function roomAction()
{
  $this->seed(1658326829);

  $level = new CellarsLevel(1);
  print "Seed: ". $this->seed() . '<br>';

  $level->width = 2;
  $level->height = 2;

  $level->create();

  foreach([[0,0],[0,1],[1,0],[1,1]] as $pos) {
    $room =  new Room(1);
    $level->getSector($pos[0],$pos[1])->add($room);    
    $room->init('default');
    $room->rectangleLayout();
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
  $this->app->setSession('selected', ['x' => (int)$x, 'y' => (int)$y]);

  $this->seed($this->app->getSession('seed'));

  $level = $this->newLevel();

  $sx = floor($x / $level->sectorWidth);
  $sy = floor($y / $level->sectorHeight);
  $sector = $level->getSector($sx, $sy);

  $html = "Sector ($sx,$sy)<br>Room " .$sector->room->__toString();

  return $html;
}

function painter($level, $x, $y)
{
  return new Painter($level, $level->getSector($x,$y)->position());
}


}

 ?>