<?php
  require_once('code/dboforms.php');

  $model = $CONTROLLER->getModel();
  $doc_types = $model['doc_types'];
  $doc_type = getMapValue($model, 'doc_type');
  $message = getMapValue($model, 'message');
  $dbofu = new DBOFormUtil(null);
  $documentList = getMapValue($model, 'file_list');
?>
<script type="text/javascript">
   function refreshForm() {
     document.uploadForm.document_action.value = 'refresh';
     document.uploadForm.submit();
   }
   function uploadDocument() {
     var file_name = document.uploadForm.file_name.value;
     var upload_file = document.uploadForm.uploadedfile.value;
     if (upload_file == '') {
       alert('Please select a file to upload');
     } else {
       if (file_name == '') {
         document.uploadForm.file_name.value = upload_file;
       }
       document.uploadForm.submit();
     }
   }
   function removeFile(fileName) {
      if (confirm('Are you sure you want to delete file ' + fileName + '?')) {
       document.fileForm.document_action.value = 'delete';
       document.fileForm.document_name.value = fileName;
       document.fileForm.doc_type.value = document.uploadForm.doc_type.value;
       document.fileForm.submit();
      }
   }
   function archiveFile(fileName) {
     document.fileForm.document_action.value = 'archive';
     document.fileForm.document_name.value = fileName;
     document.fileForm.doc_type.value = document.uploadForm.doc_type.value;
     document.fileForm.submit();
   }
   function unarchiveFile(fileName) {
     document.fileForm.document_action.value = 'unarchive';
     document.fileForm.document_name.value = fileName;
     document.fileForm.doc_type.value = document.uploadForm.doc_type.value;
     document.fileForm.submit();
   }
</script>
<form enctype="multipart/form-data" action="/index.php/document"
      method="post" name="uploadForm">

<table>
  <tbody>
    <tr>
      <td>Document Type</td>
<!-- make this a select -->
      <td>
        <select name="doc_type" onChange="refreshForm()">
        <?php
          foreach($doc_types as $dt) {
             if ($dt->id == $doc_type) {
               $selStr = ' selected="selected"';
             } else {
               $selStr = '';
             }
             echo '<option value="' . $dt->id . '"' . $selStr . '>' 
                   . $dt->name . '</option>' . PHP_EOL;
          }
         ?>
        </select>
    </tr>
    <tr>
      <td>File</td>
      <td><input name="uploadedfile" type="file"/></td>
    </tr>
    <tr>
      <td>Name</td>
      <td><input type="text" name="file_name"/> (if left blank, will be the name of the file uploaded)</td>
    </tr>
  </tbody>
</table>

</table>

<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
<input type="hidden" name="document_action" value="save" />
<?php echo $dbofu->createButton('Upload File', 'uploadDocument'); ?>
<?php echo $dbofu->createButton('Refresh', 'refreshForm'); ?>
</form>

<div style="clear:both;">
  <br/>
<hr/>
</div>
  <br/>
  <form name="fileForm" action="/index.php/document"
        method="post">
    <input type="hidden" name="document_action" value="" />
    <input type="hidden" name="document_name" value="" />
    <input type="hidden" name="doc_type" value="" />
<div id="dbofu-table-yui" style="clear:both;">
</div>
<table id="dbofu-table" style="display:none;">
      <thead>
        <tr>
           <th>File Name</th>
           <th>Size</th>
           <th>Date</th>
           <th>Action</th>
           <th>Archive</th>
        </tr>
      </thead>
      <tbody>
      <?php
        foreach ($documentList as $doc) {
          echo "<tr>" . PHP_EOL;
          echo "<td>" . PHP_EOL;
          echo $doc['name'] . '<br/>';
          echo "</td>" . PHP_EOL;
          echo '<td align="right">' . PHP_EOL;
          $unit = '';
          $size = $doc['stats']['size'];
          if ($size > 1000000000) {
            $size = $size / 1000000000.0;
            $unit = 'G';
          } else if ($size > 1000000) {
            $size = $size / 1000000.0;
            $unit = 'M';
          } else if ($size > 1000) {
            $size = $size / 1000.0;
            $unit = 'K';
          }
 
          echo number_format($size, 2) . $unit . '<br/>';
          echo "</td>" . PHP_EOL;
          echo "<td>" . PHP_EOL;
          echo date('n/j/Y H:i:s', $doc['stats']['mtime']) . '<br/>';
          echo "</td>" . PHP_EOL;
          echo "<td>" . PHP_EOL;
          if ($doc['archive_flag']) {
            echo "Archived";
          } else {
            echo '<a href="javascript: removeFile(\'' . $doc['name'] . '\');">delete</a>' 
              . PHP_EOL;
          }
          echo "</td>" . PHP_EOL;
          echo "<td>" . PHP_EOL;
          if ($doc['archive_flag']) {
            echo '<input type="checkbox" ' .
                  ' onclick="javascript: unarchiveFile(\'' 
                               . $doc['name'] . '\');" checked />' . PHP_EOL;
          } else {
            echo '<input type="checkbox" ' .
                  ' onclick="javascript: archiveFile(\'' 
                               . $doc['name'] . '\');"/>' . PHP_EOL;
          }
          echo "</td>" . PHP_EOL;
          echo "</tr>" . PHP_EOL;
        }
      ?>
      </tbody>
    </table>
  </form>


<?php
  if ($message != null) {
     echo '<script type="text/javascript">' . PHP_EOL;
     echo "alert('$message');" . PHP_EOL;
     echo '</script>' . PHP_EOL;
  }
?>


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
                     ,height: "300px"
              });

      dt.render("#dbofu-table-yui");
        
    });
</script>
