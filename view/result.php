<h2>Results
</h2>
  <b>(Click on the underlined items for a copy of the results)</b>
<p />
<h3>Shoot Results</h3>
<p>
<ul>
  <?php
    $model = $CONTROLLER->getModel();
    foreach ($model['shoot_results'] as $result) {
      echo '<li>';
      $html->link($result->event->toLinkName() . ' - Results', $result->result_info->filepath);
      echo '</li>';
    }
  ?> 
</ul>
</p>
<h3>League Results</h3>
<p>
  <ul>
  <?php
    $model = $CONTROLLER->getModel();
    foreach ($model['league'] as $evt) {
      echo '<li>';
      $html->link($evt->toLinkName() . ' - Results', $evt->result_link);
      echo '</li>';
    }
  ?> 
  </ul>
</p>
