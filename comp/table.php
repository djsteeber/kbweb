<?php

  class HTMLTable {
    private $rows = array();

    public function addRow($row) {
      array_push($this->rows, $row);
    }

    public function showRow($row) {
      echo '<tr>' . PHP_EOL;
      foreach ($row as $item) {
         echo '<td>' . PHP_EOL;
         echo $item . PHP_EOL;
         echo '</td>' . PHP_EOL;
      }
      echo '</tr>' . PHP_EOL;
    }

    public function showTable() {
      echo '<table>' . PHP_EOL;
      foreach ($this->rows as $row) {
        $this->showRow($row);
      }
      echo '</table>' . PHP_EOL;
    }
  }

  # test this
  $row = array();
  array_push($row, 'Dale');
  array_push($row, 'Steeber');
  $x = new HTMLTable();
  $x->addRow($row);
  $x->showTable();

?>
