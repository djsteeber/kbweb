<?php
  require_once('code/dboforms.php');
#  require_once('code/htmlcomponents.php');
  $dbofu = new DBOFormUtil(null);
  $model = $CONTROLLER->getModel();
  $people = getMapValue($model, 'people');
  $message = getMapValue($model, 'message');

  # we could get the user from the controller, but 
?>
<script type="text/javascript">
   function submitSave() {
     document.prform.submit();
   }
</script>

<h2>Administration - User Password Reset</h2>

<form name="prform" method="POST" action="/index.php/person/arup">
  Select User:
  <select name="person_id">
    <option value="null"></option>
    <?php 
      foreach ($people as $person) {
        echo '<option value="' . $person->id . '">' 
             . "$person->last_name, $person->first_name"
             . '</option>.' . PHP_EOL;
      }
     ?>
  </select> 
  <br/>
  <br/>
  New Password:  <input name="password" type="password"></input>
  <input name="action" type="hidden" value="reset"/>
  <br/>
  <br/>
  <?php echo $dbofu->createButton('Reset Password', 'submitSave') ?>
  <br/>
  <br/>
  <?php if ($message != null) echo "$message"; ?>

</form>
