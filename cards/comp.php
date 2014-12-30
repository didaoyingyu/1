<?php
ob_start();
error_reporting(E_ERROR | E_PARSE);
$mysql_host = "localhost";
$mysql_database = "wuyiwul1_aaalivedat";
$mysql_user = "root";
$mysql_password = "wEYTvSw4TyLMTxnE";
$con=mysql_connect($mysql_host,$mysql_user,$mysql_password);
if(!$con)
{
	die("failed connect".mysql_error());
}
$db_select=mysql_select_db($mysql_database);
if(!$db_select)
{
		die(" failed user".mysql_error());
}
mysql_set_charset("utf8");
?>