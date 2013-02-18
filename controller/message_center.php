<?php
  require_once('model/person.php');
  require_once('model/officer.php');
  require_once('code/class.phpmailer.php');
  require_once('code/class.smtp.php');
  class MessageCenterController extends Controller {


    public function __construct() {
      parent::__construct();
      $this->addSecurityRole('index', 'member');
      $this->addSecurityRole('send', 'member');
    } 
    public function index($request = null) {
      $to_list = array('all' => 'All Members'
                      ,'board' => 'All Board Members'
                      ,'range' => 'Range Officers'
                      ,'webmaster' => 'Web Master');
      $user = $this->getUser();
      if ($user->hasRole('admin')) {
        $to_list['test'] = 'test';
      }

      $model = array('to_list' => $to_list);
      $this->setModel($model);
      $this->setView('view/message_center_form.php');
    }

    public function send($request = null) {
      $batchSize = 10;
      $ary = array();
      $varMap = getMapValue($request, 'data');
      $subject = getMapValue($varMap, 'form_subject');
      $body = getMapValue($varMap, 'form_message');
      $body = stripslashes($body);
      $body_end = <<<EOS
<br/><br/><code>This message was sent via the kenoshabowmen.com website.  Please do not respond to this email, as there is no one monitoring the message_center email box</code>
EOS;

      $toListName = getMapValue($varMap, 'form_to', 'all');
      $people = $this->getEmailRecipients($toListName);

      # initialize the mail sender
      $mail = $this->getMailSender();
      $mail->Subject = $subject;
      $mail->MsgHTML('<body>' . $body . $body_end . '</body>');

      # check here for attachments, if they exist, add them
      $name = $this->addAttachment($varMap, $mail);

      # loop through the address list and send 
      #   in batches of $batchSize addresses
      $addrCount = 0;
      while (count($people) > 0) {
         $person = array_pop($people);
         $addrCount++;
         $mail->AddAddress($person->email, $person->full_name);
         array_push($ary, $person->full_name . ' ' . $person->email);
         # if batch is full or there are no more people in queue i.e. last send
         if (($addrCount >= $batchSize) or (count($people) == 0)) {
           array_push($ary, "--- End Batch --- ");
           $mail->Send();
           $mail->ClearAddresses();
           $addrCount = 0;
         }
      }
      $model = array('to_list' => $ary
                    ,'subject' => $subject
                    ,'message' =>$body);
      if ($name != null) {
         $model['attachment'] = $name;
      }
      $this->setModel($model);
      $this->setView('view/message_center_status.php');
    }

    private function addAttachment(&$varMap, &$mail) {
      $attachment = getMapValue($varMap, 'attachment');
      $name = null;
      if ($attachment != null) {
        $fp = getMapValue($attachment, 'tmp_name');
        $name = getMapValue($attachment, 'name');
        if (($fp != null) and (is_uploaded_file($fp))) {
          $mail->AddAttachment($fp, $name);
        }
      }
      return $name;
    }

    private function getMailSender() {
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->SMTPAuth = true;
      $mail->Host = "mail.kenoshabowmen.com";
      $mail->Username = "message_center@kenoshabowmen.com";
      $mail->Password = "oN3dedPFfF";
      $mail->SetFrom("message_center@kenoshabowmen.com", "KB Message Center");
      $mail->AddReplyTo("message_center@kenoshabowmen.com", "KB Message Center");

      return $mail;
    }

    private function getEmailRecipients($toListName) {
      $odb = new ODB();
      $people = array();
      if ($toListName == 'range') {
        $officers = $odb->fetchAll('Officer');
        foreach($officers as $officer) {
           if ($officer->person->email != null) {
             if (strncmp($officer->position->value, 'range', 5) == 0) {
               array_push($people, $officer->person);
             }
           }
        }
      } else if ($toListName == 'board') {
        $officers = $odb->fetchAll('Officer');
        $people = array();
        foreach($officers as $officer) {
           if ($officer->person->email != null) {
             array_push($people, $officer->person);
           }
        }
      } else if ($toListName == 'all') {
        $cond = array('cond' => 'email is not null');  
        $people = $odb->fetchAll('Person', $cond);
      } else if ($toListName == 'test') {
        $person = new Person();
        $person->first_name = 'Dale';
        $person->last_name = 'Steeber';
        $person->email = 'djsteeber@yahoo.com';
        array_push($people, $person);
      } else if ($toListName == 'webmaster') {
        $person = new Person();
        $person->first_name = 'Web';
        $person->last_name = 'Master';
        $person->email = 'webmaster@kenoshabowmen.com';
        array_push($people, $person);
      }
      return $people;
    }

    private function send_old($request = null) {
      $varMap = getMapValue($request, 'data');
      $subject = getMapValue($varMap, 'form_subject');
      $body = getMapValue($varMap, 'form_message');
      $body = "<html><head><title>" . $subject . "</title></head>"
         ."<body>" . $body . "</body></html>";
      $toListName = getMapValue($varMap, 'form_to', 'all');

      $user = $this->getUser();

      if ($user != null) {
        $from = $user->email;
      } else {
        $from = "unknown@kenoshabowmen.com";
      }


# get this from the web user
      // lets send the message
      $header = "Reply-To: $from\r\n"; 
      $header .= "Return-Path: $from\r\n"; 
      $header .= "From: message_center@kenoshabowmen.com\r\n"; 
      $header .= "Organization: Kenoshabowmen\r\n"; 
      $header .= "MIME-Version:  1.0\r\n"; 
      $header .= "Content-Type: text/html charset=iso-8859-1\r\n"; 
      $to ="dsteeber@localhost";
      $people = null;

      $odb = new ODB();
      if ($toListName == 'range') {
        $officers = $odb->fetchAll('Officer');
        $people = array();
        foreach($officers as $officer) {
           if ($officer->person->email != null) {
             if (strncmp($officer->position->value, 'range', 5) == 0) {
               array_push($people, $officer->person);
             }
           }
        }
      } else if ($toListName == 'board') {
        $officers = $odb->fetchAll('Officer');
        $people = array();
        foreach($officers as $officer) {
           if ($officer->person->email != null) {
             array_push($people, $officer->person);
           }
        }
      } else if ($toListName == 'all') {
        $cond = array('cond' => 'email is not null');  
        $people = $odb->fetchAll('Person', $cond);
      } else if ($toListName == 'webmaster') {
        $person = new Person();
        $person->first_name = 'Web';
        $person->last_name = 'Master';
        $person->email = 'webmaster@kenoshabowmen.com';
        $people = array($person);
      }
       

      $ary = array();
      if ($people != null) {
        foreach ($people as $person) {
           $to = $person->email;
           array_push($ary, $to);
#           mail($to, $subject, $body, $header);
        }
      }
      $this->setModel($ary);
      $this->setView('view/message_center_status.php');
    }
  }

?>
