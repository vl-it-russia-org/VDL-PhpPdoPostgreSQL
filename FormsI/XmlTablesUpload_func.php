<?php

function ReadIndx (&$pdo, $TabCode, &$IndxStr) {
  $IndxCnt=0;
  $TabName='';
  // Узнаем название таблицы
  $query = "select \"TabName\" from \"AdmTabNames\" ".
           "where (\"TabCode\"=:TabNo)";
  
  $PdoArr = array();
  $PdoArr['TabNo']= $TabCode;

  try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

    if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
      $TabName = $dp2['TabName'];  
    };

    echo ("<br>$TabName indexes:<br>");
    if ($TabName=='') {
      die ("<br> Error: Have no table with Table code = $TabCode "); 
    }



      
      
      
      //=======================================================================
      //
      //  Indexes
      //
      //=======================================================================
      $j=0;
      $Pos1=0;
      $FMore=0;
      if ($IndxStr!='') {
        $FMore=1;
      }
      
      $IndxName='';
      try {
      while ($FMore==1) {
        $IndxTxt = GetXMLVal ( $IndxStr, 'Index', $Pos1); 
        
        
        if ( $IndxTxt != '') {
          $IndxArr=array();
          $j=0;

          $Fld='TabCode';
          $FldsArr[$Fld]= $TabCode;

          $Fld='IndxType';
          $FldsArr[$Fld]= GetXMLVal ( $IndxTxt, $Fld, $j);
          $IndxType= addslashes ($FldsArr[$Fld]);

          $Fld='IndxName';
          $FldsArr[$Fld]= GetXMLVal ( $IndxTxt, $Fld, $j);
          $IndxName = addslashes($FldsArr[$Fld]);
          if ($FldsArr['IndxType']==10) {
            $IndxName = $TabName.'_pkey';
            $FldsArr[$Fld]=$IndxName;
          }
          $IndxCnt++;
          // AdmTabIndx
          // TabCode, IndxType, IndxName
          $query = "select * from \"AdmTabIndx\" ". 
                   "where (\"TabCode\"= :TabCode )and(\"IndxName\" = :IndxName)"; 
          
          $PdoArr = array();
          $PdoArr['TabCode'] =$TabCode;
          $PdoArr['IndxName']=$IndxName;
          
          //echo ("<br> Line ".__LINE__.": $query<br>");
          //print_r($PdoArr);
          //echo ("<br>");

          
          $STH22 = $pdo->prepare($query);
          $STH22->execute($PdoArr);

          if ($dp22 = $STH22->fetch(PDO::FETCH_ASSOC)) {          
            echo ("<br> Already have Index $TabName : $IndxName ");
          }
          else {
            // Add index to table
            $query = "insert into \"AdmTabIndx\" (\"TabCode\", \"IndxType\", \"IndxName\")". 
                     "values (:TabCode, :IndxType, :IndxName)"; 


            $PdoArr['IndxType']=$IndxType;
            $STHIns = $pdo->prepare($query);
            $STHIns->execute($PdoArr);

            $ErrArr = $STHIns->errorInfo();
            echo ("<br> Line ".__LINE__.":ErrArr: ");
            print_r($ErrArr);
            echo ("<br>");


          }
        
          $IndxPos=0;
          $IndxMore = 1;
          
          $PdoArr = array();
          $PdoArr['TabCode'] =$TabCode;
          $PdoArr['IndxName']=$IndxName;

          while ($IndxMore==1) {
          
            $IndxFld = GetXMLVal ($IndxTxt, 'IndxField', $IndxPos);

            if ($IndxFld== '') {
              $IndxMore=0;
            }
            else {
              $j=0;
              $ILineNo = GetXMLVal ($IndxFld, 'LineNo', $j);
              $j=0;
              $IFldNo = GetXMLVal ($IndxFld, 'FldNo', $j);
              $j=0;
              $IOrd = GetXMLVal ($IndxFld, 'Ord', $j);
            
              // AdmTabIndxFlds
              // TabCode, IndxName, LineNo, FldNo, 
              // Ord
              $query = "select * from \"AdmTabIndxFlds\" ". 
                       "where (\"TabCode\"= :TabCode) and (\"IndxName\"=:IndxName) and (\"LineNo\"=:LineNo)"; 

              $PdoArr['LineNo']=$ILineNo;
              
              $STH25 = $pdo->prepare($query);
              $STH25->execute($PdoArr);

              //echo ("<br> Line ".__LINE__.": $query<br>");
              //print_r($PdoArr);
              //echo ("<br>");

              if ($dp25 = $STH25->fetch(PDO::FETCH_ASSOC)) {
              
                $query = "update \"AdmTabIndxFlds\" set \"FldNo\"=:FldNo, \"Ord\"=:Ord ". 
                         "where (\"TabCode\"=:TabCode) and (\"IndxName\"=:IndxName) and (\"LineNo\"=:LineNo)"; 

                $PdoArr1 = array();
                $PdoArr1['TabCode'] =$TabCode;
                $PdoArr1['IndxName']=$IndxName;
                $PdoArr1['LineNo']=$ILineNo;
                
                $PdoArr1['FldNo']=$IFldNo;
                $PdoArr1['Ord']=$IOrd;
                
                $STH75 = $pdo->prepare($query);
                $STH75->execute($PdoArr1);

                //echo ("<br> Line ".__LINE__.": $query<br>");
                //print_r($PdoArr1);
                //echo ("<br>");

              }
              else {
                $query = "insert into \"AdmTabIndxFlds\" (\"TabCode\", \"IndxName\", \"LineNo\", \"FldNo\", \"Ord\") ".
                         "values (:TabCode, :IndxName, :LineNo, :FldNo, :Ord )"; 

                $PdoArr1 = array();
                $PdoArr1['TabCode'] =$TabCode;
                $PdoArr1['IndxName']=$IndxName;
                $PdoArr1['LineNo']=$ILineNo;
                
                $PdoArr1['FldNo']=$IFldNo;
                $PdoArr1['Ord']=$IOrd;
                
                $STH75 = $pdo->prepare($query);
                $STH75->execute($PdoArr1);

                //echo ("<br> Line ".__LINE__.": $query<br>");
                //print_r($PdoArr1);
                //echo ("<br>");

              }
            }
          }
        }
        else {
          $FMore=0;
        
        
        }
      }
      }
      catch (PDOException $e) {
        echo ("<hr> Line ".__LINE__."<br>");
        echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
        print_r($PdoArr);	
        die ("<br> Error: ".$e->getMessage());
      }
  }
  catch (PDOException $e) {
  }
  return $IndxCnt;
}
//---------------------------------------------------------------------------------

