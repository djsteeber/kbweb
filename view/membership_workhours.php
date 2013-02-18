<?php
  $model = $CONTROLLER->getModel();
  $members = getMapValue($model, 'members');
?>
<script type="text/javascript">
  function resetView() {
    alert('resetting view to original');
    document.workHoursForm.reset();
  }

  function saveWorkHours() {
    alert('saving work hours');
    document.workHoursForm.action.value = 'save';
    document.workHoursForm.submit();
  }

  function leavingThePageWithChanges() {
    alert('changes will be lost');
  }

</script>
<H2>Work Hours Entry</H2>
  <form name="workHoursForm" method="POST" 
       action="/index.php/membership/workhours">
    <input type="hidden" name="action" value="view" />
    <div id="dbofu-table-yui" style="clear:both;">
    </div>
    <table id="dbofu-table" style="display:none;">
      <tr>
        <th>Name</th>
        <th>Spouse</th>
        <th>Status</th>
        <th>Current Work Hours</th>
        <th>New Work Hours</th> 
      </tr>
      <?php
        foreach ($members as $membership) {
         echo '<tr>' . PHP_EOL;
         echo '<td>' . $membership->name . '</td>' . PHP_EOL;
         echo '<td>' . $membership->spouse . '</td>' . PHP_EOL;
         echo '</td>' . PHP_EOL;
         echo '<td>' . $membership->status->name . '</td>' . PHP_EOL;
         echo '<td>' . $membership->work_hours . '</td>' . PHP_EOL;
         echo '<td><input type="text" name="nwh:' . $membership->id . '" 
                          value=""/></td>' 
              . PHP_EOL;
         echo '</tr>' . PHP_EOL;
        }
      ?>
    </table>
  </form>
  <div style="clear:both;">
     <a class="cmdbutton" onclick="this.blur();"
        href="javascript: resetView();">
       <span>Reset</span>
     </a>
     <a class="cmdbutton" onclick="this.blur();"
        href="javascript: saveWorkHours();">
       <span>Save Changes</span>
     </a>
  </div>
  <div style="clear:both;">
    <br />
  </div>
<script type="text/javascript">
YUI().use( "datatable", function (Y) {
    var dt;
    
    function parseHTMLTable( table_id, destroy_flag ) {
        var tnode = Y.one( table_id ),
            a_thead = [],
            a_tr    = [];
    //
    //  Extract the TH contents, put in a_thead array
    //            
        tnode.all("th").each(function(item){
            a_thead.push( item.getContent() );
        });
        
    //
    //  Extract the TR contents, put in a_thead array
    //            
        tnode.all("tbody tr").each(function(item){
               var tr_obj = {};
            item.all("td").each( function(titem, tindex){
                tr_obj[ a_thead[tindex] ] = titem.getContent();                
            });
            a_tr.push( tr_obj );
        });

    // finished, if destroy_flag TRUE, delete it
        if ( destroy_flag === true) tnode.setContent('');
    
        return { cols:a_thead, data:a_tr };        
    }

     var table_data = parseHTMLTable( "#dbofu-table", true );
      
      var dt = new Y.DataTable.Base( {
            columnset: table_data.cols,
            recordset: table_data.data
        });

      dt.plug(Y.Plugin.DataTableScroll, {
                      width: "860px"
                     ,height: "400px"
              });

      dt.render("#dbofu-table-yui");
        
    });
</script>


