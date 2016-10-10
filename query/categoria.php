<?php	
	function cadastra($nome, $limit){
		include "query/conexao.php";
		
		$limit = str_replace(".", "", $limit);
		$limit = str_replace(",", ".", $limit);
	
		
		$sql = "INSERT INTO `categoria` (`desc`, `limite`, `user`, `dsy`)
				VALUES ('". $nome . "', ".$limit.", ".$_SESSION["user_id"].", NOW())";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Cadastrado com sucesso!');window.location.replace('categoria.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível realizar o cadastro!');window.location.replace('categoria.php');</script>";
		}
		
		$conn->close();
	}	
	
	function edita($id, $nome, $limit){
		include "query/conexao.php";
				
		$sql = "UPDATE `categoria` SET `desc` = '".$nome."', 
									   `limite` = ".$limit.", 
									   `user` = ".$_SESSION["user_id"].", 
									   `dsy` = NOW()
                WHERE `id_cat` = ".$id."";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Atualizado com sucesso!');window.location.replace('categoria.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');window.location.replace('categoria.php');</script>";
		}
		
		$conn->close();
	}	
	
	function deleta($id){
		include "query/conexao.php";
		
		$sql = "DELETE FROM `categoria`
				WHERE `id_cat` = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Categoria excluída com sucesso!');window.location.replace('categoria.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível excluir a categoria!');window.location.replace('categoria.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		}
		
		$conn->close();
	}
	
	function getOptions($id){
		include "query/conexao.php";
		$output="<option value='0'>Nova categoria...</option>";
		$result = mysqli_query($conn, "SELECT * FROM categoria");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {
				if($row->id_cat == $id)
					$output.="<option selected='selected' value='".$row->id_cat."'>".$row->desc."</option><br>";					
				else
					$output.="<option value='".$row->id_cat."'>".$row->desc."</option></br>";
			}
		}		
		$conn->close();
		
		return $output;
	}
	
	function loadFields($output, $id){
		include "query/conexao.php";
		
		$content= getOptions($id);		
		$output= str_replace("[OPTIONS]", $content, $output);
		
		$result = mysqli_query($conn, "SELECT * FROM categoria WHERE id_cat='".$id."'");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				$output= str_replace("[NAME]", $row->desc, $output);
				$limit = $row->limite;
				$limit = str_replace(".", ",", $limit);
				$output= str_replace("[LIMIT]", $limit, $output);
			}
			
		}else{
			$output= str_replace("[NAME]", "", $output);
			$output= str_replace("[LIMIT]", "", $output);
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
?>