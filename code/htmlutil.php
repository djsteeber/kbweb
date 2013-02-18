<?php
  class HtmlUtil {
    public function link($name, $ary) {
      # $ref = "/dispatch.php";
      $ref = "/index.php";
      if (is_array($ary)) {
        if (isset($ary['controller'])) {
          $ref .= '/' . $ary['controller'];
          if (isset($ary['function'])) {
            $ref .= '/' . $ary['function'];
          }
        }
        $sep = '?';
        if (isset($ary['data'])) {
          foreach($ary['data'] as $key => $val) {
            $ref .= $sep . $key . '=' . $val;
            $sep = "&";
          }
        }
      } else { # just  a link
        $ref = $ary;
      }
      # need to add in the array stuff here
      $link = '<a href="' . $ref . '">' . $name . '</a>';
      echo $link;
    }

    public function getTextBox($name, $value = '', $size = 20) {
      $html = '<input type="text" size="' . $size . '" name="' . $name
                     . '">' . $value . '</input>';
      return $html;
    }
    public function getTextArea($name, $value = '', $cols = 100, $rows = 5) {
      $html = '<textarea name="' . $name
                     . '">' . $value . '</textarea>';
      return $html;
    }

    public function getCheckbox($name, $checked = false) {
      $html = '<input type="checkbox"'
            . ' name="' . $name . '"' 
            . ' value="Y"';
      if ($checked) {
         $html .= ' checked';
      }
      $html .= '>' . '</input>';
     

      return $html;
    }

    public function getDropdown($name, $avalue, $selectedid = null) {
      # look through the assocated array and generate a select statement
      $html = '<select name="' . $name . '">' . PHP_EOL;
      foreach($avalue as $key => $value) {
        #add selected key word here  selected="selected"
        if ($key == $selectedid) {
          $html .= '<option selected="selected" value="' . $key .'">' . $value['display'] . '</option>' . PHP_EOL;
        } else {
          $html .= '<option value="' . $key .'">' . $value['display'] . '</option>' . PHP_EOL;
        }
      }
      $html .= '</select>' . PHP_EOL;
      return $html;
    }

    public function getDatePicker($name, $value = null) {
      $myCP = new tc_calendar($name, true, false);
      $myCP->setPath('/comp/calendar');
      if ($value == null) {
        $value = date();
      } else {
        $myCP->setText($value);
      }
      list($year, $month, $day) = explode('-', $value, 3);
      #$myCP->setDate($dtary[1],$dtary[2],$dtary[0]); # change this
      $myCP->setDate($day, $month, $year); # change this
      $myCP->setIcon('/comp/calendar/images/iconCalendar.gif');
      #$myCP->setYearInterval(2012, 2020);
      # should go from now into the future
      $myCP->setDateFormat('Y-m-d'); 
      #$myCP->dateAllow('2012-01-01', '2020-12-31');

      $myCP->setDate($day, $month, $year); # change this
      ob_start();
      $myCP->writeScript();
      $html = ob_get_contents();
      ob_end_clean();
    
      return $html;

    }
  }
?>
