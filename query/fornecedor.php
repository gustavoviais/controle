<?php	
	
	function cadastra($nome){
		include "query/conexao.php";
		
		$sql = "INSERT INTO fornecedor (nome_fornecedor)
				VALUES ('". $nome . "')";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Cadastrado com sucesso!');window.location.replace('fornecedor.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível realizar o cadastro!');window.location.replace('fornecedor.php');</script>";
		}
		
		$conn->close();
	}	
	
	function edita($id, $nome){
		include "query/conexao.php";
		
		$sql = "UPDATE fornecedor SET nome_fornecedor = '".$nome."'
                WHERE id_fornecedor = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Atualizado com sucesso!');window.location.replace('fornecedor.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');window.location.replace('fornecedor.php');</script>";
		}
		
		$conn->close();
	}	
	
	function deleta($id){
		include "query/conexao.php";
		
		$sql = "DELETE FROM fornecedor
				WHERE id_fornecedor = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Fornecedor excluído com sucesso!');window.location.replace('fornecedor.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível excluir o fornecedor!');window.location.replace('fornecedor.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		}
		
		$conn->close();
	}
	
	function getOptions($id){
		include "query/conexao.php";
		$output="<option value='0'>Novo Fornecedor...</option>";
		$result = mysqli_query($conn, "SELECT * FROM fornecedor");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {
				if($row->id_fornecedor == $id)
					$output.="<option selected='selected' value='".$row->id_fornecedor."'>".$row->nome_fornecedor."</option><br>";					
				else
					$output.="<option value='".$row->id_fornecedor."'>".$row->nome_fornecedor."</option></br>";
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
		
		$result = mysqli_query($conn, "SELECT * FROM fornecedor WHERE id_fornecedor='".$id."'");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				$output= str_replace("[NAME]", $row->nome_fornecedor, $output);
			}
		}
		$conn->close();	
		
		return $output;
	}
?>