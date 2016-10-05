<?php	
	session_cache_expire(1);
	session_start();
	if(!isset($_SESSION["userrow"])){		
		header("location: login.php");
		die();
	}
	
	include "query/index.php";
	include "utils/valida.php";
	
	switch (get_post_action('save_login', 'sair')) {
		case 'save_login':
			$uname = $_POST['username'];
			$upass = sha1($_POST['password']);
			
			changePass($uname, $upass, $_SESSION["userrow"]);
						
			break;		
		
		case 'sair':			
			session_destroy();
			setcookie(session_name(), '', time()-86400);
			header("location: login.php");
			die();
			break;
			
		default:
			exibir();
	}
	
	function exibir(){
		$output = "";
			
		$output = file_get_contents("forms/index.html");
		$output = loadHeader($output);
		
		$content = getContent();		
		
		if($content != "")
			$output= str_replace("[CONTENT]", $content, $output);
		else
			$output= str_replace("[CONTENT]", "", $output);		
		
		echo $output;
	}	
	
	function loadHeader($output){
		$header = file_get_contents("forms/header.html");
		$output= str_replace("[HEADER]", $header, $output);	
		$output= str_replace("[AHOME]", "class='active'", $output);	
		$output= str_replace("[ADETALHES]", "", $output);	
		$output= str_replace("[ACATEGORIAS]", "", $output);	
		$output= str_replace("[AEMPRESAS]", "", $output);	
		$output= str_replace("[USUARIOS]", "", $output);		
		$output= str_replace("[LOGGED]", ucfirst($_SESSION["userrow"]), $output);	
		$output= str_replace("[USUARIO]", $_SESSION["userrow"], $output);	
		
		return $output;
	}
?>