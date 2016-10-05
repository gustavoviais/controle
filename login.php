<?php
	session_start();
	include "query/conexao.php";
	include "utils/valida.php";
	
	switch (get_post_action('acessar')) {
		case 'acessar':	

			$uname = mysqli_real_escape_string($conn, $_POST['username']); //against sql injection
			$upass = sha1($_POST['password']);
			$query = "select * from login where username='{$uname}' AND password='{$upass}'";
			$result = mysqli_query($conn, $query);
			
			if($row = mysqli_fetch_object($result)){
				$_SESSION["userrow"] = $row->username;
				$_SESSION["user_id"] = $row->id;
				header("location: index.php");
				die();
			}
			else{
				exibir_erro();
			}
			
			break;		

		default:
			exibir();
	}
	
	function exibir(){
		$output = "";
		
		$output = file_get_contents("forms/login.html");
		$output= str_replace("[ERRO]", "", $output);
		
		echo $output;
	}
	
	function exibir_erro(){
		$output = "";
		
		$output = file_get_contents("forms/login.html");
		$output= str_replace("[ERRO]", "Usuário/Senha não conferem!", $output);
		
		echo $output;
	}
?>