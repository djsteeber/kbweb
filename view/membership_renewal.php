<?php
   $model = $CONTROLLER->getModel();
   $mrModel = $model['mrModel'];
   $submitAction = $model['submit'];
   $user = $CONTROLLER->getUser();
   if ($user == null) {
     $user = new Person();
   }
?>
<h2>Kenosha Bowmen Membership Renewal Application</h2>

<u><i>Please update the information:</i></u>
<br/>
Once you hit register, a form will be emailed to you.  Please print and sign the form, and mail it in with your check to the address on the form.
<p/>
<form method="POST" action="<?php echo $submitAction; ?>">
<table style="background-color:#ccc;border:1px solid black;">
  <thead>
    <tr>
      <th colspan="8">Registration Form</th>
    </tr>
  </thead>
  <tbody>
     <tr>
      <td>First Name:</td>
      <td><input type="text" name="first_name" 
                 value="<?php echo $user->first_name;?>"/></td>
      <td>Middle:</td>
      <td><input type="text" name="middle_name" size="12"
                 value="<?php echo $user->middle_name;?>"/></td>
      <td>Last Name:</td>
      <td><input type="text" name="last_name"
                 value="<?php echo $user->last_name;?>"/></td>
      <td>Suffix:</td>
      <td><input type="text" name="suffix" size="6"
                 value="<?php echo $user->suffix;?>"/></td>
    </tr>
    <tr>
      <td>Spouse:</td>
      <td><input type="text" name="spouse"
                 value="<?php echo $user->spouse;?>"/></td>
    </tr>
    </tr>
    <tr>
      <td>Email:</td>
      <td colspan="2"><input type="text" name="email" size="30"
                 value="<?php echo $user->email;?>"/></td>
    </tr>
    <tr>
      <td>Address:</td>
      <td colspan="7"><input type="text" name="address" size="50"
                 value="<?php echo $user->address;?>"/></td>
    </tr>
    <tr>
      <td>City:</td>
      <td><input type="text" name="city"
                 value="<?php echo $user->city;?>"/></td>
      <td>State:</td>
      <td><input type="text" name="state" size="2" maxLength="2"
                 value="<?php echo $user->state;?>"/></td>
      <td>Zip:</td>
      <td><input type="text" name="zip" size="10"
                 value="<?php echo $user->zip;?>"/></td>
    </tr>
    <tr>
      <td>Phone:</td>
      <td colspan="2"><input type="text" name="phone"
                 value="<?php echo $user->phone;?>"/></td>
    </tr>
    <tr>
      <td>Additional Clubs:</td>
      <td colspan="8"><textarea name="additional_clubs"></textarea></td>
    </tr>
    
<?php
  for ($i=1; $i<=10; $i += 2) {
     echo '<tr>' . PHP_EOL;
     echo '<td>';
     echo "Archer $i :";
     echo '</td>' . PHP_EOL;
     echo '<td colspan="3">';
     echo '<input type="text" name="archer_'. $i . '" size="30"/>';
     echo '</td>' . PHP_EOL;
     echo '<td>';
     echo "Archer " . ($i+1) . ":";
     echo '</td>' . PHP_EOL;
     echo '<td colspan="3">';
     echo '<input type="text" name="archer_'. ($i+1) . '" size="30"/>';
     echo '</td>' . PHP_EOL;
     echo '</tr>' . PHP_EOL;
  }
?>
    <tr>
      <td>Sponsor:</td>
      <td colspan="3"><input type="text" size="50" name="phone"/></td>
      <td colspan="4"><i>(Current member of Kenosha Bowmen)</i></td>
    </tr>
  </tbody>
<table>
</table>
<p/>
By registering and signing the application you are agreeing to do a minimum of twenty (20) hours of work this coming membership year for the benefit of Kenosha Bowmen Hours this coming year must be completed by the last day of February. All memberships must have their hours completed by this date or their membership is terminated.  
<p/>
New members sometimes find it hard to get their work hours completed.  Listed below are some of the jobs you can sign up for.  Please check the ones you feel you would be interested in helping with.  Kenosha Bowmen also realized with new members, comes new talent.  If you feel there are other thing that you can do that are not listed above, please list them in the <i>Other</i> section below.
<p/>
<table style="background-color:#ccc;border:1px solid black;">
  <thead>
    <tr>
      <th colspan="8">Work Hours / Participation</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <input type="checkbox" name="help_registration" value="Y">
           Shoot Registration
        </input>
      </td>
      <td>
        <input type="checkbox" name="help_bar" value="Y">
           Tend Bar
        </input>
      </td>
      <td>
        <input type="checkbox" name="help_kitchen" value="Y">
           Kitchen
        </input>
      </td>
      <td>
        <input type="checkbox" name="help_mow" value="Y">
           Mow the Grass
        </input>
      </td>
    </tr>
    <tr>
      <td>
        <input type="checkbox" name="help_league" value="Y">
           Run a League
        </input>
      </td>
      <td>
        <input type="checkbox" name="help_target" value="Y">
           Cut and Paste Targets
        </input>
      </td>
      <td>
        <input type="checkbox" name="help_range" value="Y">
           Range Captains
        </input>
      </td>
      <td>
        <input type="checkbox" name="help_outdoor" value="Y">
           Other Outdoor Work
        </input>
      </td>
    </tr>
    <tr>
      <td>Other</td>
      <td colspan="2"><textarea style="width:400px;" name="help_other"></textarea></td>
      <td>Please list other ways to help the club</td>
  </tbody>
</table>
<p/>
Applicants must pay a membership fee of $60.00 and a Wisconsin Bow Hunter fee of $20.00. It is understood that all monies are to be forfeited if the applicant changes their mind. If for any reason the applicant is not accepted by the membership, their monies shall be refunded.
<p/>
The Kenosha Bowmen Club is a 101% Wisconsin Bow Hunters Club. If you wish to enroll additional family members in WBH, please list their names and include an additional $20.00 for each membership.
<p/>
You will receive a membership card and combinations to the grounds and buildings when all fees have been paid.
<p/>

<a class="cmdbutton" onclick="this.blur();"
   href="#"><span>Register</span></a>
</br>
</form>
