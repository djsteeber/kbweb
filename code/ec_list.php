<?php
  require_once('code/dboforms.php');
  $model = $CONTROLLER->getModel();
  $obj = $model['object'];
  $title = $model['title'];
  $url_root = $model['url-root'];
  echo "<H2>$title</H2>" . PHP_EOL;
?>
<!-- change this to a link that acts like a button 
      so that the post can be changed to a get method
-->
<script type="text/javascript">
   function submitCreate() {
      document.createForm.submit();
   }
</script>
<form name="createForm"
      action="<?php echo $url_root . '/new'; ?>" method="POST">
  <a class="cmdbutton" 
     onclick="this.blur();"
     href="javascript: submitCreate()">
       <span>Create <?php echo $title?></span><a/>
</form>
<div style="clear:both;"></div>
<br/>
<div id="dbofu-table-yui">
</div>
<?php
  $dbofu = new DBOFormUtil($obj);
  $dbofu->showDataTable($url_root);
?>
<script>

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
                      width: "930px"
                     ,height: "400px"
              });

      dt.render("#dbofu-table-yui");
        
    });
</script>