function ReadTab (&$pdo, &$ReadTab) {

  $Flds1 = array ('TableName'=>'TabName', 'TabDescription'=>'TabDescription', 
                  'TabEditable'=>'TabEditable', 'ChangeDt'=>'ChangeDt', 'Ver'=>'Ver');

  $ConnArr= array ('LineNo', 'TabCond', 'Tab2', 'Field2', 'CondTab2', 
                   'AddFldsListTo', 'AddFldsListFrom', 'SelectViewName', 'AddConnFldFrom', 'AddConnFldTo');

      $Arr=array();
      $Fld='TabCode';
      $j=0;
      $Arr[$Fld]= GetXMLVal ( $ReadTab, $Fld, $j);

      echo ("<br>$Fld : {$Arr[$Fld]} ");
      
      $Fld='TableName';
      $j=0;
      $Arr[$Fld]= GetXMLVal ( $ReadTab, $Fld, $j);
      $TabName=$Arr[$Fld];

      $Fld='TabDescription';
      $j=0;
      $Arr[$Fld]= GetXMLVal ( $ReadTab, $Fld, $j);
      
      $Fld='ChangeDt';
      $j=0;
      $Arr[$Fld]= GetXMLVal ( $ReadTab, $Fld, $j);
      if ($Arr[$Fld]=='0000-00-00 00:00:00') {
        $Arr[$Fld]='2000-01-01 00:00:00';
      }

      $Fld='Ver';
      $j=0;
      $Arr[$Fld]= GetXMLVal ( $ReadTab, $Fld, $j);

      
      $Fld='TabEditable';
      $j=0;
      $Arr[$Fld]= GetXMLVal ( $ReadTab, $Fld, $j);

      // AdmTabNames
      // TabName, TabDescription, TabCode, TabEditable
      $query = "select * from \"AdmTabNames\" ". 
               "where (\"TabName\" = :TabName)"; 

      echo ("<br>Line 29: $query<br>");
      
      $PdoArr=array();
      $PdoArr['TabName']=$Arr['TableName'];
      try {
        $STH = $pdo->prepare($query);
        $STH->execute($PdoArr);


        if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
          if (  $Arr['TabCode'] != $dp2['TabCode']) {
            echo ("<br> Be careful: Table code is not same: Have={$dp2['TabCode']} Insert={$Arr['TabCode']}<br>");  
          }
          
          $Arr['TabCode']= $dp2['TabCode']; 
          $TabCode = $dp2['TabCode'];

        
          $Fld='TabDescription';
          $Str='';
          $Div='';
          $PdoArr=array();
          if ($Arr[$Fld]!= $dp2[$Fld]) {
            $PdoArr[$Fld]=$Arr[$Fld];
            $Str.= "$Div\"$Fld\"=:$Fld";
            $Div=', ';
          };

          if ($Str!= '') {
            $PdoArr['TabCode']= $TabCode;
            $query = "update \"AdmTabNames\" set $Str ". 
                     "where (\"TabCode\" = :TabCode)"; 

            //echo ("<br>$query<br>");

            try {
              $STUpd = $pdo->prepare($query);
              $STUpd->execute($PdoArr);
            }
            catch (PDOException $e) {
              die ("<br> Error: ".$e->getMessage());
            }
          }
      }
      else {
        
        $query = "select * from \"AdmTabNames\" ". 
                 "where (\"TabCode\" = :TabCode)"; 

        $PdoArr = array();
        $PdoArr['TabCode']= $Arr['TabCode'];
	//echo ("<br>$query<br>");
        
        
        $TabCode = $Arr['TabCode'];

        try {
          $STH2 = $pdo->prepare($query);
          $STH2->execute($PdoArr);

          if ($dp2 = $STH2->fetch(PDO::FETCH_ASSOC)) {
        
            //$Arr['TabCode']= $dp2['TabCode']; 
            echo ("<br> Have table: ");
            print_r($dp2);
            echo("<br>");
            // New Id -- Max + 1;

            die ("<br> Error");

            // AdmTabNames
            // TabName, TabDescription, TabCode, TabEditable
            $Str1='';
            $Str2='';
            $Div='';
            foreach ($Arr as $Fld=>$Val) {
              if ($Fld !=  'TabCode') {
                $F1 = $Fld;
                if (!empty($Flds1[$F1])) {
                  $F1=$Flds1[$F1]; 
                }
                $Str1.="$Div\"$F1\"";
                
                
                $Str2.="$Div'$Val'";
                $Div=', ';
              }
            }

            $query = "insert into \"AdmTabNames\"($Str1)values($Str2)"; 
            echo ("<br>$query<br>");
            
            $InsTabCnt++;
            
            $sql5 = $mysqli->query ($query) 
                    or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
            
            $Arr['TabCode']=$mysqli->insert_id;

        }
        else {
          // AdmTabNames
          // TabName, TabDescription, TabCode, TabEditable
          $Str1='';
          $Str2='';
          $Div='';
          print_r($Arr);
          $PdoArr=array();
          foreach ($Arr as $Fld=>$Val) {
              $Fld1=$Flds1[$Fld];
              if ($Fld=='TabCode') {
                $Fld1= $Fld;
              
              }
              
              $Str1.="$Div\"$Fld1\"";
              $Str2.="$Div:$Fld1";
              
              $PdoArr[$Fld1]=$Val;

              $Div=', ';
          }

          $query = "insert into \"AdmTabNames\"($Str1)values($Str2)"; 
          
          //echo ("<br>$query<br>");
          try {
            $STIns = $pdo->prepare($query);
            $STIns->execute($PdoArr);  
          }
          catch (PDOException $e) {
            die ("<br> Error: ".$e->getMessage());
          }
          $InsTabCnt++;
        }
          }
          catch (PDOException $e) {
            die ("<br> Error: ".$e->getMessage());
          }
      }

            }
            catch (PDOException $e) {
              die ("<br> Error: ".$e->getMessage());
            }

    
      // Fields
      $j=0;
      $TxtFlds=GetXMLVal ( $ReadTab, 'Fields', $j);
      $Pos1=0;
      $FMore=1;

      $FieldName='';
      
      while ($FMore==1) {
        $FldTxt = GetXMLVal ( $TxtFlds, 'Field', $Pos1); 
        if ( $FldTxt != '') {
          $FldsArr=array();
          $j=0;
          $FldsArr['TypeId']=$Arr['TabCode'];

          $Fld='ParamNo';
          $FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);

          $Fld='ParamName';
          $j=0;
          $FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);
          $FieldName=$FldsArr[$Fld];

          $Fld='DocParamType';
          $j=0;
          $FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);

          $Fld='Ord';
          $j=0;
          $FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);

          $Fld='AddParam';
          $j=0;
          $FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);

          $Fld='AutoInc';
          $j=0;
          $FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);
          
          $Fld='EnumLong';
          $j=0;
          $FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);
          if (empty($FldsArr[$Fld])) {
            $FldsArr[$Fld]=0;
          }

          $Fld='Description';
          $j=0;
          $FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);

          $Fld='ShortInfo';
          $j=0;
          //$FldsArr[$Fld]= GetXMLVal ( $FldTxt, $Fld, $j);
          $FldsArr[$Fld]= 0;
          
          // AdmTabFields
          // TypeId, ParamNo, ParamName, NeedSeria, 
          // DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
          // CalcFormula, AutoInc, Description, BinCollation, ShortInfo
          $PdoArr = array();
          $query = "select * from \"AdmTabFields\" ". 
                   "where (\"TypeId\"=:TypeId) and (\"ParamName\"=:ParamName) "; 
          
          $PdoArr['TypeId']=$FldsArr['TypeId'];
          $PdoArr['ParamName']=$FldsArr['ParamName'];

          //echo ("<br> Line ".__LINE__.": $query<br>");
          //print_r($PdoArr);
          //echo ("<br>");

          try {
            $STH7 = $pdo->prepare($query);
            $STH7->execute($PdoArr);

            if ($dp7 = $STH7->fetch(PDO::FETCH_ASSOC)) {
              
              $FldName7=$dp7['ParamName'];
              $Str1='';
              $Div='';
              foreach ($FldsArr as $Fld=>$Val) {
                if ($dp7[$Fld]!=$Val) {    
                  $Str1.="$Div\"$Fld\"='$Val'";
                  $Div=', ';
                }
              }
              if ($Str1!= '') {
                echo ("<br> upd $FldName7 : $Str1<br>");
                $UpdFldCnt++;

                $query = "update \"AdmTabFields\" set $Str1 ".
                         "where (\"TypeId\"=:TypeId) and (\"ParamName\"=:ParamName)"; 
              
                //echo ("<br> Line ".__LINE__.": $query<br>");
                //print_r($PdoArr);
                //echo ("<br>");

                //echo ("<br>$query<br>");
                $STH5 = $pdo->prepare($query);
                $STH5->execute($PdoArr);
              }
          }
          else {
              $Str1='';
              $Str2='';
              $Div='';
              $PdoArr = array();
              foreach ($FldsArr as $Fld=>$Val) {
                  $Str1.="$Div\"$Fld\"";
                  $PdoArr[$Fld]=$Val;
                  $Str2.="$Div:$Fld";
                  $Div=', ';
              }

              $query = "insert into \"AdmTabFields\"($Str1)values($Str2)"; 
              
              //echo ("<br> Line ".__LINE__.": $query<br>");
              //print_r($PdoArr);
              //echo ("<br>");

              $STIns = $pdo->prepare($query);
              $STIns->execute($PdoArr);
                
              $InsFldCnt++;
          }
        }
        catch (PDOException $e) {
          echo ("<hr> Line ".__LINE__."<br>");
          echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
          print_r($PdoArr);	
          die ("<br> Error: ".$e->getMessage());
        }


          //=============================================================
          // Input connection FIeld to other Tables
          //=============================================================
          $Fld='FldConn';
          $j=0;
          $ConnTxt= GetXMLVal ( $FldTxt, $Fld, $j);

          if ( $ConnTxt!= '') {
            $More=1;
            $j=0;
            $i=0;
            while ($More==1) {
              $i++;
              if ($i>20) {
                die ('<br> BAD CONN cycle');
              }
              $ConnTxt1= GetXMLVal ( $ConnTxt, 'Conn', $j);
              if ($ConnTxt1!='') {
                $FArr=array();
                foreach ($ConnArr as $F) {
                  $j1=0;
                  $FArr[$F]= GetXMLVal ($ConnTxt1, $F, $j1);
                }
              }
              else {
                $More=0;
              }
            }
           
            //echo ("<hr>Connections $TabName / $FieldName : ");
            //print_r ($FArr);
            // AdmTab2Tab
            // TabName, FldName, LineNo, TabCond, 
            // Tab2, Field2, CondTab2, AddConnFldFrom, AddConnFldTo, 
            // Id, AddFldsListTo, AddFldsListFrom, SelectViewName
            $LineNo = $FArr['LineNo'];

            $PdoArr = array();

            $query = "select * from \"AdmTab2Tab\" ". 
                     "where (\"TabName\" = :TabName ) and (\"FldName\"= :FldName ) and ".
                           "(\"LineNo\"=:LineNo)"; 
            
            $PdoArr['TabName']  = $TabName;
            $PdoArr['FldName']= $FieldName;
            $PdoArr['LineNo']= $LineNo;

            try {
              $STH22 = $pdo->prepare($query);
              $STH22->execute($PdoArr);

              if ($dp22 = $STH22->fetch(PDO::FETCH_ASSOC)) {
              // Already have this connection
                $Upd='';
                $DivUpd='';
                $PdoArr = array();

                foreach ($ConnArr as $F) {
                  if ($dp22[$F]!= $FArr[$F]){
                    $V= addslashes ();
                    $Upd.= "$DivUpd\"$F\"=:$F";
                    $PdoArr[$F]=$FArr[$F];
                    $DivUpd=', ';
                  }
                }

                if ( $Upd != '') {
                  echo ("<br>Connections $TabName / <b>$FieldName</b> updated :  $Upd <br>");
                  $PdoArr['Id']=$dp22['Id'];
                  
                  $query = "update \"AdmTab2Tab\" set $Upd ". 
                           "where (\"Id\" = :Id)"; 

                  $STH27 = $pdo->prepare($query);
                  $STH27->execute($PdoArr);
                }
              }
              else {
                // insert new connection

                echo ("<br>Connections $TabName / <b>$FieldName</b> add new to {$FArr['Tab2']} <br>");
                $S1='';
                $S2='';
                $PdoArr = array();
                foreach ($ConnArr as $F) {
                  $S1.=", \"$F\"";
                  $PdoArr[$F]= $FArr[$F];
                  $S2.=", :$F";
                }
                
                $PdoArr['TabName']= $TabName;
                $PdoArr['FldName']= $FieldName;

                
                $query = "insert into \"AdmTab2Tab\" (\"TabName\", \"FldName\"$S1) ". 
                         "values (:TabName, :FldName$S2)";
                          
                $STH27 = $pdo->prepare($query);
                $STH27->execute($PdoArr);
              }
          
            }
            catch (PDOException $e) {
              echo ("<hr> Line ".__LINE__."<br>");
              echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
              print_r($PdoArr);	
              die ("<br> Error: ".$e->getMessage());
            }
          
          
          }
        }
        else {
          $FMore=0;
        }
      }

  return  $Arr['TabCode'];
}


?>