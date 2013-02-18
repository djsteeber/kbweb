<?php
  require_once('model/lookup_types.php');
  require_once('model/person.php');
  require_once('model/membership_registration.php');



  class MembershipController extends EditableController {

    public function __construct() {
      parent::__construct('Membership');
      $this->addSecurityRole('maint', 'admin');
      $this->addSecurityRole('workhours', 'workhours');
      $this->addSecurityRole('registration', 'admin');
    }

    public function index($data = null) {
      $this->setView("view/membership.php");
    }

    public function registration($request = null) {
      $data = getMapValue($request, 'data');
      $action = getMapValue($data, 'registration_action', 'view');

      $mrModel = new MembershipRegistration();
      $model = array('mrModel' => $mrModel
                    ,'submit' => '/index.php/registration/submit');
      // set renewal flag here
      $this->setModel($model);

      $this->setView("view/membership_renewal.php");
    }

    public function workhours($request = null) {
      $data = getMapValue($request, 'data');
      $action = getMapValue($data, 'action');
      $odb = $this->getODB();

      if ($action == 'save') {
        foreach ($data as $field => $value) {
          $fspl = split(':', $field);
          if ((count($fspl) > 1) and ($fspl[0] == 'nwh')
              and (trim($value) != '')) {
            $membership = $odb->fetch('Membership', $fspl[1]);
            if ($membership != null) {
              $membership->work_hours = $value;
              $odb->save($membership);
            }
          }
        } // for loop
      } // action save
 
      $odb = $this->getODB();
      $model = array();
      $membersList = $odb->fetchAll('Membership');
      usort($membersList, array('Membership', 'sortByLastName'));

      //sort this here
      $model['members'] = $membersList;


      $this->setModel($model);
      $this->setView('view/membership_workhours.php');
    }

    
  }
?>
