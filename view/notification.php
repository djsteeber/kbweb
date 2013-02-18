<?php
  require_once('code/dboforms.php');
  $dbofu = new DBOFormUtil(null);
?>

<h3>Notifications</h3>
To receive an email notification on events, please add your email address to our contact list. (Change wording HERE)

<p/>
<script type="text/javascript">
  function submitForm() {
    document.subscriptionForm.action = "/index.php/notification/subscribe";
    document.subscriptionForm.submit();
  }
</script>

<form method="POST" name="subscriptionForm">
<table>
  <tr>
    <td>First Name</td>
    <td>Last Name</td>
  </tr>
  <tr>
    <td><input type="text" name="first_name"/></td>
    <td><input type="text" name="last_name"/></td>
  </tr>
  <tr>
    <td colspan="2">Email Address</td>
  </tr>
  <tr>
    <td colspan="2"><input type="text" name="email" size="30"/></td>
  </tr>
  <tr>
   <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
   <td colspan="2">Subscribe To:</td>
  </tr>
  <tr>
    <td>Upcoming Events</td>
    <td><input type="checkbox" name="notification[]" value="event"/></td>
  </tr>
  <tr>
    <td>Results</td>
    <td><input type="checkbox" name="notification[]" value="result"/></td>
  </tr>
  <tr>
    <td>Announcements</td>
    <td><input type="checkbox" name="notification[]" value="announcement"/></td>
  </tr>
</table>
<br/>
<?php echo $dbofu->createButton('Subscribe', 'submitForm'); ?>
<br/>
</form>


