<script>
function SetValById (Id, Value, ValTxt) {
  El1=document.getElementById(Id);
  if ( El1!= null) {
    El1.value = Value;
  }

  TxtEl= Id+'Txt';
  El1=document.getElementById(TxtEl);
  if ( El1!= null) {
    El1.value = ValTxt;
  }

  return 0;
}
</script>
