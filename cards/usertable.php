<?php 
require_once('comp.php');
$p = "select * from pwd ";
$p2= mysql_query($p,$con);
while($p3 = mysql_fetch_array($p2))
{


if( $_SESSION['p'] != 1)
{   header('Location: index.php');
    exit();
}} 

	$queryA = 'select * from users where `id` = ' . "'"  . $id . "'";
	if(!$queryA) {
		die(mysql_error());
	}
 	$userA = mysql_query($queryA);
	$user_arrayA = mysql_fetch_array($userA);
?>
<h2 style="text-align:center"> Username : <b><?php echo $user_arrayA['username']; ?></b></h2>
<h2 style="text-align:center"> Email : <a href="mailto:<?php echo $user_arrayA['email']; ?>"><?php echo $user_arrayA['email']; ?></a></h2><br>
<?php
if(isset($_GET['column']) and $_GET['column'] != '' and isset($_GET['order']) and $_GET['order'] != '' ) 
	echo '<b style="color: red;font-size: 1.4em"> Column '. $_GET['column'] .' is sorted ' . $_GET['order'] . '</b><Br>';
	
?>
<form action="generator.php" method="post" >
	Column : <select name="column">  
					<option value="last_shown">last_show</option>
					<option value="last_date">last_date</option>
					<option value="rank">rank</option>
					<option value="card_id">card Id</option>
					<option value="utp">utp</option>
					<option value="play_count">play_count</option>
					<option value="history">history</option>
					<option value="test_history">test_history</option>
			</select><br><br>
	Order  : <select name="order">
				<option value="ASC">ASC</option><option value="DESC">DESC</option>
			</select><br>
			<br>
			<input type="hidden" name="id" value="<?php echo $id; ?>"/>
			<input type="submit" value="Sort!" name="submitted" />
</form>
<br>
<table border=1 style="margin:auto;float:none;">
	<colgroup>
		<col width="350px">
		<col width="350px">
		<col width="75px">
		<col width="75px">
		<col width="75px">
		<col width="50px">
		
	</colgroup>
	<style> 
		b {
			font-size: 1.2em;
			padding: 5px;
			font-family : 'Agency FB';
			letter-spacing:1px;
		}
	</style>
	<tr>
		<td>
			<b>Question</b>
		</td>
		<td>
			<b>Answer</b>
		</td>
		<td>
			<b>Deck Name</b>
		</td>
		<td>
			<b>Last shown (yyyy/mm/dd)</b>
		</td>
		<td>
			<b>Last Date (yyyy/mm/dd)</b>
		</td>
		<td>
			<b>Elapsed time (Days)</b>
		</td>
		<td>
			<b>History</b>
		</td>
		<td>
			<b>Test History</b>
		</td>
		<td>
			<b>Rank</b>
		</td>
		<td>
			<b>Play Count</b>
		</td>
		<td>
			<b>UTP</b>
		</td>
		<td>
			<b>Card ID</b>
		</td>
	</tr>

<?php 
global $i;
if(!isset($_GET['next'])) {
	$i = 0;
}
else {
	$i = $_GET['next'];
}
$append = " , 50";
$showall = false;
if(isset($_GET['showall'])) {
	$append = "";
	$showall = true;
}
$query1 = 'select * from `user_card` where `user_id` ' . "= '{$id}' ";

if(isset($_GET['column']) and $_GET['column'] != '' and isset($_GET['order']) and $_GET['order'] != '' ) {
	$query1 .= ' ORDER BY ' .  $_GET['column']. ' ' . $_GET['order'] . ' '; 
}

if($showall == false) {
$query1 .= " LIMIT " . $i . $append; 
}
$results = mysql_query($query1);
if(!$results) {
	die(mysql_error());
	die();
}
while($result_array = mysql_fetch_array($results)) {
	echo "<tr>";
	$card_id = $result_array['card_id'];
	$query2 = 'select * from `card` where `card_id` = ' . 
				"'" . 
				$result_array['card_id'] 
				. "'";
	$sub_results = mysql_query($query2);
	
	$query_lastshown = "select * from `user_card` where `card_id` = '" . $card_id . "' "  . "and  `user_id` = '" . $id . "'" ;
	$lastshow = mysql_query($query_lastshown);
	if(!$query_lastshown) {
	mysql_error();die();
	}	
	$lastshow_array = mysql_fetch_array($lastshow);
	
	if(!$sub_results) {
		die(mysql_error());
	}
	$query3 = "select `deck_name` from `card_deck` where `deck_id` = " . "'" . $lastshow_array['deck_id'] ."' ORDER BY `card_deck`.`deck_name` DESC";
	
	$deck_name = mysql_query($query3);
	if(!$query3) {
		die(mysql_error());
	}
	$deck_name_array = mysql_fetch_array($deck_name);
	$sub_results_array = mysql_fetch_array($sub_results);
		
	echo "<td>" . $sub_results_array['question'] . "</td>";
	
	echo "<td>" . $sub_results_array['answer'] . " </td>";
	
	echo "<td>" . $deck_name_array['deck_name'] . '</td>';
	
	echo "<td>". "<script>var d = new Date(". $lastshow_array['last_shown'].");
				var year = d.getFullYear();
				var date = d.getDate(); 
				var month = d.getMonth();
				month = month + 1;
				var time = d.getTime();
				var hour = d.getHours();
				var min = d.getMinutes();
				var sec = d.getSeconds();
				 document.write(year + '/' + month +'/' + date + ' Time' + hour + ':' + min + ':' + sec); </script>" .'</td>';
	
	echo "<td>" . "<script>var d = new Date(". $lastshow_array['last_date'].");
				var year = d.getFullYear();
				var date = d.getDate(); 
				var month = d.getMonth();
				month = month + 1;
				var time = d.getTime();
				var hour = d.getHours();
				var min = d.getMinutes();
				var sec = d.getSeconds();
				 document.write(year + '/' + month +'/' + date + ' Time' + hour + ':' + min + ':' + sec); </script>" .'</td>';
	$now = time();
	$then = strtotime( $lastshow_array['utp']);
	$delta_time = $now - $then;
	$days = floor($delta_time /(60*60*24) );
	echo "<td>".$days ."</td>";	
	echo "<td>".$lastshow_array['history'] ."</td>";
	echo "<td>".$lastshow_array['test_history'] ."</td>";
	echo "<td>".$lastshow_array['rank'] ."</td>";
	echo "<td>".$lastshow_array['play_count'] ."</td>";
	echo "<td>".$lastshow_array['utp'] ."</td>";
	echo "<td>".$lastshow_array['card_id'] ."</td>";
	echo '</tr>';
}
?>
</table>
<?php 
	$i = $i + 50;
	$next = "index2.php?id=" . $id . "&next=" . $i;
?>
<br/>
<?php
if($showall == false) 
{
 echo '<a href="';
 echo $next;
 echo '">NEXT';
  echo "</a><p>&nbsp";
  echo "</p>";
  
 }
 ?>
<a href="<?php echo $next . '&showall'; ?>">SHOW ALL</a><p>&nbsp;</p>