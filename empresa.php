<?php
	session_cache_expire(1);
	session_start();
	if(!isset($_SESSION["userrow"])){		
		header("location: login.php");
		die();
	}
	
	include "query/empresa.php";
	include "utils/valida.php";
	
	switch (get_post_action('save', 'delete', 'load', 'save_login', 'sair')) {
		case 'save':
			
			if(validaCampo($_POST['name'])){
				echo validaCampo($_POST['name']);
				break;
			}
			
			if($_POST['empresas'] == 0){
				cadastra($_POST['name']);
			}
			else{
				edita($_POST['empresas'], $_POST['name']);
			}
			
			break;

		case 'delete':
			deleta($_POST['empresas']);
			break;
			
		case 'load':
			carrega($_POST['empresas']);
			break;
			
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
		
		$output = file_get_contents("forms/empresa.html");
		$output = loadHeader($output);
		$content= getOptions(0);
		$output= str_replace("[OPTIONS]", $content, $output);
		$output= str_replace("[NAME]", "", $output);
		
		echo $output;
	}
	
	function carrega($id){
		$output = "";
		
		$output = file_get_contents("forms/empresa.html");
		$output = loadHeader($output);
		$output = loadFields($output, $id);
		
		echo $output;
	}
	
	function loadHeader($output){
		$header = file_get_contents("forms/header.html");
		$output= str_replace("[HEADER]", $header, $output);	
		$output= str_replace("[AHOME]", "", $output);	
		$output= str_replace("[ADETALHES]", "", $output);	
		$output= str_replace("[ACATEGORIAS]", "", $output);	
		$output= str_replace("[AEMPRESAS]", "class='active'", $output);	
		$output= str_replace("[USUARIOS]", "", $output);		
		$output= str_replace("[LOGGED]", ucfirst($_SESSION["userrow"]), $output);	
		$output= str_replace("[USUARIO]", $_SESSION["userrow"], $output);	
		
		return $output;
	}
?>