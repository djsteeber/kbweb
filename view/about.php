<h2>About US</h2>
<h3>From our Constitution</h3>
<p>
  Kenosha Bowmen Inc. was founded "to foster, expand and perpetuate 
  the practice of field archery and the spirit of good fellowship 
  among all archers, to encourage the use of bow and arrow in hunting 
  of all legal game birds, fish, and animals, and to cooperate 
  with the Wisconsin Field Archery Association (WFAA) and 
  the Wisconsin Bow Hunters Association (WBH) in securing 
  the best hunting conditions and privileges for all bow hunters.
  To cooperate with the Wisconsin Conservation agencies for the propagation
  and conservation of all game, and to maintain field courses and 
  an indoor range to conduct archery games and tournaments."
</p>
<h3>Club Officers / Range Captains / Border Members</h3>
<p>
<table width="700px">
<tbody>
  <?php
       $i = 0;
       foreach($CONTROLLER->getModel() as $o) {
         if (($i % 2) === 0) {
           echo '<tr>' . PHP_EOL;
         }
         echo '<td>' . $o->getName() . '</td>';
         echo '<td>' . $o->getPosition() . '</td>' . PHP_EOL;
         if (($i % 2) === 1) {
           echo '</tr>' . PHP_EOL;
         }
         $i++;
       }
       if (($i % 2) == 0) {
         echo '<td></td></tr>';
       }
  ?>
</tbody>
</table>
</p>
<h3>Wisonsin Bowhunters Association</h3>
<p>
  All our members are constitutionally required to be a member of the WBH. 
  Together with our membership as a club this qualifies Kenosha Bowmen 
  for 101% membership status in the organization.
</p>
<h3>Range Maps & Target Locations</h3>
<p>
  (Requires Adobe Reader)<br />
  (Selections on the left were created with the help of a Garmin GPS 
   and Google Earth.)
  <ul class="maplinks">
    <li>
      <a href="/misc_docs/maps/kbfacilitiesmap.pdf" target="_blank">Kenosha Bowmen Facilities Map</a>
    </li>
    <li>
      <a href="/misc_docs/maps/range1.pdf" target="_blank">Map of Range 1</a>
    </li>
    <li>
      <a href="/misc_docs/maps/range2.pdf" target="_blank">Map of Range 2</a>
    </li>
    <li>
      <a href="/misc_docs/maps/range3.pdf" target="_blank">Map of Range 3</a>
    </li>
    <li>
      <a href="/misc_docs/maps/range4.pdf" target="_blank">Map of Range 4</a>
    </li>
    <li>
      <a href="/misc_docs/maps/kbrangemaps.pdf" target="_blank">NFAA Range Maps</a>
    </li>
    <li>
      <a href="/misc_docs/maps/nfaatargetloc.pdf" target="_blank">NFAA Range Target Distances and Associated Targets</a>
    </li>
  </ul>
</p>
<!--
<h3><a href="officers.php">Officers and Committee Members</a></h3>
-->
