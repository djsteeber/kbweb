<?php
  require_once('code/dboforms.php');
#  require_once('code/htmlcomponents.php');

  # we could get the user from the controller, but 
  # this would let an admin, see the preference page
  # on users other than himself
  $model = $CONTROLLER->getModel();
  $message = getMapValue($model, 'message', null);
  $user = getMapValue($model, 'user');
  $dbofu = new DBOFormUtil($user);
  $alistComp = new AList();
  $cb = new CheckBoxComp();
  
?>
<script type="text/javascript">
   function submitSave() {
     document.preferenceForm.command.value="save";
     /* here we want to select all of the items in the lists
        so that they are passed, optionally, we could use a hidden field
        or a hidden list to have the selections already done
      */
     document.preferenceForm.submit();
   }
   function submitPasswordChange() {
     /* need to verify the new / confirm match
        must go back to the server to verify old password, 
        for security reasons */
     var pcurrent = document.passwordForm.password_current.value;
     var pnew = document.passwordForm.password_new.value;
     var pconfirm = document.passwordForm.password_confirm.value;

     if (pcurrent == '') {
       alert('Please enter your current password');
     } else if (pnew == '') {
       alert('Please enter a new password');
     } else if (pnew != pconfirm) {
       alert('New Password and Confirm Password do not Match');
     } else {
       document.passwordForm.submit();
     }
   }
   function resetForm() {
     document.preferenceForm.reset();
   }
</script>
<h2>User Preferences</h2>
<div style="width:930px;">
<div style="width:600px;float:left;"> <!-- main form -->
<form name="preferenceForm" 
      method="POST" action="/index.php/person/preferences">
  <table style="background-color:#ccc;">
    <thead>
    <tr>
      <th>Personal Info</th>
    </tr>
    </thead>
    <tr>
      <td>Name</td>
      <td span="5">
        <?php echo $user->full_name ?>
      </td>
    </tr>
    <tr>
      <td>Email</td>
      <td colspan="5">
        <?php echo $dbofu->getHtmlComponent($user, 'email'); ?>
      </td>
    </tr>
    <tr>
      <td>Address</td>
      <td colspan="5">
        <?php echo $dbofu->getHtmlComponent($user, 'address'); ?>
      </td>
    </tr>
    <tr>
      <td>City</td>
      <td >
        <?php echo $dbofu->getHtmlComponent($user, 'city'); ?>
      </td>
      <td>State</td>
      <td >
        <?php echo $dbofu->getHtmlComponent($user, 'state'); ?>
      </td>
      <td>Zip</td>
      <td >
        <?php echo $dbofu->getHtmlComponent($user, 'zip'); ?>
      </td>
    </tr>
    <tr>
      <td>Phone</td>
      <td colspan="5">
        <?php echo $dbofu->getHtmlComponent($user, 'phone'); ?>
      </td>
    </tr>
    <tr>
      <td>Cell</td>
      <td colspan="5">
        <?php echo $dbofu->getHtmlComponent($user, 'cell'); ?>
      </td>
    </tr>
    <tr>
      <td>Club Sponsor</td>
      <td colspan="5">
        <?php echo $user->membership->club_sponsor; ?>
      </td>
    </tr>
    <tr>
      <td>Spouse</td>
      <td colspan="5">
         <input type="text" name="spouse" size="30" 
                value="<?php echo $user->membership->spouse; ?>"/>
      </td>
    </tr>
   </table>
   <br/>
   <table style="background-color:#ccc">
    <thead>
      <tr>
        <th>Membership Info</th>
      </tr>
    </thead>
    <tr>
      <td valign="top">Additional Clubs</td>
      <td valign="top">
        <?php
          $alistComp->render('additional_clubs'
                            ,$user->membership->additional_clubs);
         ?>
    </tr>
    <tr>
      <td valign="top">Archers</td>
      <td valign="top">
        <?php
          $alistComp->render('archers'
                            ,$user->membership->archers);
         ?>
    </tr>
    <tr>
      <td valign="top">Skills</td>
      <td valign="top">
        <?php
          $alistComp->render('skills'
                            ,$user->membership->skills);
         ?>
    </tr>
    <tr>
     <td valign="top">Work Hours Help</td>
     <td>
      <table> 
        <tr>
          <td>
            <?php 
              $cb->render('help_registration', 'Help Registration'
                         ,$user->membership->help_registration);
            ?>
          </td>
          <td>
            <?php 
              $cb->render('help_bar', 'Tend Bar'
                         ,$user->membership->help_bar);
            ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php 
              $cb->render('help_kitchen', 'Kitchen'
                         ,$user->membership->help_kitchen);
            ?>
          </td>
          <td>
            <?php 
              $cb->render('help_mow', 'Mow the Grass'
                         ,$user->membership->help_mow);
            ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php 
              $cb->render('help_league', 'Run a League'
                         ,$user->membership->help_league);
            ?>
          </td>
          <td>
            <?php 
              $cb->render('help_target', 'Cut / Paste Targets'
                         ,$user->membership->help_target);
            ?>
          </td>
        </tr>
        <tr>
          <td>
            <?php 
              $cb->render('help_range', 'Range Captains'
                         ,$user->membership->help_range);
            ?>
          </td>
          <td>
            <?php 
              $cb->render('help_outdoor', 'Other Outdoor Work'
                         ,$user->membership->help_outdoor);
            ?>
          </td>
        </tr>
      </table>
     </td>
    </tr>
<!-- add
     additional clubs
     archer names
     sponsor - read only
     work hours / participation
-->
  </table>
  <input type="hidden" name="command" value="show"/>
  <p/>
  <table>
    <tr> 
    <td><?php echo $dbofu->createButton('Reset', 'resetForm') ?></td>
    <td><?php echo $dbofu->createButton('Save', 'submitSave') ?></td>
    </tr>
  </table>
</form>
</div>
<!-- password reset for in upper right corner -->
<div style="width:310px;border:1px solid gray;float:right;padding:5px;">
<form name="passwordForm" 
      method="POST" action="/index.php/person/preferences">
  <table>
    <thead>
      <tr><th>Reset Password</th></tr>
    </thead>
    <tr>
      <td>Current Password</td>
      <td><input type="password" name="password_current"/></td>
    </tr>
    <tr>
      <td>New Password</td>
      <td><input type="password" name="password_new"/></td>
    </tr>
    <tr>
      <td>Confirm Password</td>
      <td><input type="password" name="password_confirm"/></td>
    </tr>
  </table>
  <input type="hidden" name="command" value="change_password"/>
  <table>
    <tr> 
    <td><?php echo $dbofu->createButton('Change Password', 'submitPasswordChange') ?></td>
    </tr>
  </table>
</form>
</div>
</div>
<div style="clear:both;"/>
<?php
  if ($message != null) {
    echo '<script>' . PHP_EOL;
    echo "alert('$message');" . PHP_EOL;
    echo '</script>' . PHP_EOL;
  }
?>
