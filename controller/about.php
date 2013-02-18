<?php
 require_once('model/officer.php');
  class AboutController extends Controller {

    
    public function index() {
      $odb = $this->getODB();
      $cond = array('order' => 'position');
      $model = $odb->fetchAll('Officer', $cond);

      $this->setModel($model);
      $this->setView('view/about.php');
    }
  }

?>
