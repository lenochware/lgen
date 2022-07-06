<?php

class BaseController extends pclib\Controller {

//protected $db;

function title($level = null, $title = null)
{
  if (!$title) {
    return $this->app->layout->_TITLE;
  }

  $this->app->layout->bookmark($level, $title, ($level == 1)? $this->app->controller : null);
  $this->app->layout->_TITLE = $title;
}


function reload($action = '') {
  $this->app->redirect($this->app->controller.$action);
}

function back() {
  if ($_POST['back']) {
    $this->app->redirect(array('url' => $_POST['back']));
  }
  else {
    $this->reload();
  }
}

function invalid($form) {
  $e = array();
  foreach($form->invalid as $id => $message) $e[] = $id.':'.$message;
  $this->app->error(implode('<br>', $e));
}

function action($rs)
{
  $action = new pclib\Action($rs);
  $ct = $this->app->newController($action->controller);

  if (!$ct) throw new Exception('Build of '.$action->controller.' failed.');

  return $ct->run($action);
}

public function outputJson(array $data)
{
  header('Content-Type: application/json; charset=utf-8');
  die(json_encode($data, JSON_UNESCAPED_UNICODE/*|JSON_PRETTY_PRINT*/));
}

}

?>