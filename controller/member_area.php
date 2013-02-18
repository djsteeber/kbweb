<?php

  require_once('model/event.php');
  require_once('model/lookup_types.php');
  class MemberAreaController extends Controller {


    public function __construct() {
      $this->addSecurityRole('index', 'member');
      $this->addSecurityRole('index', 'generic_member');
      $this->addSecurityRole('securefile', 'member');
    }

    public function index() {
      $odb = $this->getODB();
      $this->setView('view/member_area.php');
      $model = array();
 
      $cond = array('cond' => "value = 'membership'"); 
      $docType = $odb->fetchFirst('DocumentType', $cond);

      # membership docs 
      $docType = $odb->fetchLookup('DocumentType', 'membership');
      $model['membership'] = $docType->getDocumentList('time');

      
      # membership docs 
      $docType = $odb->fetchLookup('DocumentType', 'meeting');
      $model['meeting'] = $docType->getDocumentList('time');


      # membership docs 
      $docType = $odb->fetchLookup('DocumentType', 'newsletter');
      $model['newsletter'] = $docType->getDocumentList('time');



      #work parties
      $workparty = $odb->fetchLookup('EventType', 'workparty');
      $cond = array('cond' => "event_type = $workparty->id"
                       . " and end_dt >= '" . date('Y-m-d') . "'"
                    ,'order' => 'start_dt');
      $model['work_party'] = $odb->fetchAll('Event', $cond);


      $this->setModel($model);
    }


    function getFilename($data) {
      $fileName = null;

      if (isset($data)
          and isset($data['data'])
          and isset($data['data']['filename'])) {
        $fileName = $data['data']['filename'];
      }

      return $fileName;
    }

    # to alert echo <script>alert("some message here")</script>;

    #check if the file exists, if not, just return to the main page
    # refactor:  I'd like to make the secure file retrieval a controller
    #            by itself
    public function securefile($data = null) {
      $fileName = $this->getFileName($data);
      #error_log('in call to securefile with data');
      #error_log('doc = ' . $fileName);
      $docRoot = $_SERVER['DOCUMENT_ROOT'];
      #$file = $docRoot . '/_secure_docs/' . $fileName;
      $file = $docRoot . $fileName;
      #error_log('file = ' . $file);

      if (file_exists($file)) {
        $this->setStreamFile('application/pdf'
                             ,$file);
      } else {
         $this->index();
      }
    }

    public function document($data = null) {
       # first pass, ignore the data, and just present the upload form
       $this->setView('view/document_load.php');
       // no model set a this time

    }



  }

?>
