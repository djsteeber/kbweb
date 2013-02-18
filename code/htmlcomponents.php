<?php

  class HTMLComponent {
  }

  class ButtonRenderer {
    public static function render($title, $jsFunction) {
      echo $html = '<a class="cmdbutton" href="javascript: ' 
           . $jsFunction . '()"'
           . 'onclick="this.blur();"><span>' . $title . '</span></a>';
    }
  }


  class CheckBoxComp extends HTMLComponent {
    public function render($name, $title, $value = false) {
      $id = uniqid('cb_',true);
      echo "<input type=\"checkbox\""
           . " onchange=\"javascript: setCBTFValue(this, '$id');\"";
      if ($value) {
        echo " checked=\"true\"";
      }
      echo "/> " . $title;
      echo "<input type=\"hidden\" id=\"$id\"";
      echo " name=\"$name\"";
      if ($value) {
        echo " value=\"1\"";
      } else {
        echo " value=\"0\"";
      }
      echo "/>";
    }

  }


  class AList extends HTMLComponent {

    public function render($name, $value = null) {
       $values = $value;
       if ($values != null) {
         if (! is_array($values)) {
           $values = split(';', $values);
         }
       } else {
         $values = array();
       }

       $listBoxSize = count($values);
       if ($listBoxSize < 2) {
         $listBoxSize = 2;
       }
       $id = uniqid('',true);
       $inputId = 'alist_input_' . $id;
       $listId = 'alist_list_' . $id;
       $hiddenFieldId = $listId . '_value';
       $msg = '(enter the name and press <b>enter</b> key'
            . ', <b>double click</b> the name in the list to remove it'
            .')';
       echo '<!-- AList -->' . PHP_EOL;
       echo '<div>' . PHP_EOL;

       // left div
       echo '<div style="float:left;margin-right:10px;width:220px;">' 
            . PHP_EOL; 
       echo '<input type="text" size="30" id="' . $inputId .'" '
            . 'onkeypress="if (isEnterPressed(event)) '
            . "addOption('$listId', this);\""
            . '><br/>' 
            . PHP_EOL;
       echo "<input type=\"hidden\" name=\"$name\" value=\"$value\""
             . " id=\"$hiddenFieldId\"/>" . PHP_EOL;

       // message
       echo '<span style="font-size:8pt;font-style:italic">' . PHP_EOL;
       echo $msg . PHP_EOL;
       echo '</span>' . PHP_EOL;
       echo '</div>' . PHP_EOL;

       // right div
       echo '<div style="float:left;margin-right:10px;">' . PHP_EOL;
       echo '<select style="min-width:200px;" ';
       echo ' id="' . $listId . '"';
       echo ' size="' . $listBoxSize . '" ';
       echo ' ondblclick="removeOption(this);"';
       echo '>' . PHP_EOL;
       foreach($values as $v) {
         echo '<option name="' . $v . '">' . $v . '</option>' . PHP_EOL;
       }
       echo '</select>' . PHP_EOL;
       echo '</div>' . PHP_EOL;

       echo '</div>' . PHP_EOL;

       echo '<!-- End AList -->' . PHP_EOL;
    }
  }
?>
