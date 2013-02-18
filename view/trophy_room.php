<?php 
  $model = $CONTROLLER->getModel();
  $trophyRoomInfo = getMapValue($model, 'trophy_room');
?>

<div>
<h2>Trophy Room</h2>


<p />
<ul style="margin:0;padding:0;">
<?php
  foreach($trophyRoomInfo as $item) {
    echo '<li class="items">';
    echo '<div class="trophyroom">';
     $sz = ' width="75" height="100" ';
     if ($item->orientation == 'L') {
       $sz = ' width="100" height="75" ';
     }
     $image = '/misc_docs/trophy_room/' . $item->image_file;
     echo '<a href="' . $image . '">';
     echo '<img class="trophyroom" src="' . $image  . '"' . $sz . '/>';
     echo '</a>' . PHP_EOL;
     echo '<font size="+1">';
     echo "$item->title";
     echo '</font>';
     echo '<br/><br/>';
     echo $item->description;
     echo '</div>';
  }
?>
</ul>
</div>
<div id="clr">
</div>
