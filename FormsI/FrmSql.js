<script>


function GetTabName(Fldv='TabName') {
  TabName='';

  
  Window.TabInfo=[];
  Window.FldInfo=[];

  
  El1=document.getElementById(Fldv);
  if ( El1!= null) {
    TabName=El1.value;
  }
  if (TabName != '') {
    xhr = new XMLHttpRequest();
    xhr.onload = function() {
      // Here you can use the Data
      //alert (1);
      //alert (xhr.responseText);

      const obj = JSON.parse(xhr.responseText);

      Cnt=obj.Count;
      //alert( 'Count= '+Cnt);
      TabList = document.getElementById('TabList');
      TabList.options.length=0;

      Window.TabInfo=obj;
      
      for (let i = 0; i < Cnt; i++) {
        ii=i+1;
        TabList.options[i]= new Option(obj[ii].TabName,obj[ii].TabName, false, false);
      }
    }

    //var myArray = [1, 2, 3];
    //var myJson = JSON.stringify(myArray); // "[1,2,3]"
    xhr.open ('GET', 'GetTabNames.php?Tab='+TabName, true);
    xhr.send();
  }
  
  return false;
}
//=====================================================================================
function CopyTxtValue(FromFld, ToFld) {
  TabName='';
  
  El1=document.getElementById(FromFld);
  if (El1!= null) {
    TabName= El1.value;
  }

  El1=document.getElementById(ToFld);
  if (El1!= null) {
  
    El1.value = TabName;
  }
  return 0;
}
//=====================================================================================
function TableSelected(Fldv='TabName') {
  TabName='';
  
  El1=document.getElementById('TabList');
  TabName= El1.value;
  
  El1=document.getElementById(Fldv);
  El1.value = TabName;
  if (TabName != '') {
    xhr = new XMLHttpRequest();
    xhr.onload = function() {
      // Here you can use the Data
      //alert (1);
      //alert (xhr.responseText);

      const obj = JSON.parse(xhr.responseText);

      Cnt=obj.Count;
      //alert( 'Count= '+Cnt);
      TabList = document.getElementById('FldList');
      TabList.options.length=0;

      Window.FldInfo=obj;

      
      for (let i = 0; i < Cnt; i++) {
        ii=i+1;
        TabList.options[i]= new Option(obj[ii].FldName,obj[ii].FldName, false, false);    
      }


    }

    //var myArray = [1, 2, 3];
    //var myJson = JSON.stringify(myArray); // "[1,2,3]"
    xhr.open ('GET', 'GetTabFilelds.php?Tab='+TabName, true);
    xhr.send();
  }

}
//=====================================================================================
function insertAtCursor(myField, myValue) {
    //IE support
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    //MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue
            + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }
}
//=====================================================================================
function PutTabName() {
  TabName='';
  
  El1=document.getElementById('TabList');
  TabName= El1.value;
  
  El1=document.getElementById('SqlTxt');

  insertAtCursor(El1, TabName);
  
}
//=====================================================================================
function ShowTabInfo() {
  El1=document.getElementById('TabList');
  TabName= El1.value;
  
  if (TabName!='') {
    obj=Window.TabInfo;

    Cnt=obj.Count;
    j=1;
    Found=0;
    while ( Found==0) {
      if (obj[j].TabName==TabName) {
        Found=1;
      
        Txt = 'TabCode: <a href="TabCard.php?TabCode='+obj[j].TabCode+
              '" target=Tab'+obj[j].TabCode+'>'+obj[j].TabCode+'</a>'+
              '<br>TabDescription: '+obj[j].TabDescription+
              '<br><a href="BuildCycle.php?TabNo='+obj[j].TabCode+'">Cycle to file</a>' ; 
        El1=document.getElementById('TabInfo');
        El1.innerHTML=Txt;
      }
      else {
        j++;
        if (j> Cnt) {
          Found=-1;
        }
      }
    }
    
  };
}
//=====================================================================================
function ShowFldInfo() {
  El1=document.getElementById('FldList');
  FldName= El1.value;
  
  if (TabName!='') {
    obj=Window.FldInfo;

    Cnt=obj.Count;
    j=1;
    Found=0;
    while ( Found==0) {
      if (obj[j].FldName==FldName) {
        Found=1;
      
        Txt = 'FldCode: '+obj[j].ParamNo+'\r\nFldType: '+obj[j].FldType; 
        El1=document.getElementById('FldInfo');
        El1.value=Txt;
      }
      else {
        j++;
        if (j> Cnt) {
          Found=-1;
        }
      }
    }
    
  };
}


//=====================================================================================
function FldSelected() {
  FldName='';
  
  El1=document.getElementById('FldList');
  FldName= El1.value;

  if (FldName!='') {
  
    El1=document.getElementById('SqlTxt');

    insertAtCursor(El1, FldName);
  }  
}


</script>