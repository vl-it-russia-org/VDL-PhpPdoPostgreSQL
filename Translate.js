<script>

function CopyTxtFromTo (FromId, ToId) {
  El2=document.getElementById(FromId);
  if ( El2!= null) {

    El1=document.getElementById(ToId);
    if ( El1!= null) {

      El1.value =El2.value;
    }
  
  }
  return 0;
}
//==========================================================

//    <script async defer src="https://apis.google.com/js/api.js"
//      onload="this.onload=function(){};handleClientLoad();"
//      onreadystatechange="if (this.readyState === 'complete') this.onload();">
//   
function TranslateTxtRuEn (RuId, EnId) {
  var sourceText = '';
  var sourceLang = 'ru';
  var targetLang = 'en';

  El2=document.getElementById(RuId);
  if ( El2!= null) {
    sourceText =El2.value;
    sourceText = sourceText.replace(".", "");
    //alert (sourceText);
  }

  var url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=" 
            + sourceLang + "&tl=" + targetLang + "&dt=t&q=" + encodeURI(sourceText);
  
  //alert (url);

  var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", url, false ); // false for synchronous request
    xmlHttp.send();
  
  Res1 = xmlHttp.responseText;
  
  //confirm (Res1);
  
  var result = JSON.parse(Res1);

  translatedText = result[0][0][0];
  var res = translatedText.replace("&", "&amp;");
  res = res.replace("'", "&apos;");
  res = res.replace("\"", "&quot;");

  
  El1=document.getElementById(EnId);
  if ( El1!= null) {
    El1.value=res;
  }
  return 0;
}
//===============================================================================
function TranslateTxtGen (RuId, EnId, sourceLang, targetLang ) {
  var sourceText = '';
  
  El2=document.getElementById(RuId);
  if ( El2!= null) {
    sourceText =El2.value;
  }

  var url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=" 
            + sourceLang + "&tl=" + targetLang + "&dt=t&q=" + encodeURI(sourceText);
  

  var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", url, false ); // false for synchronous request
    xmlHttp.send();
  
  if (xmlHttp.status != 200) {
    // обработать ошибку
    alert( xmlHttp.status + ': ' + xmlHttp.statusText ); // пример вывода: 404: Not Found
  } else {  
  
    Res1 = xmlHttp.responseText;
    
    //confirm (Res1);
    
    var result = JSON.parse(Res1);

    translatedText = result[0][0][0];
    
    var res = translatedText.replace("&", "&amp;");
    res = res.replace("'", "&apos;");
    res = res.replace("\"", "&quot;");
    
    El1=document.getElementById(EnId);
    if ( El1!= null) {
      El1.value=res;
    }
  }
  return 0;
}
//====================================================================================
function TranslateTxtEn2RuFr (EnId, RuId, FrId) {
  
  TranslateTxtGen (EnId, RuId, 'en', 'ru' );
  TranslateTxtGen (EnId, FrId, 'en', 'fr' );
  
  return 0;
}
//====================================================================================
function TranslateTxtRu2EnFr (RuId, EnId,  FrId) {
  
  TranslateTxtGen (RuId, EnId, 'ru',  'en');
  TranslateTxtGen (RuId, FrId, 'ru',  'fr');
  
  return 0;
}

//====================================================================================


</script>
  


