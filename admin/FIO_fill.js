<script>
function MakeFIO (IdLet, Dir) {
  El = document.getElementById(IdLet);
  if (El==null) {
    alert ("Empty "+IdLet);
    return 0; 
  }

  Full = El.value;
  if (Dir==1) { // FIO (FI)
    Arr= Full.split(' ');
    document.getElementById("LastName").value = Arr[0];
    document.getElementById("FirstName").value = Arr[1];
    if (Arr.length > 2) {
      document.getElementById("PatronymicName__c").value = Arr[2];
    }
  }
  else  
  if (Dir==2) { // IOF (IF)
    Arr= Full.split(' ');
    if (Arr.length > 2) { //IOF
      document.getElementById("FirstName").value = Arr[0];
      document.getElementById("PatronymicName__c").value = Arr[1];
      document.getElementById("LastName").value = Arr[2];
    }
    else {
      document.getElementById("FirstName").value = Arr[0];
      document.getElementById("LastName").value = Arr[1];
    }
  }
  return 0;
}
</script>