<?php
   $model = $CONTROLLER->getModel();
   $message = getMapValue($model, 'message');
   $origRequest = getMapValue($model, 'original-request'
                              ,'/index.php/index');
?>
<script type="text/javascript">
  function submitLoginBig() {
    document.loginForm.submit();
  }
  function submitForgotPassword() {
    document.fpForm.submit();
  }
  function keyEnterSubmitBig(e) {
    if (typeof e == 'undefined' && window.event) {
      e = window.event;
    }
    if (e.keyCode == 13) {
      submitLoginBig();
    }
  }
</script>
<h2>Member login</h2>
This area is restricted to members of the Kenosha Bowmen.<br/>
Please login with the username and password given to you at the Monthly meeting.
<p/>
If you cannot make it to the meeting please send an email to 
<a href="mailto:webmaster@kenoshabowmen.com">webmaster@kenoshabowmen.com</a> 
and the information can forwarded to you.
<p />
Once you have have logged-in, you will not need to re-login unless you close your web browser.
<p />
<div>
<form 
  name="loginForm"
  method="post"
  action="/index.php/index/processLogin">
  <table>
    <tr>
     <td>
      User Name:
     </td>
     <td>
  <input type="text" name="login" id="login" />
     </td>
    <tr>
     <td>
  <label for="password">Password:</label>
     </td>
     <td>
  <input type="password" name="password" id="password" 
        onkeypress="keyEnterSubmitBig(event);"/>
     </td>
    </tr>
  </table>
  <input type="hidden" name="original-request" id="original-request"
           value="<?php echo $origRequest; ?>" />
  <?php ButtonRenderer::render('Login', 'submitLoginBig'); ?>
</form>
</div>
<br/>
<div style="clear:both;">
If you have forgotten your password please fill out the information, and your password will be emailed to you.
<p/>
<form name="fpForm" 
      method="POST" 
      action="/index.php/index/processForgotPassword">
  <table>
    <tr>
      <td>Email Address: </td>
      <td>
        <input type="text" name="email" id="email" />
      </td>
      <td>(the email adddress on file)</td>
    </tr>
    <tr>
      <td>Zip Code: </td>
      <td>
        <input type="text" name="zip" id="zip" />
      </td>
      <td>(enter the home zipcode for your registration)</td>
    </tr>
    <tr>
      <td>Gate Code: </td>
      <td>
        <input type="text" name="gate_code" id="gate_code" />
      </td>
      <td>(enter the gate code)</td>
    </tr>
  </table>
  <?php ButtonRenderer::render('Get Password', 'submitForgotPassword'); ?>
</form>
</div>
<div style="clear;both;"></div>
<?php 
  if ($message != null) { 
    echo '<script type="text/javascript">' . PHP_EOL;
    echo "alert('$message');" . PHP_EOL;
    echo '</script>' . PHP_EOL;
  }
?>

