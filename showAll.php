<?php	
	session_cache_expire(1);
	session_start();
	if(!isset($_SESSION["userrow"])){		
		header("location: login.php");
		die();
	}
	//error_reporting(0);
	include "query/showAll.php";
	include "utils/valida.php";
	
	switch (get_post_action('consultar', 'save_login', 'sair')) {
		case 'consultar':	
			exibir();	
			
			//echo "<script type='text/javascript'>window.location.replace('showAll.php');</script>";
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
		
	function exibir(){
		gerenciaSessao();
		
		if(isset($_GET["orderby"]))
			$orderby=$_GET["orderby"];
		else
			$orderby="numero_nf desc, data_entrada";
		
		$content = getAllContent($orderby);
		
		$output = file_get_contents("forms/showAll.html");	
		$output = loadHeader($output);
		$output= str_replace("[CONTENT]", $content, $output);			
		
		$content = getTotalParcial();
		$output= str_replace("[TOTAL_PARCIAL]", $content, $output);	
		
		if(isset($_SESSION['numero_registros']))
			$output= str_replace("[NUMERO_REGISTROS]", $_SESSION['numero_registros'], $output);
		else
			$output= str_replace("[NUMERO_REGISTROS]", "", $output);
		
		$content = getTotalNota();
		$output= str_replace("[TOTAL_NOTA]", $content, $output);
		
		$content = getTotal();
		$output= str_replace("[TOTAL]", $content, $output);			
		
		$output= loadFields($output);
		
		echo $output;		
	}
	
	function loadFields($output){
		
		if(isset($_POST['id']))
			$output= str_replace("[ID]", $_POST['id'], $output);
		else
			$output= str_replace("[ID]", "", $output);
		
		if(isset($_POST['di']))
			$output= str_replace("[DI]", $_POST['di'], $output);
		else
			$output= str_replace("[DI]", "", $output);
		
		if(isset($_POST['df']))
			$output= str_replace("[DF]", $_POST['df'], $output);
		else
			$output= str_replace("[DF]", "", $output);
		
		if(isset($_POST['nf']))
			$output= str_replace("[NF]", $_POST['nf'], $output);
		else
			$output= str_replace("[NF]", "", $output);
		
		if(isset($_POST['fornecedor']))
			$output= str_replace("[FORNECEDOR]", $_POST['fornecedor'], $output);
		else
			$output= str_replace("[FORNECEDOR]", "", $output);
		
		if(isset($_POST['total_nf']))
			$output= str_replace("[TOTAL_NF]", $_POST['total_nf'], $output);
		else
			$output= str_replace("[TOTAL_NF]", "", $output);
		
		if(isset($_POST['user']))
			$output= str_replace("[USER]", $_POST['user'], $output);
		else
			$output= str_replace("[USER]", "", $output);
		
		if(isset($_POST['reembolso']))
			$output= str_replace("[REEMBOLSO]", $_POST['reembolso'], $output);
		else
			$output= str_replace("[REEMBOLSO]", "", $output);
		
		if(isset($_POST['cat']))
			$output= str_replace("[CAT]", $_POST['cat'], $output);
		else
			$output= str_replace("[CAT]", "", $output);
		
		if(isset($_POST['value']))
			$output= str_replace("[VALUE]", $_POST['value'], $output);
		else
			$output= str_replace("[VALUE]", "", $output);
		
		if(isset($_POST['obs']))
			$output= str_replace("[OBS]", $_POST['obs'], $output);
		else
			$output= str_replace("[OBS]", "", $output);
		
		return $output;
	}
	
	function loadHeader($output){
		$header = file_get_contents("forms/header.html");
		$output= str_replace("[HEADER]", $header, $output);	
		$output= str_replace("[AHOME]", "", $output);	
		$output= str_replace("[ADETALHES]", "", $output);	
		$output= str_replace("[ACATEGORIAS]", "", $output);	
		$output= str_replace("[AEMPRESAS]", "", $output);	
		$output= str_replace("[USUARIOS]", "", $output);		
		$output= str_replace("[CONSULTA]", "class='active'", $output);		
		$output= str_replace("[LOGGED]", ucfirst($_SESSION["userrow"]), $output);	
		$output= str_replace("[USUARIO]", $_SESSION["userrow"], $output);	
		
		return $output;
	}
	
	function gerenciaSessao(){		
		if((isset($_POST['id']))&&($_POST['id'] != "")&&($_POST['id'] != "*"))
			$_SESSION['id'] = " AND d.id_details IN (".$_POST['id'].")";
		else
			$_SESSION['id'] = "";		
		
		if((isset($_POST['di']))&&($_POST['di']!="")&&($_POST['di'] != "*")){
			$di_aux = explode("/", $_POST['di']);
			$di = $di_aux[2]."-".$di_aux[1]."-".$di_aux[0];			
			$_SESSION['di'] = " AND d.data_entrada>='".$di."'";			
		}else
			$_SESSION['di'] = "";
		
		if((isset($_POST['df']))&&($_POST['df']!="")&&($_POST['df'] != "*")){
			$df_aux = explode("/", $_POST['df']);
			$df = $df_aux[2]."-".$df_aux[1]."-".$df_aux[0];
			
			$_SESSION['df'] = " AND d.data_saida<='".$df."'";
		}else
			$_SESSION['df'] = "";
		
		if((isset($_POST['nf']))&&($_POST['nf'] != "")&&($_POST['nf'] != "*"))
			$_SESSION['nf'] = " AND nf.numero_nf IN (".$_POST['nf'].")";
		else
			$_SESSION['nf'] = "";
		
		if((isset($_POST['fornecedor']))&&($_POST['fornecedor'] != "")&&($_POST['fornecedor'] != "*"))
			$_SESSION['fornecedor'] = " AND f.nome_fornecedor LIKE '%".$_POST['fornecedor']."%'";
		else
			$_SESSION['fornecedor'] = "";
		
		if((isset($_POST['total_nf']))&&($_POST['total_nf'] != "")&&($_POST['total_nf'] != "*")){
			$total_nf = str_replace(".", "", $_POST['total_nf']);
			$total_nf = str_replace(",", ".", $total_nf);
			
			if((strpos($total_nf, '=') !== FALSE) || (strpos($total_nf, '>') !== FALSE) || (strpos($total_nf, '<') !== FALSE))				
				$_SESSION['total_nf'] = " AND nf.total_nf ".$total_nf."";
			else
				$_SESSION['total_nf'] = " AND nf.total_nf = ".$total_nf."";
		}else
			$_SESSION['total_nf'] = "";
		
		if((isset($_POST['user']))&&($_POST['user'] != "")&&($_POST['user'] != "*"))
			$_SESSION['user'] = " AND u.nome LIKE '%".$_POST['user']."%'";
		else
			$_SESSION['user'] = "";			
		
		if((isset($_POST['reembolso']))&&($_POST['reembolso'] != "")&&($_POST['reembolso'] != "*")){
			if($_POST['reembolso']=="s" || $_POST['reembolso']=="si" || $_POST['reembolso']=="sim" || $_POST['reembolso']=="S" || $_POST['reembolso']=="SIM" || $_POST['reembolso']=="Sim"  )
				$reembolso = 1;
			else
				$reembolso = 0;
			$_SESSION['reembolso'] = " AND d.reembolso = ".$reembolso."";
		}else
			$_SESSION['reembolso'] = "";	

		if((isset($_POST['cat']))&&($_POST['cat'] != "")&&($_POST['cat'] != "*"))
			$_SESSION['cat'] = " AND c.desc LIKE '%".$_POST['cat']."%'";
		else
			$_SESSION['cat'] = "";
		
		if((isset($_POST['value']))&&($_POST['value'] != "")&&($_POST['value'] != "*")){			
			$valor = str_replace(".", "", $_POST['value']);
			$valor = str_replace(",", ".", $valor);
			
			if((strpos($valor, '=') !== FALSE) || (strpos($valor, '>') !== FALSE) || (strpos($valor, '<') !== FALSE))				
				$_SESSION['valor'] = " AND d.valor ".$valor."";
			else
				$_SESSION['valor'] = " AND d.valor = ".$valor."";
		}else{
			$_SESSION['valor'] = "";
		}
		
		if((isset($_POST['obs']))&&($_POST['obs'] != "")&&($_POST['obs'] != "*"))
			$_SESSION['obs'] = " AND obs LIKE '%".$_POST['obs']."%'";
		else
			$_SESSION['obs'] = "";			
	}
	
	
?>
