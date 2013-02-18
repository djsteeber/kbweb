<?php

  require_once('model/trophy_room.php');
  class TrophyRoomController extends EditableController {

    public function __construct() {
      parent::__construct('TrophyRoom', 'trophy_room');
      $this->addSecurityRole('maint', 'admin');
    }


    public function index() {
      $model = array();
      $odb = $this->getODB();

      $cond = array ('order' => 'tr_date desc');
      $model['trophy_room'] = $odb->fetchAll('TrophyRoom', $cond);


      $this->setModel($model);
      $this->setView('view/trophy_room.php');
    }
  }

?>
