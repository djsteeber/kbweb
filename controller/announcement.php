<?php

  require_once('model/announcement.php');
  class AnnouncementController extends EditableController {

    public function __construct() {
      parent::__construct('Announcement');
      $this->addSecurityRole('maint', 'admin');
      $this->addSecurityRole('maint', 'announcement');
    }

  }

?>
