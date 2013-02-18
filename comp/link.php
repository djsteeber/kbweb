<?php
  class Link {
    private $controller;
    private $name;
    private $attrs;

    public function __construct($name, $config) {
      $this->name = $name;
      $this->attrs = $config;
    }

    public function render() {
      $ary = $this->attrs;
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
      } else {
        $ref = $ary;
      }
      # need to add in the array stuff here
      $link = '<a href="' . $ref . '">' . $this->name . '</a>';
      echo $link;
    }
  }
?>
