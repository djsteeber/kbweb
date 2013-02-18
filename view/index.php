<?php
  $model = $CONTROLLER->getModel();

  $announcements = getMapValue($model, 'announcement');
  $calendar = getMapValue($model, 'calendar');
  $events = getMapValue($model, 'events');
?>

<?php
  if (count($announcements) > 0) {
    echo '<div id="content-announcement">' . PHP_EOL;
    foreach ($announcements as $a) {
      echo "<b>$a->name</b><br/>" . PHP_EOL;
      echo $a->message . PHP_EOL;
    echo '<p/>' . PHP_EOL;
    }
    echo '</div>' . PHP_EOL;
    echo '<p/>' . PHP_EOL;
  }
?>
<div style="margin:15px;">
Kenosha Bowmen Inc. was founded "to foster, expand and perpetuate the practice of field archery and the spirit of good fellowship among all archers, to encourage the use of bow and arrow in hunting of all legal game birds, fish, and animals, and to cooperate with the Wisconsin Field Archery Association (WFAA) and the Wisconsin Bow Hunters Association (WBH) in securing the best hunting conditions and privileges for all bow hunters. To cooperate with the Wisconsin Conservation agencies for the propagation and conservation of all game, and to maintain field courses and an indoor range to conduct archery games and tournaments." 
</div>
<p/>
<div id="content-container">
  <div id="content-left">
    <h3><a href="/index.php/event">Upcoming Events</a></h3>
    <hr/>
     <!-- most recent 7 events -->
     <table>
        <?php
          foreach($events as $evt) {
            echo '<tr>';
            echo '<td style="padding-right:10px;">' . $evt->toDateString() . '</td>' . PHP_EOL;
            echo '<td>';
            if ($evt->event_info != null) {
              $html->link($evt->name, $evt->getEventInfoPath());
            } else {
              echo $evt->name;
            }
            echo '</td>' . PHP_EOL;
            echo '</tr>' . PHP_EOL;
          }
         ?>
     </table>
    <h3>Information</h3>
    <hr/>
    <ul style="list-style:none;padding:0;">
      <li><a href="/index.php/map">Address, Phone, Directions</a></li>
      <li><a href="mailto:webmaster@kenoshabowmen.com">Email the Webmaster</a></li>
    </ul>
  </div>

  <div id="content-right">
    <h3>Calendar</h3>
    <hr/>
    <table>
      <tr>
         <td style="vertical-align:top;">
          <?php
              $now = getdate();
              $mon = $now['mon'];
              $year = $now['year'];
              $calendar->showMonth($year, $mon);
           ?>
         </td>
         <td style="vertical-align:top;">
          <?php
              $mon = ($mon + 1);
              if ($mon > 12) {
                $year++;
                $mon = $mon % 12;
              }
              $calendar->showMonth($year, $mon);
           ?>
         </td>
      </tr>
      <tr>
         <td style="vertical-align:top;">
          <?php
              $mon = ($mon + 1);
              if ($mon > 12) {
                $year++;
                $mon = $mon % 12;
              }
              $calendar->showMonth($year, $mon);
           ?>
         </td>
         <td style="vertical-align:top;">
          <?php
              $mon = ($mon + 1);
              if ($mon > 12) {
                $year++;
                $mon = $mon % 12;
              }
              $calendar->showMonth($year, $mon);
           ?>
         </td>
      </tr>
    </table>
    <p/>
    Place your mouse pointer on a colored date to see the events happening.
  </div>
</div>
<div style="clear:both;">
<center>
<br/>
<a href="/index.php/membership">Membership Information</a>
</center>
</div>
<br/>
<br/>
