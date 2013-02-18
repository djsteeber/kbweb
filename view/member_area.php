<?php
  $model = $CONTROLLER->getModel();
  $workParties = getMapValue($model, 'work_party');
  $user = $CONTROLLER->getUser();
  $generic_user = false;
  if ($user != null) {
   $generic_user = preg_match('/generic_member/i', $user->role);
  }
?>
<?php if ($generic_user) { ?>
<h3><font color="red">Generic Login</font></h3>
You have logged in to the website using the Generic Login kenoshabowmen.  
You can login to this website using your email address.  
By doing this, you will be able to access additional information.  
<br/><br/>
<a href="/index.php/index/showLogin">Click Here</a> to login using your email account
<hr />
<?php } else if ($user->membership != null) { ?>
<h3>Work Hours</h3>
<?php 
   if ($user->membership->work_hours != null) {
    echo "You have completed " . $user->membership->work_hours . " work hours.";
   } else {
    echo "You do not have any work hours recorded yet.";
   }
?>
<?php } ?>
<h2>Members Area</h2>
<div id="content-container">
<div id="content-left">
<h3>Upcoming Work Parties</h3>
Here's our remaining work party schedule:  (Work starts at 8:00am and goes until approx. noon.)
<?php
  if (($workParties == null) or (count($workParties) == 0)) {
    echo '<p>No Work Parties Scheduled at this time</p>' . PHP_EOL;
  } else {
     echo '<ul>' . PHP_EOL;
     foreach($workParties as $wp) {
       echo '<li>';
       echo "<b> " . $wp->toDateString()
             . " -</b> $wp->name. $wp->description";
       echo '</li>' . PHP_EOL;
     }
     echo '</ul>' . PHP_EOL;
  }
?>
</div>
<div id="content-right">
  <?php 
     $docListArray = array('Membership' => 'membership'
                        ,'Meetings' => 'meeting'
                        ,'Newsletters' => 'newsletter');
     foreach ($docListArray as $dlKey => $dlValue) {
       echo "<h3>$dlKey</h3>" . PHP_EOL;
       if (isset($model[$dlValue])) {
         echo '<ul>' . PHP_EOL;
         $docs = $model[$dlValue];
         foreach ($docs as $doc) {
           echo '<li>';
           $html->link($doc['name']
                 ,array('controller' => 'member_area'
                        ,'function' => 'securefile'
                        ,'data' => array( 'filename' => $doc['doc-path'])));
           #echo '<small> - (updated: ' . $doc->last_updated . ')</small>';
            echo '<small> - (updated: ' 
                 . date('n/j/Y', $doc['stats']['mtime']) 
                 . ')</small>';
           echo '</li>';
         }
         echo '</ul>' . PHP_EOL;
       }
     }
  ?>
<!--
  <li><a href="/index.php/membership_renewal">Online Membership Renewal</a></li>
-->
</div>
</div>
<div style="clear:both;">
</div>
