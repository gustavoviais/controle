<?php	
	function cadastra($numero_nf, $data_nf, $total_nf, $id_fornecedor){
		include "query/conexao.php";		
		
		$dnf = explode("/", $data_nf);
		$data_nf = $dnf[2]."-".$dnf[1]."-".$dnf[0];
		
		$sql = "INSERT INTO `nota_fiscal` (`numero_nf`, `data_nf`, `total_nf`, `id_fornecedor`, `user`, `dsy`)
				VALUES ('". $numero_nf . "', '".$data_nf."', ".$total_nf.", ".$id_fornecedor.", ".$_SESSION["user_id"].", NOW())";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Cadastrado com sucesso!');window.location.replace('detalhes.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível realizar o cadastro!');window.location.replace('nota_fiscal.php');</script>";
		}
		
		$conn->close();
	}	
	
	function edita($id, $numero_nf, $data_nf, $total_nf, $id_fornecedor){
		include "query/conexao.php";
		
		$dnf = explode("/", $data_nf);
		$data_nf = $dnf[2]."-".$dnf[1]."-".$dnf[0];
		
		
				
		$sql = "UPDATE `nota_fiscal` SET `numero_nf` = '".$numero_nf."', 
									     `data_nf` = '".$data_nf."', 
									     `total_nf` = ".$total_nf.", 
									     `id_fornecedor` = ".$id_fornecedor.", 
									     `user` = ".$_SESSION["user_id"].", 
									     `dsy` = NOW()
                WHERE `id_nf` = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Atualizado com sucesso!');window.location.replace('nota_fiscal.php');</script>";
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');window.location.replace('nota_fiscal.php');</script>";
		}
		
		$conn->close();
	}	
	
	function deleta($id){
		include "query/conexao.php";
		
		$sql = "DELETE FROM `nota_fiscal`
				WHERE `id_nf` = '".$id."'";

		if ($conn->query($sql) === TRUE) {
			echo "<script type='text/javascript'>window.alert('Nota Fiscal excluída com sucesso!');window.location.replace('nota_fiscal.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		} else {
			echo "<script type='text/javascript'>window.alert('Não foi possível excluir a Nota Fiscal!');window.location.replace('nota_fiscal.php');</script>";
			echo '<meta HTTP-EQUIV="Refresh">';
		}
		
		$conn->close();
	}
	
	function getOptions($id){
		include "query/conexao.php";
		$output="<option value='0'>Adicionar Nota Fiscal...</option>";
		$result = mysqli_query($conn, "
						SELECT * 
						FROM nota_fiscal NF
							 left join fornecedor F
								on NF.id_fornecedor=F.id_fornecedor
				  ");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {
				if($row->id_nf == $id)
					$output.="<option selected='selected' value='".$row->id_nf."'>".$row->numero_nf." - ".$row->nome_fornecedor." - "
							 .date('d/m/Y', strtotime($row->data_nf))." - R$ ".$row->total_nf."</option><br>";					
				else
					$output.="<option value='".$row->id_nf."'>".$row->numero_nf." - ".$row->nome_fornecedor." - "
							 .date('d/m/Y', strtotime($row->data_nf))." - R$ ".$row->total_nf."</option></br>";
			}
		}		
		$conn->close();
		
		return $output;
	}
	
	function getFornecedor($id){
		include "query/conexao.php";
		$output="";
		
		if($id!=0)
			$result = mysqli_query($conn, "SELECT DISTINCT F.id_fornecedor,
												 	   F.nome_fornecedor,
												 	   -1 as id_nf
										FROM fornecedor F
											LEFT JOIN nota_fiscal NF
													ON NF.id_fornecedor = F.id_fornecedor
										WHERE F.id_fornecedor not in (SELECT id_fornecedor FROM nota_fiscal WHERE id_nf = ".$id.")
										UNION
										SELECT DISTINCT F.id_fornecedor,
											F.nome_fornecedor,
											NF.id_nf
										FROM fornecedor F
											LEFT JOIN nota_fiscal NF
													ON NF.id_fornecedor = F.id_fornecedor
										WHERE NF.id_nf = ".$id."");
		else
			$result = mysqli_query($conn, "SELECT DISTINCT F.id_fornecedor,
												F.nome_fornecedor,
												-1 id_nf
											FROM fornecedor F
												LEFT JOIN nota_fiscal NF
														ON NF.id_fornecedor = F.id_fornecedor
											");
		
		
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {
				if($row->id_nf == $id)
					$output.="<option selected='selected' value='".$row->id_fornecedor."'>".$row->nome_fornecedor."</option><br>";					
				else
					$output.="<option value='".$row->id_fornecedor."'>".$row->nome_fornecedor."</option></br>";
			}
		}		
		$conn->close();
		
		return $output;
	}
	
	function loadFields($output, $id){
		include "query/conexao.php";
		
		$content= getOptions($id);		
		$output= str_replace("[OPTIONS]", $content, $output);
		
		$content= getFornecedor($id);		
		$output= str_replace("[FORNECEDOR]", $content, $output);
		
		$result = mysqli_query($conn, "SELECT * FROM nota_fiscal WHERE id_nf='".$id."'");
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				$output= str_replace("[NUMERO_NF]", $row->numero_nf, $output);
				$output= str_replace("[DNF]", date('d/m/Y', strtotime($row->data_nf)), $output);
				$limit = $row->total_nf;
				$limit = str_replace(".", ",", $limit);
				$output= str_replace("[TOTAL_NF]", $limit, $output);
			}			
		}else{
			$output= str_replace("[NUMERO_NF]", "", $output);
			$output= str_replace("[DNF]", "", $output);
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