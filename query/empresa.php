<?php	
	
	function cadastra($nome){
		include "query/conexao.php";
		
		$sql = "INSERT INTO empresa (nome, `user`, `dsy`)
				VALUES ('". $nome . "', ".$_SESSION["user_id"].", NOW())";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Cadastrado com sucesso!');window.location.replace('empresa.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível realizar o cadastro!');window.location.replace('empresa.php');</script>";
		}
		
		$conn->close();
	}	
	
	function edita($id, $nome){
		include "query/conexao.php";
		
		$sql = "UPDATE empresa SET nome = '".$nome."', 
								  `user` = ".$_SESSION["user_id"].", 
								  `dsy` = NOW() 
                WHERE id_emp = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Atualizado com sucesso!');window.location.replace('empresa.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');window.location.replace('empresa.php');</script>";
		}
		
		$conn->close();
	}	
	
	function deleta($id){
		include "query/conexao.php";
		
		$sql = "DELETE FROM empresa
				WHERE id_emp = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Empresa excluída com sucesso!');window.location.replace('empresa.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível excluir a empresa!');window.location.replace('empresa.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		}
		
		$conn->close();
	}
	
	function getOptions($id){
		include "query/conexao.php";
		$output="<option value='0'>Nova empresa...</option>";
		$result = mysqli_query($conn, "SELECT * FROM empresa");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {
				if($row->id_emp == $id)
					$output.="<option selected='selected' value='".$row->id_emp."'>".$row->nome."</option><br>";					
				else
					$output.="<option value='".$row->id_emp."'>".$row->nome."</option></br>";
			}			
		}	
		$conn->close();		
		
		return $output;
	}
	
	function changePass($uname, $upass, $oldname){
		include "query/conexao.php";
		
		$uname = mysqli_real_escape_string($conn, $uname);
		$query = "update login set username='{$uname}', password='{$upass}' where username like '{$oldname}'";
		$result = mysqli_query($conn, $query);
		
		$conn->close();
		
		if ($result === TRUE) {
			session_destroy();
			setcookie(session_name(), '', time()-86400);
			echo "<script type='text/javascript'>window.alert('Atualizado com sucesso!');window.location.replace('login.php');</script>";			
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');</script>";
			header("location: login.php");
			die();
		}
	}
	
	function loadFields($output, $id){
		include "query/conexao.php";
		
		$content= getOptions($id);		
		$output= str_replace("[OPTIONS]", $content, $output);
		
		$result = mysqli_query($conn, "SELECT * FROM empresa WHERE id_emp='".$id."'");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				$output= str_replace("[NAME]", $row->nome, $output);
			}
		}
		$conn->close();	
		
		return $output;
	}
?>