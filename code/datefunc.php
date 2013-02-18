<?php

   class MyDate {
     public $month;
     public $day;
     public $year;

     private $monthLU = array( 1 => 'Jan'
                             , 2 => 'Feb'
                             , 3 => 'Mar'
                             , 4 => 'Apr'
                             , 5 => 'May'
                             , 6 => 'Jun'
                             , 7 => 'Jul'
                             , 8 => 'Aug'
                             , 9 => 'Sep'
                             ,10 => 'Oct'
                             ,11 => 'Nov'
                             ,12 => 'Dec');

     public function __construct($dtStr) {
       $dta = split('/', $dtStr);
       #echo 'initializing MyDate ' . $dtStr . PHP_EOL;
       $this->year = intval($dta[2]);
       $this->month = intval($dta[0]);
       $this->day = intval($dta[1]);
     }

     
    
     public function equals($object) {
       return (($this->year == $object->year)
               and ($this->month == $object->month)
               and ($this->day == $object->day));
     }

     public function getMonthStr() {
       return $this->monthLU[$this->month];
     }

     public function getMonthDayStr() {
       $str = $this->getMonthStr() . ' ' . $this->day;
       return $str;
     }
     public function getMonthDayYearStr() {
       $str = $this->getMonthStr() . ' ' . $this->day . ', ' . $this->year;
       return $str;
     }
   }  # end MyDate

   function getDateRangeStr($dtStart, $dtEnd) {
      $dts = new MyDate($dtStart);
      $dte = new MyDate($dtEnd);
      $sep = ' - ';

      if ($dts->equals($dte)) {
        $rtn = $dts->getMonthDayStr();
      } else if ($dts->month == $dte->month) {
        $rtn = $dts->getMonthDayStr() . $sep . $dte->day;
      } else {
        $rtn = $dts->getMonthDayStr() . $sep . $dte->getMonthDayStr();
      }

      return $rtn;
   }

   function getDateStr($dt) {
     $mdt = new MyDate($dt);
     return $mdt->getMonthDayYearStr();
   }


?>
