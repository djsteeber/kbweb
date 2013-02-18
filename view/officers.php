
<?php
  require_once('/var/www/code/person.php');

 ?>
<h2>Officers<h2>
<p>
  <table>
   <?php 
     foreach(getOfficers() as $officer) { 
   ?>   
   <tr>
   <td> &nbsp; </td>
   <td>
      <?php echo $officer->toEmailLink(); ?> 
   </td>
   <td>
    -
   </td>
   <td>
      <?php echo $officer->title; ?>
   </td>
   </tr>
   <?php 
     }
   ?>

  </table>
</p>


<h2>Committees<h2>
<p>
  <table>
   <?php 
     foreach(getCommitteeMembers() as $member) { 
   ?>   
   <tr>
   <td> &nbsp; </td>
   <td>
      <?php echo $member->toEmailLink(); ?> 
   </td>
   <td>
    -
   </td>
   <td>
      <?php echo $member->title; ?>
   </td>
   </tr>
   <?php 
     }
   ?>

  </table>
</p>
