<h2>Photo Gallery</h2>
<h3>Family Archery<h3>
<?php
  $handle = opendir('photos/gallery/familyarchery');

  # move this to the controller
  while (false != ($file = readdir($handle))) {
    if (($file != '..') and ($file != '.'))  {
      echo '<img src="/photos/gallery/familyarchery/' . $file . '" />  ';
    }
  }
  closedir($handle);
?>
