<?php	
	session_cache_expire(1);
	session_start();
	if(!isset($_SESSION["userrow"])){		
		header("location: login.php");
		die();
	}
	
	if(isset($_GET["id"])){
		include "query/showDetail.php";
		include "utils/valida.php";
		
		$output = file_get_contents("forms/showDetail.html");	
		$output= str_replace("[LOAD]", $_GET["id"], $output);	
		
		$output = carrega($_GET["id"], $output);
		
		echo $output;		
	}
	
?>