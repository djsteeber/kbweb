<?php

  require_once('model/notification.php');
  class NotificationController extends EditableController {

    public function __construct() {
      parent::__construct('Notification');
    }

    public function index() {
      $model = array();
      $this->setView('view/notification.php');
    }

    public function subscribe($data = null) {
      echo "user subscribed";
      print_r($data);
      // if new, store the record with status of pending
      //  
      $this->setView('view/notification_subscribe.php');
    }

    public function confirmation($data = null) {
      print_r($data);
      $this->setView('view/notification_confirm.php');
    }

  }

?>
