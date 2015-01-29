<?php
session_unset(); 
error_reporting(E_ERROR | E_PARSE);
session_start();
?>
  <html>
  <head>
  <title>user login</title>
  </head>
  <body>
 <?php
require_once('comp.php');
$p = "select * from pwd ";
$p2= mysql_query($p,$con);
global $h;
global $i;
$i=0;
$_SESSION['o'] = 0;
$_SESSION['p'] = 0;
while($p3 = mysql_fetch_array($p2))
{
$_SESSION['o'] = 1;
$i = $i + 1;
if($p3['pass'] == null)
{
    
	header('Location: index2.php');
    exit();
	break;
}

else
{
$h = $p3['pass'];

break;
}
}

if(isset($_POST['pas']))
{
  if($h == ($_POST['pas']))
  {  $_SESSION['p'] = 1;
    header('Location: index2.php');
	exit();
   }
   else
   {
   echo "pls put password correct";
   }	
} 
if($i == 0)
{
  header('Location: index2.php');
  exit();}
 ?>

<form id="form1" method="post" action="index.php">
 <p>enter password:</p>
 <input type ="password" name="pas"><br />
 <input type="submit" value="submit">
  <br />
  </form>
  </body>
  </html>
  