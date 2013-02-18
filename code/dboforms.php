<?php
  require_once('code/webmvc.php');
  require_once('spaw2/spaw.inc.php');

  /**
   * This class,  file takes DBObjects and creates HTML forms from them.
   * this is really a static function, but it is packaged in a class.
   */

  class DBOFormField {
    public $title = null;
    public $name = null;
    public $html_field = null;
    public $hidden = false;
  }

  class DBOFormUtil {
    private $dbo = null;
    private $saveAction = null;
    private $cancelAction = null;
    private $formMethod = 'POST';

    public function __construct($dbo) {
      $this->dbo = $dbo;
    }

    public function setSaveAction($saveAction) {
      $this->saveAction = $saveAction;
    }

    public function getSaveAction($saveAction) {
      $action = '';
      if ($this->saveAction != null) {
        $action = $this->saveAction;
      }
      return $action;
    }

    public function setCancelAction($cancelAction) {
      $this->cancelAction = $cancelAction;
    }

    public function getCancelAction() {
      $action = '';
      if ($this->cancelAction != null) {
        $action = $this->cancelAction;
      }
      return $action;
    }

    public function getFormMethod() {
      return $this->formMethod;
    }

    public function setFormMethod($formMethod) {
      $this->formMethod = strtoupper($formMethod);
      if (! in_array(array('POST', 'GET', 'PUT', 'DELETE'), $ary)) {
        $this->formMethod = 'GET';
      }
    }

    public function getHtmlComponent(&$dbo, $fieldName) {
      $dboField = $dbo->getFieldByName($fieldName);
      $dboff = $this->getField($dbo, $dboField);
      return $dboff->html_field;
    }

    private function getField(&$dbo, &$dboField, $viewOnly = false) {
      // each field is going to be called based on the class name
      // if additional classes are created, then we need to create the 
      // subsequent functions here
      $dboff = new DBOFormField();
      $dboff->title = $dboField->display;
      $dboff->name = $dboField->name;
      $dboff->hidden = false;

      $method = 'get' . get_class($dboField->type) . 'HTML';
      $this->$method($dbo, $dboField, $dboff, $viewOnly);

      return $dboff; 
    }

    private function getFTBooleanHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $name = $field->name;
      $value = $dbo->$name;
      $checked = '';
      if (($value == 'Y') or ($value == true)) {
        $checked = ' checked';
      }
      $dboff->html_field = '<input type="checkbox" '
                                  . 'name="' . $name . '" '
                                  . 'value="Y"'
                                  . $checked . '></input>';
    }

    private function getFTDateHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $id = uniqid('dt_', true);
      $id = str_replace('.', '_', $id);
      $name = $field->name;
      $value = $dbo->$name;
      $templ = '<input type="text" name="%s" id="%s" value="%s"/>' . PHP_EOL
              . '<script> ' . PHP_EOL
              . '$(document).ready(function () {$("#%s").datepicker({changeMonth:true,changeYear:true});}); ' . PHP_EOL
              . '</script> ' . PHP_EOL;
      $dboff->html_field = sprintf($templ, $name, $id, $value, $id);
      #$this->getFTDefaultHTML($dbo, $field, $dboff, $viewOnly);
    }

    private function getFTDefaultHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $name = $field->name;
      $value = $dbo->$name;
      $dboff->html_field = '<input type="text" '
                                  . 'name="' . $name . '" '
                                  . 'value="' . $value . '"/>';
    }

    private function getFTIntHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $this->getFTDefaultHTML($dbo, $field, $dboff, $viewOnly);
    }

    private function getFTIDHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $name = $field->name;
      $value = $dbo->$name;

      $dboff->html_field = '<input type="hidden" '
                                  . 'name="' . $name . '" '
                                  . 'value="' . $value . '"/>';
      $dboff->hidden = true;
    }

    private function getFTStringHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $name = $field->name;
      $value = $dbo->$name;

      if (! $viewOnly) {
        //capture the output
        $size = $field->type->getLength();
        if ($size == null) {
          $spaw = new SpawEditor($name, $value);
          ob_start();
          $spaw->show();
          $dboff->html_field = '<div>' . ob_get_contents() . '</div>';
          ob_end_clean();
        } else {
          $maxlength = $size;
          if ($size > 40) { 
             $size = $size / 2;
          } else if ($size >= 20) {
             $size = 20;
          } 
            
          $dboff->html_field = '<input type="text" '
                                    . 'size="' . $size . '" '
                                    . 'maxlength="' . $maxlength . '" '
                                    . 'name="' . $name . '" '
                                    . 'value="' . $value . '"/>';
        }
      }
    }

    private function getFTSecretHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $name = $field->name;
      $value = $dbo->$name;

      $dboff->html_field = '';
      $dboff->hidden = true;
    }

    private function getFTDecimalHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $this->getFTDefaultHTML($dbo, $field, $dboff, $viewOnly);
    }

    private function getFTReferenceHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $dboff->html_field = 'reference field';
      $name = $field->name;
      $value = $dbo->$name;

      $cn = $field->type->getClassName();
      $ref_obj = new $cn();
      $odb = $this->dbo->getODB();
      //$odb = new ODB();
      $objs = $odb->fetchAll($cn);
      $html = '<select name="' . $name . '">' . PHP_EOL;
      if ($field->type->isNullable()) {
        $html .= '<option value="null"></option>' . PHP_EOL;
      }
      foreach ($objs as $obj) {
        $html .= '<option value="' . $obj->id . '"';
        if (($value != null) and ($obj->id == $value->id)) {
          $html .= ' selected="selected"';
        }
        $html .= '>' . $obj->name
                   . '</option>' . PHP_EOL;
      }
      $html .= '</select>';
      $dboff->html_field = $html;
    }

    private function getDirList($dir) {
      $rtn = array();
      if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
          if (($entry != '.') and ($entry != '..')
              and (is_file($dir . '/' . $entry))) {
            array_push($rtn, $entry);
          }
        }
        closedir($handle);
      }
      sort($rtn);
      return $rtn;
    }
    private function getFTFileHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $name = $field->name;
      $value = $dbo->$name;
      if (! $viewOnly) {
        $file_list = $this->getDirList($_SERVER['DOCUMENT_ROOT'] 
                                          . $field->type->getPath());
        $html = '<select name="' . $name . '">' . PHP_EOL;
        $html .= '<option value="  "></option>' . PHP_EOL;
        foreach($file_list as $f) {
          $html .= '<option value="' . $f . '"';
          if ($f == $value) {
            $html .= ' selected="selected"';
          }

          $html .= '>' . $f . '</option>' . PHP_EOL;
        }
        $html .= '</select>' . PHP_EOL;
        $dboff->html_field = $html;
      }
    }

    /* ideally I would like this to be a list box that can be added to 
       but for now, we will just make it an input box */
    private function getFTStringListHTML(&$dbo, &$field, &$dboff, $viewOnly) {
      $name = $field->name;
      $value = $dbo->$name;
      if (! $viewOnly) {
        $dboff->html_field = '<input type="text" '
                                  . 'size="50" '
                                  . 'name="' . $name . '" '
                                  . 'value="' . $value . '"/>';
      }
    }


    private function getFields($viewOnly = false) {
      $ary = array();
      foreach ($this->dbo->getFields() as $dboField) {
        $dboff = $this->getField($this->dbo, $dboField, $viewOnly);
        array_push($ary, $dboff);
      }
      return $ary;
    }

    public function showForm() {
      $this->showTableForm();
    }

    private function indent($cnt) {
       for ($i = 0; $i < $cnt; $i++) {
         echo '  ';
       }
    }

    private function showJavaScript() {
/*
Note:  Validation for required fields in JavaScirpt

in previous code, add in a validator
var myformValidator =  new Validatopr("<formid>");
myformValidator.addValidation("<fieldid>", "req", "<alert message>");
if (document.<formid>.onsubmit()) {
}
for delete use 
if (confirm("msg") == true) {
}
 */
      $js = <<<EOS
<script type="text/javascript">
  function submitSave() {
      document.editform.maintaction.value = "save";
      document.editform.submit();
  }
  function submitDelete() { 
    if (confirm("Click OK to delete the record.") == true) {
      document.editform.maintaction.value = "delete";
      document.editform.submit();
    }
  }

  function resetForm() {
     document.editform.reset();
  }

  function submitCancel() {
  }

</script>
EOS;
      echo $js . PHP_EOL;

    }

    public function createButton($title, $jsFunction) {
      $html = '<a class="cmdbutton" href="javascript: ' . $jsFunction . '()"'
           . 'onclick="this.blur();"><span>' . $title . '</span></a>';
      return $html;
    }

    public function createCancelButton() {
      $html = '<a class="cmdbutton" href="#"'
        . ' onClick="parent.location=\'' . $this->getCancelAction() . '\'">'
        . '<span>Cancel</span></a>';
      return $html;
    }

    private function showTableForm() {
      $fields = $this->getFields();
      $method = $this->getFormMethod();
      echo PHP_EOL;
      echo '<form name="editform" enctype="multipart/form-data"'
                 . ' method="' . $method . '"'
                 . ' action="' . $this->getSaveAction(null) . '">' . PHP_EOL;
      echo '<table>' . PHP_EOL;
      echo '<tbody>' . PHP_EOL;
     
      foreach($fields as $field) {
        if (! $field->hidden) { 
          echo '<tr>' . PHP_EOL;
          echo '<td>' . $field->title . '</td>' . PHP_EOL;
          echo '<td>' . $field->html_field . '</td>' . PHP_EOL;
          echo '</tr>' . PHP_EOL;
        }
      }
      echo $this->indent(1);
      echo '</tbody>' . PHP_EOL;
      echo '</table>' . PHP_EOL;
      echo '<table>' . PHP_EOL;
// button bar
      echo '<tr>';
      echo '<td>'; 
      if (($this->dbo != null) and ($this->dbo->id != null)
          and ($this->dbo->id > 0)) {
        echo '<td>'; 
        echo $this->createButton('Delete', 'submitDelete');
        echo '</td>' . PHP_EOL;
      }
      echo '<td>';
      echo $this->createCancelButton();
      echo '</td>' . PHP_EOL;
      echo '<td>';
      echo $this->createButton('Reset', 'resetForm');
      echo '</td>' . PHP_EOL;
      echo '<td>';
      echo $this->createButton('Save', 'submitSave');
      echo '</td>' . PHP_EOL;
      echo '</tr>';
      echo '</table>' . PHP_EOL;
      foreach($fields as $field) {
        if ($field->hidden) { 
          echo $this->indent(1);
          echo $field->html_field . PHP_EOL;
        }
      }
      echo '<input type="hidden" name="maintaction" value="save"/>' . PHP_EOL;
      echo '</form>' . PHP_EOL;
      $this->showJavaScript();
    }

    private function filterHiddenFieldsOut($f) {
      return (! $f->hidden);
    }
    public function showDataTable($url_root) {
      $url_root = trim($url_root, '/');
      $fields = $this->getFields(true);
      $odb = $this->dbo->getODB();
      $objects = $odb->fetchAll(get_class($this->dbo));
  
      echo '<table id="dbofu-table" style="display:none">' . PHP_EOL;
      echo '<thead>' . PHP_EOL;
      echo '<tr>' . PHP_EOL;
# remove the id off of the fields list
      #array_shift($fields);
      $fields = array_filter($fields, array($this, 'filterHiddenFieldsOut'));
      foreach($fields as $field) {
        echo '<th>' . $field->title . '</th>' . PHP_EOL;
      }
      echo '</thead>' . PHP_EOL;
      echo '<tbody>' . PHP_EOL;
      foreach($objects as $object) { 
        echo '<tr>' . PHP_EOL;
        $first = true;
        foreach($fields as $field) {
          $fn = $field->name;
          $value = $object->$fn;
          if ($value instanceof DBObject) {
            $value = $value->name;
          }
          if (is_bool($value)) {
            if ($value) {
              $value = 'Yes';
            } else {
              $value = 'No';
            }
          }
          if ($first) {
            
            $value = '<a href="/' . $url_root . '/edit?id=' 
                    . $object->id . '">' 
                    . $value . '</a>';
          }
          echo '<td>' . $value . '</td>' . PHP_EOL;
          $first = false;
        }
        echo '</tr>' . PHP_EOL;
      }
      echo '</tbody>' . PHP_EOL;
      echo '</table>' . PHP_EOL;
    }
  }
?>
