<?php

  require_once('model/event.php');
  class EventController extends EditableController {

    public function __construct() {
      parent::__construct('Event');
      $this->addSecurityRole('maint', 'admin');
    }

    public function index() {
      $model = array();
      #$evtModel = new EventModel();
      $odb = $this->getODB();
      $cond = array('cond' => "members_only = 'N'"
                            . " and value like '%shoot%'");
      $etShootIds = $odb->fetchIDList('EventType', $cond);
      $shootIds = join(',', $etShootIds);
      $cond = array ('cond' => ' location = 1 '
                          . ' and event_type in (' . $shootIds .')'
                    ,'order' => 'start_dt');
      $model['shoot'] = $odb->fetchAll('Event', $cond);
      $etLesson = $odb->fetchLookup('EventType', 'lesson');
      $cond = array ('cond' => ' location = 1 '
                          . ' and event_type = '. $etLesson->id
                    ,'order' => 'start_dt');
      $model['lesson'] = $odb->fetchAll('Event', $cond);

      $etLeague = $odb->fetchLookup('EventType', 'league');
      $cond = array ('cond' => ' location = 1 '
                          . ' and event_type = ' . $etLeague->id
                    ,'order' => 'start_dt');
      $model['league'] = $odb->fetchAll('Event', $cond);

      $this->setModel($model);
      $this->setView('view/event.php');
    }

  }

?>
