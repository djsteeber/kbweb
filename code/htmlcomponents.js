
function updateSelectBoxHidden(selectBox) {
  var hf = document.getElementById(selectBox.id + '_value');
  var len = selectBox.length;
  if (len == 1) {
    hf.value=selectBox.options[0].text;
  } else if (len > 1) {
    var optValues = []
    for (var i=0; i < len;i++) {
      optValues.push(selectBox.options[i].text); 
    }
    hf.value = optValues.join(';');
  } else {
    hf.value = '';
  }
}

function addOption(selectBoxId,tf) {
  var selectBox = document.getElementById(selectBoxId);
  var hf = document.getElementById(selectBoxId + '_value');
  if (tf.value == '') return;
  var opt = document.createElement("OPTION");
  opt.name='opt';
  opt.text=tf.value;
  selectBox.options.add(opt);
  tf.value = '';
  if (selectBox.length < 2) {
    selectBox.size = 2;
  } else {
    selectBox.size = selectBox.length;
  }
  updateSelectBoxHidden(selectBox);
}

function removeOption(selectBox) {
  if (selectBox.selectedIndex >= 0) {
     selectBox.options.remove(selectBox.selectedIndex);
     if (selectBox.length < 2) {
       selectBox.size = 2;
     } else {
       selectBox.size = selectBox.length;
     }
     updateSelectBoxHidden(selectBox);
  }
}
function isEnterPressed(e) { 
  var rtn = false;
  if (typeof e == 'undefined' && window.event) {
     e = window.event;
  } 
  if (e.keyCode == 13) {
    rtn = true;
  }
  return rtn;
}

function setCBTFValue(checkBox, tfId) {
  var tf = document.getElementById(tfId);
  if (checkBox.checked) {
    tf.value = '1';
  } else { 
    tf.value = '0';
  }
}
