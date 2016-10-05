<?php
	session_cache_expire(1);
	session_start();
	if(!isset($_SESSION["userrow"])){		
		header("location: login.php");
		die();
	}
	
	include "query/nota_fiscal.php";
	include "utils/valida.php";
	
	switch (get_post_action('save', 'delete', 'load', 'save_login', 'sair')) {
		case 'save':
		
			$total_nf = str_replace(".", "", $_POST['total_nf']);
			$total_nf = str_replace(",", ".", $total_nf);
			$total_nf = floatval($total_nf);
			
			if($_POST['sel'] == 0){
				cadastra($_POST['numero_nf'], $_POST['dnf'], $total_nf, $_POST['sel2']);
			}
			else{
				edita($_POST['sel'], $_POST['numero_nf'], $_POST['dnf'], $total_nf, $_POST['sel2']);
			}
			
			break;

		case 'delete':
			deleta($_POST['sel']);
			break;
			
		case 'load':
			carrega($_POST['sel']);
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
		$output = file_get_contents("forms/nota_fiscal.html");	
		$output = loadHeader($output);
		
		$content= getOptions(0);
		$output= str_replace("[OPTIONS]", $content, $output);
		$output= str_replace("[NUMERO_NF]", "", $output);
		$content = getFornecedor(0);
		$output= str_replace("[FORNECEDOR]", $content, $output);
		$output= str_replace("[TOTAL_NF]", "", $output);	
		$output= str_replace("[DNF]", "", $output);	
		
		echo $output;
	}
	
	function carrega($id){
		$output = "";
		
		$output = file_get_contents("forms/nota_fiscal.html");
		$output = loadHeader($output);
		$output = loadFields($output, $id);
		
		echo $output;
	}
	
	function loadHeader($output){
		$header = file_get_contents("forms/header.html");
		$output= str_replace("[HEADER]", $header, $output);	
		$output= str_replace("[AHOME]", "", $output);	
		$output= str_replace("[ADETALHES]", "", $output);	
		$output= str_replace("[ANF]", "class='active'", $output);	
		$output= str_replace("[ACATEGORIAS]", "", $output);	
		$output= str_replace("[AEMPRESAS]", "", $output);	
		$output= str_replace("[USUARIOS]", "", $output);		
		$output= str_replace("[LOGGED]", ucfirst($_SESSION["userrow"]), $output);	
		$output= str_replace("[USUARIO]", $_SESSION["userrow"], $output);	
		
		return $output;
	}
?>