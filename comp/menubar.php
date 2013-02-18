<?php
   class MenuBar {
     private $items = array();
     private $name = 'menu';
     private $SCRIPT = null;

     public function __construct($name) {
       $this->name = $name;

       $this->SCRIPT = <<<EOS
         <script>
          YUI().use("node-menunav", function(Y) {

            Y.on("contentready", function () {
               this.plug(Y.Plugin.NodeMenuNav);

            }, "#%s");

         });
       </script>
EOS;
     }

     public function addMenuItem($name, $link, $sub=null, $roles=null) {
        array_push($this->items, array('name' => $name
                                      ,'link' => $link
                                      ,'sub' => $sub
                                      ,'roles' => $roles));
     }
     // now now only 2 levels of menus
     public function render($roles = null) {
       $this->renderMenu(false, $roles);
       $this->renderScript();
     }

     public function renderScript() {
       echo sprintf($this->SCRIPT, $this->name);

     }

     private function renderMenu($submenu = false, $roles = null) {
       $this->renderHead($submenu);
       foreach($this->items as $item) {
           $this->renderItem($item, $roles);
       }
       $this->renderFoot($submenu);
     }

     private function renderHead($submenu = false) {
       if ($submenu) {
       echo '<div id="' .$this->name . '" class="yui3-menu">' . PHP_EOL;
       } else {
       echo '<div id="' .$this->name . '" class="yui3-menu yui3-menu-horizontal">' . PHP_EOL;
       }
       echo '<div class="yui3-menu-content">' . PHP_EOL;
       echo '<ul>' . PHP_EOL;
     }

     private function renderFoot($submenu = false) {
       echo '</ul>' . PHP_EOL;
       echo '</div>' . PHP_EOL;
       echo '</div>' . PHP_EOL;
     }

     private function checkRole($menuRoles, $userRoles) {
       $urs = split(';', $userRoles);
       foreach ($urs as $role) {
         if (in_array($role, $menuRoles)) {
           return true;
         }
       }
       return false;
     }

     private function renderItem($item, $roles) {
       # check the roles here before rendering
       if ($item['roles'] != null) {
         if (! $this->checkRole($item['roles'], $roles)) {
            return;
         }
       }
       if ($item['sub'] != null) {
         echo '<li>' . PHP_EOL;
         echo '<a class="yui3-menu-label" href="' . $item['link'] . '">' 
            . $item['name'] . '</a>' . PHP_EOL;
         $item['sub']->renderMenu(true, $roles);
         echo '</li>' . PHP_EOL;
       } else {
         echo '<li class="yui3-menuitem">' . PHP_EOL;
         echo '<a class="yui3-menuitem-content" href="' . $item['link'] . '">' 
            . $item['name'] . '</a>' . PHP_EOL;
         echo '</li>' . PHP_EOL;
       }
     }
   }

?>
