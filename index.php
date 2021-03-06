<?php

$version = '0.8';
$build = '3e5f1b';

$versioning = 'Version: '.$version.' ('.$build.')';

require('mysql.php'); 

if(!$link) {
    die('Keine Verbindung: '.mysql_error());
};

// Auswählen der Datenbank
$db_selected = mysql_select_db('d0131787', $link);

if(!$db_selected) {
    die ('Kann Datenbank nicht nutzen: ' .mysql_error());
};
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Blutdruck</title>
<meta name="format-detection" content="telephone=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="apple-mobile-web-app-capable" content="yes" /> 
<meta name="viewport" content="width=device-width, user-scalable=no">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="apple-touch-icon" href="http://blut.aaronbauer.org/apple-touch-icon.png"/>
<!-- Verhindert das Links unter iOS in Mobile Safari geöffnet werden -->
<script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script>
<?php  
	if ($_GET['name']!='') {
	$graph_query = 'SELECT *
						FROM 
						   (
							SELECT * 
								FROM blut 
								WHERE name="'.$_GET['name'].'" ORDER BY id DESC LIMIT 20
							) 
						AS tbl ORDER BY timestamp';
						
	$graph_result = mysql_query($graph_query) or die (mysql_error());
	}
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Zeit');
        data.addColumn('number', 'Dia in mm Hg');
        data.addColumn('number', 'Sys in mm Hg');
        data.addRows([
        <?php
       if ($_GET['name']!='') {
        while ($graph = mysql_fetch_assoc($graph_result)) {
    echo '["'.$graph['timestamp'].'", '.$graph['dia'].', '.$graph['sys'].'],';	
    	
    } 
    }?>
          
        ]);

        var options = {
          width: 500, height: 300, fontName: 'Helvetica Neue', fontSize: 10, curveType: 'function',
          title: 'Blutdruck - <?php echo $_GET['name']; ?>'
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

<style type="text/css">
	body {
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-weight: lighter;
		background: url(whitey-2.png) repeat;q
		font-color: #333;
	}
	
	hr {
		border: 0.1em solid #333;
	}
	
	#wrap {
		background: #fff;
		border: 1px solid #999;
		padding: 5px 20px 5px 20px;
		width:500px;
		margin-left: auto;
		margin-top: 25px;
		margin-bottom: 25px;
		margin-right: auto;
		box-shadow: 5px 5px 1px #999;
	}
	input {
		font-size: 40pt;
	}
	
	.button {
		font-size: 10pt;
	}
	
	table {		
		border: 1pt solid black;
		text-align: center;
		padding: 3px;
		font-size: 10pt;
	}
	
	td {
		border: 1pt solid grey;
		padding: 10px;
	}
	
	div.important {
		background: red;
		font-weight: bold;
		color: #fff;
		margin: 15px;
		padding: 15px;
	}
	
	h1 {
		font-size: 28pt;
	}
	
	h2 {
		background: blue;
		margin: 15px 0px 15px 0px;
		padding: 5px 5px 5px 5px;
		font-size: 12pt;
		color:#fff;
	}
	
	a:link { font-weight:bold; color:#000; text-decoration:underline; -webkit-transition: 2s linear; }
a:visited { font-weight:bold; color:#000; text-decoration:none; }
a:focus { font-weight:bold; color:#000; text-decoration:underline; }
a:hover { font-weight:bold; color:#000; text-decoration:none;}
a:active { font-weight:bold; color:#000; text-decoration:underline; }
	
</style>
</head>
<body>
<div id="wrap">
<h1><a href="index.php">Blutdruck</a></h1>
<?php

# Erster Schritt: Name
if ($_GET['name']=='' and $_POST['dia']=='' and $_POST['sys']=='' and $_GET['page']=='') {

	$read_query = 'SELECT * FROM blut ORDER BY id DESC';
    $exec_read = mysql_query($read_query) or die(mysql_error());
    $data = mysql_fetch_array($exec_read) or die(mysql_error());
    
    
    echo 'Hallo. Zuletzt gemessen hat hier <b>'.$data['name'].'</b> am '.date("d.m.Y \u\m H:i",strtotime($data["timestamp"])).'.';
    
	echo '<h2>1. Sag mir wer du bist</h2>';
	echo '<p>Sag mir wer du bist, damit ich alles richtig eintragen kann.</p>';
	echo '<form action="index.php" method="get">
<select name="name">
  <option>Verena</option>
  <option>Franz</option>
  <option>Leander</option>
  <option>Aaron</option>
</select>
 
<input type="submit" value="Weiter" class="button" />
</form>';
   
    
}

# Zweiter Schritt: Blutdruck
if($_GET['name']!='' and $_POST['dia']=='' and $_POST['sys']=='' and $_GET['page']=='') {

echo '<h2>2. Miss deinen Blutdruck</h2>';
echo 'Hallo '.$_GET['name'].'!';

	$read_query = 'SELECT AVG(dia), AVG(sys) FROM blut WHERE name="'.$_GET['name'].'" ORDER BY id DESC'; // Holt die Durchschnitte, kann aber den Zeitstempel nicht holen
	$timestamp_query = 'SELECT * FROM blut WHERE name="'.$_GET['name'].'" ORDER BY id DESC'; // Holt den Zeitstempel noch extra
	$exec_timestamp = mysql_query($timestamp_query) or die(mysql_error());
	$timestamp_data = mysql_fetch_array($exec_timestamp) or die (mysql_error());
    $exec_read = mysql_query($read_query) or die(mysql_error());
    $data = mysql_fetch_array($exec_read) or die(mysql_error());
    
    //Zählt die Anzahl der Messungen pro Person
    $top_persons = 'SELECT COUNT(name) FROM blut WHERE name="'.$_GET['name'].'"';
    $exec_top_persons =  mysql_query($top_persons) or die (mysql_error());
    $top_persons_data = mysql_fetch_array($exec_top_persons) or die (mysql_error());
    
    echo '<p>Du hast zuletzt am <b>'.date("d.m.Y \u\m H:i",strtotime($timestamp_data["timestamp"])).'</b> gemessen. Das ist lange her.</p>';
    echo '<p>Insgesamt hast du hier schon '.$top_persons_data['COUNT(name)'].' mal gemessen.</p>';
    echo '<p>Dein Blutdruck liegt im Durchschnitt bei '.round($data['AVG(sys)']).'/'.round($data['AVG(dia)']).'.';
    
    echo '<p><div id="chart_div"></div></p>'; 
    
    $history_query = 'SELECT * FROM blut WHERE name="'.$_GET['name'].'" ORDER BY id DESC';
    $history_read = mysql_query($history_query) or die (mysql_error());
    /*
    echo '<table><tr><td>Dia</td><td>Sys</td><td>Zeit</td></tr>';
    while ($row = mysql_fetch_assoc($history_read)) {
    echo '<tr>';	
    echo '<td>'.$row["dia"].'</td>';
    echo '<td>'.$row["sys"].'</td>';
    echo '<td>'.date("H:i - d.m.Y",strtotime($row["timestamp"])).'</td>';
	echo '</tr>';
    }
    
    echo '</table>';
 */
	echo '<hr />'; 	
 	
    echo '<h2>3. Sag mir deine Messwerte</h2>';
	echo '<p>Wie ist dein Blutdruck heute? Trag die Zahlen bitte hier unten ein.</p>';	
	echo '<div align="center"><form action="index.php" method="post">
<input type="text" size="3" maxlenght="3" name="sys" /> Sys in mm Hg <br />
<input type="text" size="3" maxlenght="3" name="dia" /> Dia in mm Hg <br />
<input type="hidden" name="name" value="'.$_GET['name'].'" />
	<div class="important">Hast du auch wirklich alles richtig eingegeben?</div><p> Wenn ja, dann drücke auf <i>"weiter"</i>.</strong></p> <br />
<input type="button" class="button" value="Zurück" onClick="history.back()">
<input type="submit" value="Weiter" class="button" />
</form></div>';
}

# Dritter Schritt: Fertig. Name und Blutdruck anzeigen.
if ($_POST['name']!='' and $_POST['dia']!='' and $_POST['sys']!='' and $_GET['page']=='') {
	echo '<h2>4. Danki!</h2>';
	echo '<p>Vielen Dank '.$_POST['name'].'! Dein Blutdruck ist '.$_POST['sys'].'/'.$_POST['dia'].'</p>';
	echo ' <p><b>Du kannst die App jetzt beenden.</b></p>';
	
	$write_query = 'INSERT INTO blut (sys, dia, name) VALUES ('.$_POST['sys'].', "'.$_POST['dia'].'", "'.$_POST['name'].'");';
	$exec_write = mysql_query($write_query) or die(mysql_error());
    echo ' Alles paletti!';
 
}



if($_GET['page']=='info') {
	$read_query = 'SELECT AVG(dia), AVG(sys) FROM blut';
    $exec_read = mysql_query($read_query) or die(mysql_error());
    $data = mysql_fetch_array($exec_read) or die(mysql_error());
    echo '<p>Durchschnitt (Dia in mm Hg): '.$data['AVG(dia)'].'</p>';
    echo '<p>Durchschnitt (Sys in mm Hg): '.$data['AVG(sys)'].'</p>';
	include ('info.php');
}

if($_GET['page']=='statistics') {
	$read_query = 'SELECT STDDEV(dia), AVG(dia), STDDEV(sys), AVG(sys), COUNT(*) FROM blut';
    $exec_read = mysql_query($read_query) or die(mysql_error());
    $data = mysql_fetch_array($exec_read) or die(mysql_error());
    
    $toplist_dia_query = 'SELECT * FROM blut ORDER BY dia DESC LIMIT 5';
    $exec_toplist_dia = mysql_query($toplist_dia_query) or die (mysql_error());
    
    $toplist_sys_query = 'SELECT * FROM blut ORDER BY sys DESC LIMIT 5';
    $exec_toplist_sys = mysql_query($toplist_sys_query) or die (mysql_error());
        
    $se_dia = $data['STDDEV(dia)']/sqrt($data['COUNT(*)']);
    $ci_dia_high = $data['AVG(dia)']+1.96*$se_dia;
    $ci_dia_low = $data['AVG(dia)']-1.96*$se_dia;
    
    $ci_dia_mean = ($ci_dia_high+$ci_dia_low)/2;
    
    $se_sys = $data['STDDEV(sys)']/sqrt($data['COUNT(*)']);
    $ci_sys_high = $data['AVG(sys)']+1.96*$se_sys;
    $ci_sys_low = $data['AVG(sys)']-1.96*$se_sys;
    
    $ci_sys_mean = ($ci_sys_high+$ci_sys_low)/2;
    
    echo '<p>Anzahl der Messwerte: '.$data['COUNT(*)'].'</p>';
       
    echo '<hr /><h2>Diatolisch</h2>'; 
    
    echo '<p>Die fünf höchsten Messwerte:</p>';
    echo '<table><tr><td>Dia</td><td>Name</td><td>Zeit</td></tr>';
    while ($row_dia = mysql_fetch_assoc($exec_toplist_dia)) {
    echo '<tr>';	
    echo '<td>'.$row_dia["dia"].'</td>';
    echo '<td>'.$row_dia["name"].'</td>';
    echo '<td>'.date("H:i - d.m.Y",strtotime($row_dia["timestamp"])).'</td>';
	echo '</tr>';
    }    
    echo '</table>';
    
    echo '<p>Durchschnitt (Dia in mm Hg): '.$data['AVG(dia)'].'</p>'; 
    echo '<p>Standardabweichung (Dia in mm Hg): '.$data['STDDEV(dia)'].'</p>'; 
    echo '<p>Standardfehler: '.$se_dia.' (Dia in mm Hg)<br /></p>';
    echo '<p>95% Konfidenzintervall: ['.$ci_dia_low.'; '.$ci_dia_high.'] (Dia in mm Hg)</p>';
    
    echo '<hr /><h2>Systolisch</h2>';
 
    echo '<p>Die fünf höchsten Messwerte:</p>';
    echo '<table><tr><td>Sys</td><td>Name</td><td>Zeit</td></tr>';
    while ($row_sys = mysql_fetch_assoc($exec_toplist_sys)) {
    echo '<tr>';	
    echo '<td>'.$row_sys["sys"].'</td>';
    echo '<td>'.$row_sys["name"].'</td>';
    echo '<td>'.date("H:i - d.m.Y",strtotime($row_sys["timestamp"])).'</td>';
	echo '</tr>';
    }    
    echo '</table>';
    
    echo '<p>Durchschnitt (Sys in mm Hg): '.$data['AVG(sys)'].'</p>';
    echo '<p>Standardabweichung (Sys in mm Hg): '.$data['STDDEV(sys)'].'</p>';
    echo '<p>Standardfehler: '.$se_sys.' (Sys in mm Hg)<br /></p>';
    echo '<p>95% Konfidenzintervall: ['.$ci_sys_low.'; '.$ci_sys_high.'] (Sys in mm Hg)</p>';
   
}

?>
<hr />
<p><small><a href="?page=statistics">Statistik</a> - <?php echo $versioning; ?></small></p>
</div>
</body>
</html>

<?php mysql_close($link); ?>
