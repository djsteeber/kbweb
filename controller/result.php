<?php

  require_once('code/webmvc.php');
  require_once('model/event.php');
  require_once('model/result.php');
  class ResultController extends Controller {

    public function index() {
      $model = array();
      $odb = new ODB();

      # get these from the db
      $shoot = 4;
      $league = 8;

      # select all results, get the event object, and inject in the 
      # results link, then return the event

      $cond = array ();
      $results = $odb->fetchAll('Result');
      $model['shoot_results'] = $results;
      $model['league'] = array();

      $this->setModel($model);
      $this->setView('view/result.php');
    }
  }

?>
