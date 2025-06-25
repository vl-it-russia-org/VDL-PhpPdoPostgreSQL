<script>
function SelAll () {
  SubStr='';
  El1=document.getElementById('SubStr');
  if ( El1!= null) {
    SubStr=El1.value;
  }
  MaxVal=document.getElementById('AllCnt').value;

  for (i=1;i<=MaxVal;i++) {
    
    EL= document.getElementById('Chk_'+i);
    
    if (EL != null) {
      if (SubStr!= '') {
        Val1= EL.value;
        Pos= Val1.indexOf(SubStr);
        if (Pos>0) {
          EL.checked= !EL.checked;   
        }
      }
      else {
        EL.checked= !EL.checked;   
      }
    }
  }
}
//============================================
function MSChangeVal(SelName) {
  Cnt=0;
  
  el=document.getElementById(SelName+'_MaxQty');
  if (el!=null) {
    Cnt=el.value;
  }
  
  for (i=1;i<=Cnt;i++) {
    el=document.getElementById(SelName+'_SEL_'+i);
    if (el!=null) {
      el.checked= !el.checked; 
    }
  }  
  return 0;
}
//=========================================================
function MSClrVal(SelName) {
  Cnt=0;
  
  el=document.getElementById(SelName+'_MaxQty');
  if (el!=null) {
    Cnt=el.value;
  }
  
  for (i=1;i<=Cnt;i++) {
    el=document.getElementById(SelName+'_SEL_'+i);
    if (el!=null) {
      if (el.checked)
        el.checked= !el.checked; 
    }
  }

  el=document.getElementById(SelName+'_SelAll');
  if (el!=null) {
    if (el.checked)
      el.checked= !el.checked; 
  }
  
  return 0;
}
//=========================================================

</script>
