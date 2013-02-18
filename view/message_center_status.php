<?php
  $model = $CONTROLLER->getModel();
  $to_list = getMapValue($model, 'to_list', array());
  $subject = getMapValue($model, 'subject', '');
  $message = getMapValue($model, 'message', '');
  $attachment = getMapValue($model, 'attachment', '-no attachment-');
?>
<H2>Message Center</H2>
<table border="1">
  <tr>
    <th>Subject</th>
    <td><?php echo $subject; ?></td>
  </tr>
<!--
  <tr>
    <th>Attachment</th>
    <td><?php echo $attachment; ?></td>
  </tr>
-->
  <tr>
    <th>Message</th>
    <td><?php echo $message; ?></td>
  </tr>
</table>
<p/>
The message was sent to the following addresses:<br/>
<ul>
  <?php
    foreach ($to_list as $to) {
      echo "<li>$to</li>" . PHP_EOL;
    }
  ?>
</ul>
