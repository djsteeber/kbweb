<?php

  require_once('model/officer.php');
  class OfficerController extends EditableController {

    public function __construct() {
       parent::__construct('Officer');
       $this->addSecurityRole('maint', 'admin');
    }

  }

?>
