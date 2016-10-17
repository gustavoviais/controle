<?php
	session_cache_expire(1);
	session_start();
	if(!isset($_SESSION["userrow"])){		
		header("location: login.php");
		die();
	}
	
	include "query/mdetalhes.php";
	include "utils/valida.php";	
	
	if(isset($_GET['id']) && $_GET['id']!=""){
		if(isset($_GET['action']) && $_GET['action']!=""&& $_GET['action']==0){
			deleta($_GET['id']);
		}else{
			if($_GET['id'] == (-1))
				exibir();		
			else
				carrega($_GET['id']);
		}
	}else{
	
		switch (get_post_action('save', 'delete', 'load', 'consultar', 'save_login', 'sair')) {
			case 'save':
				$valor = str_replace(".", "", $_POST['value']);
				$valor = str_replace(",", ".", $valor);
				
				$valor_total = str_replace(".", "", $_POST['valor_total']);
				$valor_total = str_replace(",", ".", $valor_total);
				
				if(isset($_POST['novoDetalhe']) && ($_POST['novoDetalhe']>0)){
					$novoDetalhe=str_replace(".", "", $_POST['novoDetalhe']);
					$novoDetalhe = str_replace(",", ".", $novoDetalhe);
				}else
					$novoDetalhe="";
				
				$reembolso=0;
				if(isset($_POST['reembolso']))
					$reembolso = true;
				
				if($_POST['sel'] == 0){
					cadastra($_POST['sel'], $_POST['nf'], $_POST['di'], $_POST['df'], $_POST['sel_usr'], $_POST['sel_emp'], $_POST['sel_cat'], 
							 $valor, $valor_total, $_POST['local'], $_POST['comment'], $reembolso, $novoDetalhe);
				}
				else{
					edita($_POST['sel'], $_POST['nf'], $_POST['di'], $_POST['df'], $_POST['sel_usr'], $_POST['sel_emp'], $_POST['sel_cat'], 
						  $valor, $valor_total, $_POST['local'], $_POST['comment'], $reembolso, $novoDetalhe);
				}
				
				break;

			case 'delete':
				if(!isset($_POST['sel']))
					deleta($_POST['delete']);
				else
					deleta($_POST['sel']);
				break;
				
			case 'load':
				if(!isset($_POST['sel']))
					carrega($_POST['load']);
				else if($_POST['sel']==0)
					exibir();
				else if($_POST['sel']==(-1))
					consulta();
				else
					carrega($_POST['sel']);
				break;
				
			case 'consultar':				
				gerenciaSessao();	
				
				echo "<script type='text/javascript'>window.location.replace('showAll.php');</script>";
				//echo "<script type='text/javascript'>setTimeout(function(){window.location.replace('detalhes.php')},3000);</script>";
				
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
	}
	
	function exibir(){
		$output = "";
		
		$output = file_get_contents("forms/mdetalhes.html");
		$output = loadHeader($output);
		$content= getOptions(0);
		$output= str_replace("[OPTIONS]", $content, $output);
		
		$content= getNota(0);
		$output= str_replace("[NF]", $content, $output);
		
		$content= getOptions_Usr(0);
		$output= str_replace("[OPTIONS_USR]", $content, $output);
		
		$content= getOptions_Emp(0);
		$output= str_replace("[OPTIONS_EMP]", $content, $output);
		
		$content= getOptions_Cat(0);
		$output= str_replace("[OPTIONS_CAT]", $content, $output);
		
		if(isset($_POST["new"])){
			$output= str_replace("[DI]", $_POST["new"], $output);
			$output= str_replace("[DF]", $_POST["new"], $output);
		}
		else{
			$output= str_replace("[DI]", "", $output);
			$output= str_replace("[DF]", "", $output);
		}		
		
		$output= str_replace("[VALUE]", "", $output);
		$output= str_replace("[VALOR_TOTAL]", "", $output);
		$output= str_replace("[REEMBOLSO]", "", $output);
		$output= str_replace("[LOCAL]", "", $output);
		$output= str_replace("[OBS]", "", $output);
		$output= str_replace("[VALOR_OP]", "", $output);		
		$output= str_replace("[CONSULTAR]", "display: none;", $output);
		$output= str_replace("[EXIBIR]", "", $output);
		
		echo $output;
	}
	
	function carrega($id){
		$output = "";
		
		$output = file_get_contents("forms/mdetalhes.html");
		$output = loadHeader($output);
		$output = loadFields($output, $id);
		
		$output= str_replace("[CONSULTAR]", "display:none;", $output);
		$output= str_replace("[EXIBIR]", "", $output);
		
		echo $output;
	}
	
	function consulta(){
		$output = "";
		
		$output = file_get_contents("forms/mdetalhes.html");
		$output = loadHeader($output);
		$content= getOptions(-1);
		$output= str_replace("[OPTIONS]", $content, $output);
		
		$content= getNota(-1);
		$output= str_replace("[NF]", $content, $output);
		
		$content= getOptions_Usr(-1);
		$output= str_replace("[OPTIONS_USR]", $content, $output);
		
		$content= getOptions_Emp(-1);
		$output= str_replace("[OPTIONS_EMP]", $content, $output);
		
		$content= getOptions_Cat(-1);
		$output= str_replace("[OPTIONS_CAT]", $content, $output);
		
		$content= getNota(-1);
		$output= str_replace("[NF]", $content, $output);
		
		$output= str_replace("[DI]", "", $output);
		$output= str_replace("[DF]", "", $output);
		$output= str_replace("[VALUE]", "", $output);
		$output= str_replace("[VALOR_TOTAL]", "", $output);
		$output= str_replace("[REEMBOLSO]", "", $output);
		$output= str_replace("[LOCAL]", "", $output);
		$output= str_replace("[OBS]", "", $output);
		$output= str_replace("[CONSULTAR]", "", $output);
		$output= str_replace("[VALOR_OP]", " Valor por usuário/dia", $output);
		$output= str_replace("[EXIBIR]", "display: none;", $output);
		
		echo $output;
	}
	
	function loadHeader($output){
		$header = file_get_contents("forms/header.html");
		$output= str_replace("[HEADER]", $header, $output);	
		$output= str_replace("[AHOME]", "", $output);	
		$output= str_replace("[ADETALHES]", "class='active'", $output);	
		$output= str_replace("[ACATEGORIAS]", "", $output);	
		$output= str_replace("[AEMPRESAS]", "", $output);	
		$output= str_replace("[USUARIOS]", "", $output);		
		$output= str_replace("[LOGGED]", ucfirst($_SESSION["userrow"]), $output);	
		$output= str_replace("[USUARIO]", $_SESSION["userrow"], $output);	
		
		return $output;
	}
	
	function gerenciaSessao(){		
		if(isset($_POST['valor_op'])){
			$_SESSION['valor_op'] = $_POST['valor_op'];			
			$_SESSION['sel_op'] = $_POST['sel_op'];
		}
		
		if($_POST['nf'] != 0)
			$_SESSION['nf'] = " AND d.id_nf=".$_POST['nf'];
		else
			$_SESSION['nf'] = "";
		
		if($_POST['di']!="")
			$_SESSION['di'] = " AND d.data_entrada>='".$_POST['di']."'";
		else
			$_SESSION['di'] = "";
		
		if($_POST['df']!="")
			$_SESSION['df'] = " AND d.data_saida<='".$_POST['df']."'";
		else
			$_SESSION['df'] = "";
		
		if($_POST['sel_usr'][0] != 0)
			$_SESSION['sel_usr'] = " AND ud.id_usr IN (".implode(',',array_map('intval', $_POST['sel_usr'])).")";
		else
			$_SESSION['sel_usr'] = "";
		
		if($_POST['sel_emp'] != 0)
			$_SESSION['sel_emp'] = " AND d.id_emp=".$_POST['sel_emp'];
		else
			$_SESSION['sel_emp'] = "";
		
		if($_POST['sel_cat'] != 0)
			$_SESSION['sel_cat'] = " AND d.id_cat=".$_POST['sel_cat'];
		else
			$_SESSION['sel_cat'] = "";				
		
		if($_POST['value'] != ""){			
			$valor = str_replace(".", "", $_POST['value']);
			$valor = str_replace(",", ".", $valor);
			$_SESSION['valor'] = " AND valor".$_POST['sel_op'].$valor;
		}else{
			$_SESSION['valor'] = "";
		}
		
		if($_POST['local'] != "")	
			$_SESSION['local'] = " AND local LIKE '%".$_POST['local']."%'";
		else
			$_SESSION['local'] = "";
		
		if($_POST['comment'] != "")				
			$_SESSION['obs'] = " AND obs LIKE '%".$_POST['comment']."%'";
		else
			$_SESSION['obs'] = "";			
	}
?>
