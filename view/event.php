<?php
    $model = $CONTROLLER->getModel();
?>
<h2>Kenosha Bowmen sponsored events</h2>
<p>
  <b>(Click on the underlined items for a copy of the brochure)</b>
</p>

<?php
  $sections = array('shoot' => 'Shoots &amp; Events'
                   ,'league' => 'Leagues'
                   ,'lesson' => 'Lessons'
                   ,'other' => 'Other Events');
  foreach ($sections as $section => $title) {
    $evts = getMapValue($model, $section);
    if (($evts != null) and (count($evts) > 0)) {
      echo "<h3>$title</h3>" . PHP_EOL;
      echo '<div style="margin-left:20;">' . PHP_EOL;
      echo '<table border="0">' . PHP_EOL;
      echo '  <thead>' . PHP_EOL;
      echo '    <tr>' . PHP_EOL;
      echo '      <th align="left"><u>Event</u></th>' . PHP_EOL;
      echo '      <th align="left"><u>Date</u></th>' . PHP_EOL;
      echo '      <th align="center"><u>Information</u></th>' . PHP_EOL;
      if ($section == 'lesson') {
        echo '      <th align="center"></th>' . PHP_EOL;
      } else {
        echo '      <th align="center"><u>Results</u></th>' . PHP_EOL;
      }
      echo '    </tr>' . PHP_EOL;
      echo '  </thead>' . PHP_EOL;
      echo '  <tbody>' . PHP_EOL;
      echo '  </tbody>' . PHP_EOL;
      foreach ($evts as $evt) {
        echo '    <tr>';
        echo '    <td width="300">' . $evt->name . '</td>' . PHP_EOL;
        echo '    <td width="150">' . $evt->toDateString() . '</td>' . PHP_EOL;
        echo '    <td width="100" align="center">';
        if ($evt->event_info != null) {
          //add in the extention of the file, or the file name
          $html->link('event flyer', $evt->getEventInfoPath());
        } else {
          echo '&nbsp;';
        }
        echo '</td>' . PHP_EOL;
        echo '    <td width="100" align="center">';
        if ($evt->result_info != null) {
          $html->link('results', $evt->getResultInfoPath());
        } else {
          echo '&nbsp;';
        }
        echo '</td>' . PHP_EOL;
        echo '    </tr>';
      }
      echo '  </tbody>' . PHP_EOL;
      echo '</table>' . PHP_EOL;
      echo '</div>' . PHP_EOL;
    }
  }
?>
