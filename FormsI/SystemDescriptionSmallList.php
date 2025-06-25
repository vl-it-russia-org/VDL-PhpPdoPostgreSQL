<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'SystemDescription')."</h4>");
  $query = "select * FROM SystemDescription where Id='$Id' order by Id ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'ParagraphNo').'</th>');
  echo('<th>'.GetStr($pdo, 'ElType').'</th>');
  echo('<th>'.GetStr($pdo, 'Description').'</th>');
  echo('<th>'.GetStr($pdo, 'Ord1').'</th>');
  echo('<th>'.GetStr($pdo, 'ParentId').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['ParagraphNo']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "PGElType", $dpL['ElType'])."</td>");
    echo ("<td>");
    echo ("{$dpL['Description']}</td>");
    echo ("<td align=center>");
    echo ("{$dpL['Ord1']}</td>");
    echo ("<td align=center>");
    echo ("{$dpL['ParentId']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='SystemDescriptionCard.php?New=1&Id=$Id'>".GetStr($pdo, "Add")."</a>");
?>
