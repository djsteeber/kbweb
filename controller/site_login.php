<?php
  require_once('model/event.php');

  /*
   * Simple controller class that sends you to the login page
   * or back to the index page on logout.  on logout
   * the logout function also removes the cookies
   * 
   *
   *
   */
# NOT SURE THIS IS USED
  class SiteLoginController extends EditableController {

    public function __construct() {
      parent::__construct('event', true);
      $this->addSecurityRole('maint', 'admin');
    }

    public function index() {
      $this->setView('view/loginform.php');
    }


    public function logout($data = null) {
      setcookie('kbuser', '', time() -3600, '/');
      // also set the cookie variable so that this
      // initiation also clears out the web userA
      unset($_COOKIE['kbuser']);
      $this->setView('view/index.php');
    }

  }

?>
