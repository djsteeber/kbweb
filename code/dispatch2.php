<?php

  # this id needed for the calendar picker
  require_once('model/person.php');
  require_once('comp/calendar/classes/tc_calendar.php');
  require_once('code/webmvc.php');
  require_once('code/htmlutil.php');
  session_start();


  # all dispach goes through here
  # webvm/dispatch.php/controller/function/parm
  # examples
  # webvm/dispatch.php/ -- will call the index_controller
#  SecurityController::$authMethod = 'checkUser';
  $HTML = new HtmlUtil();
  $html = $HTML;
  $config = array('default-controller' => 'index'
                  ,'template' => 'template/template.php'
                  ,'user-class-name' => 'Person' 
                  ,'security-enabled' => true );  



  // note:  these are global variable and are accessable via the 
  //        pages below.  we should change all of these to upper case
  //    controller is tougher since it is used in all of the views, 
  //   for now we will have 2 variables
  $DISPATCHER = new Dispatcher($config);
  $CONTROLLER = $DISPATCHER->dispatch();
  $VIEW = $CONTROLLER->getView();
  $TEMPLATE = $CONTROLLER->getTemplate();
  $STREAMfILE = $CONTROLLER->getStreamFile();


  # echo "user = " . $CONTROLLER->getUser();
  # add a check here to see if the view exists
  if (isset($STREAMfILE['contentType'])) {
    $DISPATCHER->streamFile($STREAMfILE['contentType']
                            ,$STREAMfILE['fileName']);
  } else if ( (isset($TEMPLATE)) and ($TEMPLATE != null))  {
    include($TEMPLATE);
  } else if ($VIEW != '<raw>') {
    include($VIEW);
  } 

  /* TODO
     The security contoller / dispatcher does not pass the appropriate
     actions URI original request to the loginform
     There should also be a error login message appended to the data

  */

  # clean-up
  $DISPATCHER = null;
  $CONTROLLER = null;
  $VIEW = null;
  $TEMPLATE = null;
  $STREAMfILE = null;
?>
