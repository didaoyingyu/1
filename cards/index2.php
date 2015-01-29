<?php
session_start();
require_once('comp.php');
  $p = "select * from pwd ";
  $p2= mysql_query($p,$con);
  while($p3 = mysql_fetch_array($p2))
{ 
   $i = $_SESSION['p'];
   if($p3['pass'] == null)
  {
  
	break;
  }
  else if($_SESSION['p'] == 1)
  {
	break;
	}
	else{
       header("Location: index.php");
  }
 }
  ?>
<html>
<head>
<title>
	users
</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta content="UTF-8" http-equiv="encoding" />
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	  <style>
		a , a:hover , a:visited {
			color : blue;
		}
		td {
			padding-left : 5px;
		}
	  </style>
</head>
<?php 
	$query = 'select * from users';
 	$user = mysql_query($query,$con);
?>
<body style="text-align:center;font-family:Calibri,Serif">
<?php
	if(($_SESSION['o']) == 1)
	{ 
      echo "<a href='index.php'>";
	  echo '<button type="submit" style="float: right;">';
	  echo "logout";
	  echo "</button>";
	  echo "</a>";
	 }
	 
	 
	if(!isset($_GET['id']) or $_GET['id'] == "" ) {
		echo "<h1> List of Users</h1>";
		while($user_array = mysql_fetch_array($user)) 
			echo '<a href="index2.php?id='. $user_array['id'] .'">'. $user_array['username'] . '</a><br />';
		
	}
	else {
		$id = $_GET['id'];
		require_once('usertable.php');
	}
?>
</body>
</html>