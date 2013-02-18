<html>
  <link rel="SHORTCUT ICON" href="/favicon.ico">
  </link>
<?php
  require_once('code/htmlcomponents.php');
  require_once('comp/menubar.php');
  $user = $CONTROLLER->getUser();
  $loggedin = ($user != null);
  $userRoles = null;
  $genUser = false;
  if ($user != null) {
    $userRoles = $user->role;
    $genUser = $user->hasRole('generic_member');
  }
?>
<head>

<!-- jquery datepicker       -->

   <link rel="stylesheet"
   href="/lib/jquery-ui/development-bundle/themes/overcast/jquery.ui.all.css">
   </link>
   <script src="/lib/jquery-ui/development-bundle/jquery-1.7.1.js"></script>
   <script src="/lib/jquery-ui/development-bundle/ui/jquery.ui.core.js">
   </script>
   <script src="/lib/jquery-ui/development-bundle/ui/jquery.ui.widget.js">
   </script>
   <script src="/lib/jquery-ui/development-bundle/ui/jquery.ui.datepicker.js">
   </script>
<!--        -->

  <title>Kenosha Bowmen</title>
  <script type="text/javascript"
          src="/lib/yui3.4/build/yui/yui.js"></script>
  <script type="text/javascript"
          src="/code/htmlcomponents.js"></script>
  <link rel="stylesheet"
        href="/css/kbstyle3.css"/>
</head>
<body class="yui3-skin-sam">
 <div id="container">
  <div style="background-color: #F0F0F0;">
  <table width="100%" cellpadding="0">
    <tr>
      <td width="10%">
       <!--<img src="images/logo.jpg" width="72" height="84"></img> -->
       <img src="/images/logo_60pct.png"
            style="vertical-align:middle"/>
      </td>
      <td align="left" valign="center">
        <b>
        <span style="font-size:22pt;color:#BFB467;">
         Kenosha Bowmen
        </span>
        <br/>
        <span style="font-size:10pt;color:#BFB467;">
          15211 75<sup>th</sup> Street, Bristol, WI  53104<br/>
          (262) 857-9908
        </span>
        </b>
      </td>
      <td width="45%" align="right" valign="bottom">
        <table width="100%" height="120" cellpadding="0">
           <tr>
            <td valign="top" align="right" width="100%">
              <ul class="horizontal-rlist"> 
<!--
                <li>
              <a href="/index.php/notification"
                 title="Click here to signup for email on events"
                 class="email-alerts"></a>
              </li>
-->
              <li>
              <a href="http://www.facebook.com/kenosha.bowmen"
                 title="Follow us on Facebook"
                 target="_blank" class="facebook"></a>
              </li>
             </ul>
            </td>
           </tr>
           <tr>
             <td valign="bottom" align="right">
        <?php if (! $loggedin) { ?>
        <!-- add a php section here to test if user is logged in -->
        <form name="loginform" method="POST" style="margin:0; padding:0;"
              action="/index.php/index/processLogin">
          <span style="color:#545454;font-size:10pt;">Member Login:
          <input type="text" size="14" name="login" placeholder="Login"
                 />
          <input type="password" size="14" name="password" 
                 placeholder="Password"
                 onkeypress="keyEnterSubmit(event);"></input>
          <a href="javascript: submitLogin()">
             <img src="/images/roundGreyArrow.gif"></img>
          </a><br/>
          <?php
            if (isIEBrowser()) {
              echo 'Enter user name and password above  ';
            }
          ?>
          <a href="/index.php/person/login">Forgot Password</a>
          </span>
        </form>
        <script type="text/javascript">
          function submitLogin() {
            /* should check if user name and password are filled in */
            document.loginform.submit();
          }
          function keyEnterSubmit(e) {
            if (typeof e == 'undefined' && window.event) {
               e = window.event;
            }
           if (e.keyCode == 13) {
              submitLogin();
           }
          }
        </script>
        <?php } else { ?>
          Welcome <?php echo $user->first_name; ?> 
          <a href="/index.php/index/logout">(sign-off)</a>
     <?php if (! $genUser) { ?>
          <a href="/index.php/person/preferences">(profile)</a>
     <?php } ?>
        <?php } ?>
             </td>
         </tr>
       </table>
      </td>
    </tr>
  </table>
  </div>
  <div id="menusection">
    <?php

       $menuBar = new MenuBar('kbmenu');

       $menuBar->addMenuItem("Home", '/index.php/index');

       $subMB = new MenuBar('cism');
       $subMB->addMenuItem("About US", "/index.php/about");
       $subMB->addMenuItem("Directions", "/index.php/map");
       $subMB->addMenuItem("<hr/>", "#");
       $subMB->addMenuItem("Photo Gallery", "/index.php/photos");
       $subMB->addMenuItem("Trophy Room", "/index.php/trophy_room");
       $subMB->addMenuItem("Super Hunt", "/index.php/hunt");

       $menuBar->addMenuItem("Club Information", '#', $subMB);

       $menuBar->addMenuItem("Events &amp; Results", "/index.php/event");
       $menuBar->addMenuItem("Lessons", "/index.php/lessons");

       #$menuBar->addMenuItem("Logo Wear", "#");

       $menuBar->addMenuItem("Membership", "/index.php/membership");
       $menuBar->addMenuItem("Related Links", "/index.php/related");
       $menuBar->addMenuItem("Benefit", "/index.php/benefit");
    
       // for now, we will just check login
       if ($loggedin) {

         $subMB = new MenuBar('masm');
         $subMB->addMenuItem("Member's Page", "/index.php/member_area"
                             ,null, array('member', 'generic_member'));
         $subMB->addMenuItem("Message Center", "/index.php/message_center"
                             ,null, array('member'));
         $subMB->addMenuItem("<hr/>", "#"
                             ,null, array('admin', 'announcement'));
         $subMB->addMenuItem("Announcements Admin"
                            ,"/index.php/announcement/maint"
                             ,null, array('admin', 'announcement'));
         $subMB->addMenuItem("Work Hours Entry Screen"
                            ,"/index.php/membership/workhours"
                             ,null, array('admin', 'workhours'));
         $subMB->addMenuItem("<hr/>", "#"
                             ,null, array('admin', 'announcement'));
         $subMB->addMenuItem("Document Upload", "/index.php/document"
                             ,null, array('admin'));
         $subMB->addMenuItem("Events Admin", "/index.php/event/maint"
                             ,null, array('admin'));
         $subMB->addMenuItem("<hr/>", "#"
                             ,null, array('admin'));
         $subMB->addMenuItem("Membership Admin"
                              ,"/index.php/membership/maint"
                             ,null, array('admin'));
         $subMB->addMenuItem("Officer Admin", "/index.php/officer/maint"
                             ,null, array('admin'));
         $subMB->addMenuItem("People Admin", "/index.php/person/maint"
                             ,null, array('admin'));
         $subMB->addMenuItem("Person Password Reset"
                              ,"/index.php/person/arup"
                             ,null, array('admin'));
         $subMB->addMenuItem("Trophy Room Admin"
                              ,"/index.php/trophy_room/maint"
                             ,null, array('admin'));
         $menuBar->addMenuItem("Members Area", "#", $subMB);
       }

       $menuBar->render($userRoles);
   ?>
  </div>
  <div id="content">
    <?php
       include($CONTROLLER->getView());
    ?>
  </div>
 </div> <!-- container -->
</body>
</html>
