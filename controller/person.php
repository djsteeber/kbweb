<?php

  require_once('model/person.php');
  class PersonController extends EditableController {

    public function __construct() {
      parent::__construct('Person');
      $this->addSecurityRole('maint', 'admin');
      $this->addSecurityRole('preferences', 'member');
      $this->addSecurityRole('preferences', 'admin');
      $this->addSecurityRole('arup', 'admin');
    }


     
    /**
     * this function show the user preferences, and allows them to 
     * update them
     */
    public function preferences($request = null) {
      $data = getMapValue($request, 'data');
      $command = getMapValue($data, 'command', '');

      # do not set a model, this is base on the user
      #  in the controller
      $user = $this->getUser();
      if ($user == null) {
        # just for development, remove this
        $user = new Person();
      }
      $odb = $this->getODB();
      $model = array('user' => $user);
      switch ($command) {
        case 'save':
            $this->preferences_save($user, $data);
            $model['message'] = 'Profile Saved';
            break;
        case 'change_password':
            $msg = $this->preferences_change_password($user, $data);
            $model['message'] = $msg;
            break;
      }
      $this->setModel($model); 
      $this->setView('view/person_preferences.php');
    }

    private function preferences_save($user, $data) {
      $emailLoginMatch = ($user->login == $user->email);

      #convert help variables
      #TODO add some validation here, we should remove the password fields
      $user->setFieldValues($data);
      if ($emailLoginMatch) {
        # if the email and login match, set the login to the email
        # in the save preferences
        if (($user->email != null) or (trim($user->email) != '')) {
          $user->login = $user->email;
        }
      }
      $user->membership->setFieldValues($data);
      $odb = $this->getODB();
      $odb->save($user->membership); // save does not do a deep save
      $odb->save($user);
    }

    # this could change up to the security controller if needed
    private function preferences_change_password($user, $data) {
      $msg = '';
      $curPwd = getMapValue($data, 'password_current');
      $newPwd = getMapValue($data, 'password_new');
      $confirmPwd = getMapValue($data, 'password_confirm');

      $curPwd = $user->encryptPassword($curPwd);
      if ($curPwd == $user->password) {
        if ($newPwd == $confirmPwd) {
          $user->password = $user->encryptPassword($newPwd);
          $odb = $this->getODB();
          $odb->save($user);
          $msg = 'Password Changed';
        } else {
          $msg = 'new password and confirm password do not match';
        }
      } else {
          $msg = 'invalid current password';
      }
      return $msg;
    }
    # admin reset user password
    public function arup($request = null) {
      $data = getMapValue($request, 'data');
      $action = getMapValue($data, 'action', 'view');
      print_r($data);
     
      $odb = $this->getODB();
      $model = array();
      $cond = array('cond' => "role like '%member%'"
                   ,'order' => "last_name, first_name");
      $model['people'] = $odb->fetchAll('Person', $cond);
      if ($action == 'reset') {
        $id = getMapValue($data, 'person_id');
        $password = getMapValue($data, 'password');
        $person = $odb->fetch('Person', $id);
        $person->password = $person->encryptPassword($password);
        $odb->save($person);
        $model['message'] = "user $person->full_name password has been reset";
      }
      $this->setModel($model);
      $this->setView("view/person_arup_view.php");
    }
  }

?>
