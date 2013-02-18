<h2>Message Center</h2>
<p>
The message center is a way for members to send messages via email to all other members.
</p>
<p>
<?php
  $model = $CONTROLLER->getModel();
  $to_list = getMapValue($model, 'to_list');
?>
<script type="text/javascript">
   function sendMessage() { 
      var subject = document.msg_form.form_subject.value;
      if (subject == '') {
        alert('Please enter a Subject for your message');
        return;
      }

      answer = confirm('Sending the message may take awhile to send.  Please do not close your browser until the message is complete. Click OK to send.');
      if (answer == true) {
        document.msg_form.submit();
      }
   }
</script>
</p>
<form enctype="multipart/form-data"
      action="/index.php/message_center/send" 
      method="POST"
      name="msg_form">
<table>
  <tbody>
  <tr>
     <td>To:</td>
     <!-- 9 px per char -->
     <td>
       <select name="form_to">
       <?php
          foreach ($to_list as $key => $name) {
            echo '<option value="' . $key . '">' . $name . '</option>';
          }
       ?>
       </select>
     </td>
  </tr>
  <tr>
<!--
    <td>Attachment:</td>
    <td>
      <input name="attachment" type="file"/>(only 1 attachment max)
      <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
    </td>
-->
  </tr>
  <tr>
     <td>Subject:</td>
     <td><input name="form_subject" type="text" style="width:400px;"/></td>
   </tr>
  <tr>
     <td>Message:</td>
     <td>
<div>
<?php
   include("spaw2/spaw.inc.php");
   $content= '';
   $spaw = new SpawEditor("form_message", $content);
   $spaw->show();
?>
</div>
     </td>
<!--
<textarea name="form:message" style="width:400px;height:200px"></textarea></td>
-->
  </tr>
  </tbody>
</table>
<br/>
<a class="cmdbutton"
   href="javascript:sendMessage();"><span>Send Message</span></a>
<br/>
<br/>
<!-- <input type="submit" name="submit" value="Send Message"/>-->
</form>
