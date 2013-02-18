<?php

  require_once('model/event.php');
  require_once('model/announcement.php');
  require_once('comp/calendar.php');

  function date_to_dta($date) {
   $stra = getdate(strtotime($date));
   
   return $stra;
  }

  class IndexController extends Controller {

/*TODO:  I would like to have a home page for members and a home page
         for the general population
 */

    private function loadCalendarEvents(&$calendar, &$eventAry) {
      foreach ($eventAry as $evt) {
        foreach($evt->toCalendarEvents() as $ce) {
          $calendar->addEvent($ce);
        }
      }
    }

# uasort($ary, 'call back function');
    public function index( $request = null) {
      $odb = $this->getODB();
      $this->setView('view/index.php');
      $model = array();

      $calendar = new HtmlCalendar();

      $cond = array('cond' => "members_only = 0");
      $eventIds = $odb->fetchIDList('EventType', $cond);
      $idl = join(',', $eventIds);

      $dateStr = date('Y-m-d');

      $cond = array('cond' => "end_dt >= '" . $dateStr ."'"
                              . ' and location = 1'
                              . ' and event_type in (' . $idl . ')'
                   ,'order' => 'start_dt');
      $events = $odb->fetchAll('Event', $cond);
      $this->loadCalendarEvents($calendar, $events);


      $model['calendar'] = $calendar;

      # announcements
      $cond = array('cond' => "expiration_dt >= '" . $dateStr ."'"
                   ,'order' => '1');
      $model['announcement'] = $odb->fetchAll('Announcement', $cond);

      # load in the next 5 events
      $cond = array('limit' => 7
                   ,'cond' => "end_dt >= '" . $dateStr ."'"
                              . ' and location = 1'
                              . ' and event_type in (' . $idl . ')'
                   ,'order' => 'start_dt');
      $events = $odb->fetchAll('Event', $cond);
      $model['events'] = $events;
      

      
      $this->setModel($model);
    }
  }

?>
