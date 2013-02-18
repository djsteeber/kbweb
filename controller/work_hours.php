<?php

  require_once('model/person.php');
  require_once('model/membership.php');
  class WorkHoursController extends Controller {

    public function __construct() {
      parent::__construct('Event');
      $this->addSecurityRole('maint', 'admin');
    }

    public function index($request = null) {
      $data = getMapValue($request, 'data');
      $action = getMapValue($data, 'action', 'view');
      $odb = $this=>getODB();

      if ($action == 'view') {
// get a list of the memberships, and build a table for entry
        
      } else {
      }
      $model = array();

      $this->setModel($model);
      $this->setView('view/event.php');
    }

  }

?>
