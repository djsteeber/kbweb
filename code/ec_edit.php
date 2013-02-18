<?php
  require_once('code/dboforms.php');

  $model = $CONTROLLER->getModel();
  $title = getMapValue($model, 'title', "TITLE");
  $root_url = getMapValue($model, 'url-root');
  
  echo "<H2>$title</H2>";

  $dbofu = new DBOFormUtil($model['object']);
  $dbofu->setSaveAction($root_url . '/save');
  $dbofu->setCancelAction($root_url);
  $dbofu->showForm();
?>

