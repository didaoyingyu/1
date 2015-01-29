<?php
ob_start();
function redirectTo($link) {
	header("Location: $link");
}
$link = 'index2.php?id=' .
		$_POST['id'].
		'&order='.
		$_POST['order'].
		'&column='.
		$_POST['column'];
	echo $link;
			
redirectTo($link);

?>