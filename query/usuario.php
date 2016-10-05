<?php
	
	function cadastra($nome, $sel_emp){
		include "query/conexao.php";
		
		$sql = "INSERT INTO `usuarios` (`nome`, `id_emp`, `user`, `dsy`)
				VALUES ('". $nome . "', ".$sel_emp.", ".$_SESSION["user_id"].", NOW())";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Cadastrado com sucesso!');window.location.replace('usuario.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível realizar o cadastro!');window.location.replace('usuario.php');</script>";
		}
		
		$conn->close();
	}	
	
	function edita($id, $nome, $id_emp){
		include "query/conexao.php";
				
		$sql = "UPDATE `usuarios` SET `nome` = '".$nome."', 
									  `id_emp` = '".$id_emp."',
									  `user` = ".$_SESSION["user_id"].", 
									  `dsy` = NOW()
                WHERE `id_usr` = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Atualizado com sucesso!');window.location.replace('usuario.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');window.location.replace('usuario.php');</script>";
		}
		
		$conn->close();
	}	
	
	function deleta($id){
		include "query/conexao.php";
		
		$sql = "DELETE FROM `usuarios`
				WHERE `id_usr` = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Usuário excluído com sucesso!');window.location.replace('usuario.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível excluir o usuário!');window.location.replace('usuario.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		}
		
		$conn->close();
	}
	
	function getOptions($id){
		include "query/conexao.php";
		$output="<option value='0'>Novo usuário...</option>";
		$result = mysqli_query($conn, "SELECT u.id_usr,
											  u.nome,
											  e.nome as e_nome
									   FROM usuarios u
									   LEFT JOIN empresa e 
											ON e.id_emp=u.id_emp
								       ORDER BY u.id_emp");
									
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {
				if($row->id_usr == $id)
					$output.="<option selected='selected' value='".$row->id_usr."'>".$row->nome." (".$row->e_nome.")</option><br>";					
				else
					$output.="<option value='".$row->id_usr."'>".$row->nome." (".$row->e_nome.")</option><br>";
			}
		}		
		$conn->close();	
		
		return $output;
	}
	
	function getOptions_Emp($id){
		include "query/conexao.php";
		$output="";
		
		if($id!=0)
			$result = mysqli_query($conn, "
				SELECT DISTINCT e.id_emp id_emp, 
								e.nome nome, 
								null id_usr 
				FROM empresa e 
				LEFT JOIN usuarios u 
					ON e.id_emp=u.id_emp 
				WHERE e.id_emp NOT IN (SELECT id_emp FROM usuarios WHERE id_usr = ".$id.") 
				UNION 
				SELECT e.id_emp, 
					   e.nome, 
					   u.id_usr 
				FROM empresa e 
				LEFT JOIN usuarios u 
					ON e.id_emp=u.id_emp 
				WHERE id_usr = ".$id."
				ORDER BY id_emp
			");
		else
			$result = mysqli_query($conn, "SELECT * FROM empresa");			
		
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				if(isset($row->id_usr) && $row->id_usr == $id)
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
		
		$content= getOptions_Emp($id);	
		$output= str_replace("[OPTIONS_EMP]", $content, $output);
		
		$result = mysqli_query($conn, "SELECT * FROM usuarios WHERE id_usr='".$id."'");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				$output= str_replace("[NAME]", $row->nome, $output);
			}			
		}else{
			$output= str_replace("[NAME]", "", $output);
		}
		$conn->close();
		
		return $output;
	}
?>