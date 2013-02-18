<?php
  require_once('code/dtmodel.php');

  class DataTable {
    private $YSCRIPT;

    private $name;
    private $dataModel = null;

    public function __construct($name, $colJSON, $) {
      $this->name = $name;
      $this->YTAG = '<div id="%s"></div>';
      $this->YSCRIPT =<<<EOS
<script>
  YUI().use("datatable-base", function(Y) {
    var cols = [ %s ];
    var data = [ %s ];
    var dt = new Y.DataTable.Base({
      columnset:cols
     ,recordset:data
     });
    dt.render("#%s");
  });
</script>
EOS;
    }
    public function setModel($dm) {
      $this->dataModel = $dm;
    }

    private function buildColString() {
      $ary = array_map("_DT_COL", $this->dataModel->getColumnNames()
                         , $this->dataModel->getColumnLabels());
      $str = join(',' . PHP_EOL, $ary);
      return $str;
    }

    private function buildDataString() {
       $ary = array();
       $cnames = $this->dataModel->getColumnNames();
       foreach ($this->dataModel->getData() as $row) {
         $d = array_map("_DT_DATA", $cnames, $row);
         $d = '{' . join(',', $d) . '}';
         array_push($ary, "{$d}");
       }

       return join(',' . PHP_EOL, $ary);
    }

    public function render() {
      $str = sprintf($this->YSCRIPT
                     ,$this->name
                     ,$this->buildColString()
                     ,$this->buildDataString()
                     ,$this->name);
      echo $str;
    }
  }
/*
  $d = new DataTable("dt");
  $dm = new DTMDefault();
  $dm->addColumn("name", "Name");
  $dm->addColumn("position", "Position");
  $dm->addDataRow(array("Dale", "Webm"));
  $dm->addDataRow(array("Steve", "Pres"));
  
  $d->setModel($dm);
  $d->render();
*/

?>
