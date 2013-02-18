<?php

  class DateHelper {
    public static function getDateArray($year, $month, $day) {
      $dtstr = "$year-$month-$day";
      $dta = getdate(strtotime($dtstr));

      return $dta;
    } 

    public static function addDays($dta, $interval) {
      $dtstr = $dta['year'] . '-' . $dta['mon'] . '-' . $dta['mday'];
      $rtn = getdate(strtotime($dtstr . " +7 days"));
      return $rtn;
    }
    public static function compareDates($dta1, $dta2) {
      # same day = 0
      # dta1 is before dta2 = -1
      # dta1 is after dta2 = 1

      if ($dta1['year'] == $dta2['year']) {
        if ($dta1['yday'] == $dta2['yday']) {
          $rtn = 0;
        } else if ($dta1['yday'] < $dta2['yday']) {
          $rtn = -1;
        } else {
          $rtn = 1;
        }
      } else if ($dta1['year'] < $dta2['year']) {
        $rtn = -1;
      } else {
        $rtn = 1;
      }
      return $rtn;
    }
  }

  class CalendarEvent {
    public $name;
    public $start;
    public $end;
    public $link;
    public $event_type;

    public function surrounds($dta) {
      return ((DateHelper::compareDates($this->start, $dta) == 0)
             or (DateHelper::compareDates($this->end, $dta) == 0)
             or ((DateHelper::compareDates($this->start, $dta)
                   + DateHelper::compareDates($dta, $this->end)) != 0));
    }

  }


  # class to construct an html calendar object
  class HtmlCalendar {
    
    private static $headings = array('S','M','T','W','T','F','S');
    private $events = array();


    public function __construct() {
    }

      
    public function addEvent($calEvt) {
      array_push($this->events, $calEvt);
    }
  


    function buildCalendarArray($dta) {
      $month = array();
      $week = array();
      $dow = $dta['wday'];
      $max_days = cal_days_in_month(CAL_GREGORIAN, $dta['mon']
                                     ,$dta['year']);

      for ($inx = 1; $inx <= $max_days + $dow; $inx++) {
        if ($inx <= $dow) {
          $val = '&nbsp;';
        } else {
          $val = $inx - $dow;
        }
        array_push($week, $val);
      }
      $cnt = 0;
      while ($cnt < count($week)) {
        $ary = array_slice($week, $cnt, 7);
        while (count($ary) < 7) {
          array_push($ary, '');
        }
        array_push($month, $ary);
        $cnt += 7;
      }

      return $month;
    }

    # deprecated
    function getEvent($dta) {
      foreach ($this->events as $event) {
        if ($event->surrounds($dta)) {
          return $event;
        }
      }
      return null;
    }

    function getEvents($dta) {
      $ary = array();
      foreach ($this->events as $event) {
        if ($event->surrounds($dta)) {
          array_push($ary, $event);
        }
      }
      return $ary;
    }

    function getEventsToolTipText($events) {
      $txt = '<ul class="nodots">';
      foreach($events as $event) {
       $txt .= '<li>' . $event->name . '</li>';
      }
      $txt .= '</ul>';
      return $txt;
    }

    function getDateInfo($year, $month, $day) {
      $hasEvents = false;
      $dta = DateHelper::getDateArray($year, $month, $day);
      $today = getdate();
      $rslt = DateHelper::compareDates($today, $dta);
      $tooltip = '';
      if ($rslt > 0) {
        $style = "past";
      } else {
       $events = $this->getEvents($dta);
       if (count($events) == 0) {
         $style = 'future';
       } else {
         $style = 'calendar-event';
         $hasEvents = true;
         $tooltip = $this->getEventsToolTipText($events);
       }
      }
      return array('style' => $style
                   ,'hasEvents' => $hasEvents
                   ,'tooltip' => $tooltip);
    }

    public function showMonth($year, $month) {
      $dta = DateHelper::getDateArray($year, $month, 1);
      $month = $this->buildCalendarArray($dta);
      echo '<table class="calendar" cellspacing="0" cellpadding="0">' . PHP_EOL;
      
 
      #heading 
      echo '<caption>' . $dta['month'] . ' ' . $dta['year'] 
            . '</caption>' . PHP_EOL;
      echo '<tr>';
      foreach (HtmlCalendar::$headings as $heading) {
        echo '<th>' . $heading . '</th>' . PHP_EOL;
      }

      # table of month
      foreach ($month as $week) {
        echo '<tr>' . PHP_EOL;
        foreach ($week as $day) {
          $dateInfo = $this->getDateInfo($dta['year'], $dta['mon'], $day);
          $dateStyle = $dateInfo['style'];
          #$dateStyle = $this->getDateStyle($dta['year'], $dta['mon'], $day);
          echo '<td class="' . $dateStyle . '">'  . PHP_EOL;
          if ($dateInfo['hasEvents']) {
            echo '<a class="tooltip" href="#">' . $day;
            echo '<span>' . $dateInfo['tooltip'] . '</span>';
            echo '</a>';
          } else {
            echo $day  . PHP_EOL;
          }
          echo '</td>' . PHP_EOL;
        }
        echo '</tr>' . PHP_EOL;
      }
      echo '</table>' . PHP_EOL;
    }

    public function showKey() {
      echo '<ul style="list-style-type:none;">' . PHP_EOL;
      echo '<li class="horizontal-list">' . PHP_EOL;
      echo 'Key:';
      echo '</li>' . PHP_EOL;
      echo '<li class="horizontal-list">' . PHP_EOL;
      echo '<span id="calendar-event-lesson">&nbsp;&nbsp;</span>';
      echo ' - Lessons';
      echo '</li>' . PHP_EOL;
      echo '<li class="horizontal-list">' . PHP_EOL;
      echo '<span id="calendar-event-shoot">&nbsp;&nbsp;</span>';
      echo ' - Shoots';
      echo '</li>' . PHP_EOL;
      echo '</ul>' . PHP_EOL;
    }

  }


?>
