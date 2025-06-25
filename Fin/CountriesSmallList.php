<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'Countries')."</h4>");
  $query = "select * FROM Countries where Code2='$Code2' order by Code2 ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'Code3').'</th>');
  echo('<th>'.GetStr($pdo, 'DigCode').'</th>');
  echo('<th>'.GetStr($pdo, 'CountryName').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['Code3']}</td>");
    echo ("<td align=center>");
    echo ("{$dpL['DigCode']}</td>");
    echo ("<td>");
    echo ("{$dpL['CountryName']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='CountriesCard.php?New=1&Code2=$Code2'>".GetStr($pdo, "Add")."</a>");
?>
