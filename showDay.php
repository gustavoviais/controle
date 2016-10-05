<?php	
  session_start();
	if(!isset($_SESSION["userrow"])){		
		header("location: login.php");
		die();
  }
	
  if(isset($_GET["date"])){
		include "query/showDay.php";
    include "utils/valida.php";

    $output=$_GET["date"];
				
    $content = getDayContent($_GET["date"]);
		
		$output = file_get_contents("forms/showDay.html");	
    
    $output= str_replace("[DATE]", $_GET["date"], $output);	
		$output= str_replace("[CONTENT]", $content, $output);	
		
		$content = getTotal($_GET["date"]);
    $output= str_replace("[TOTAL]", $content, $output);
		
		echo $output;		
  }
	
?>
