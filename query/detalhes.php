<?php	
	function cadastra($id, $nf, $di, $df, $id_usr, $id_emp, $id_cat, $value, $local, $comment, $reembolso){
		include "query/conexao.php";
		
		$di_aux = explode("/", $di);
		$di = $di_aux[2]."-".$di_aux[1]."-".$di_aux[0];
		
		$df_aux = explode("/", $df);
		$df = $df_aux[2]."-".$df_aux[1]."-".$df_aux[0];
						
		$sql = "INSERT INTO `details` (`id_nf`, `data_entrada`, `data_saida`, `valor`, `local`, `obs`, `id_emp`, `id_cat`, `user`, `dsy`, `reembolso`)
				VALUES (".$nf.", '".$di."', '".$df."', ".$value.", '".$local."', '".$comment."', '".$id_emp."', '".$id_cat."', ".$_SESSION["user_id"].", NOW(), ".$reembolso.")";
				
		if ($conn->query($sql) === FALSE)
			echo "<script type='text/javascript'>window.alert('Não foi possível realizar o cadastro!');window.location.replace('detalhes.php');</script>";		
		
		$id_details = getDetail();
		
		for($i=0; $i<count($id_usr); $i++){
			$sql = "INSERT INTO `usuarios_details` (`id_details`, `id_usr`, `user`, `dsy`)
					VALUES ('".$id_details."', '".$id_usr[$i]."', ".$_SESSION["user_id"].", NOW())";							
					
			if ($conn->query($sql) === FALSE)
				echo "<script type='text/javascript'>window.alert('Não foi possível realizar o cadastro!');window.location.replace('detalhes.php');</script>";						
		}
		
		echo "<script type='text/javascript'>window.alert('Cadastrado com sucesso!!');window.location.replace('detalhes.php');</script>";
		
		$conn->close();
	}	
	
	function edita($id, $nf, $di, $df, $id_usr, $id_emp, $id_cat, $value, $local, $comment, $reembolso){
		include "query/conexao.php";
		
		$di_aux = explode("/", $di);
		$di = $di_aux[2]."-".$di_aux[1]."-".$di_aux[0];
		
		$df_aux = explode("/", $df);
		$df = $df_aux[2]."-".$df_aux[1]."-".$df_aux[0];
				
		$sql = "UPDATE `details` 
					SET `id_nf` = ".$nf.", 
						`data_entrada` = '".$di."', 
						`data_saida` = '".$df."', 
						`id_emp` = '".$id_emp."', 
						`id_cat` = '".$id_cat."', 
						`valor` = ".$value.", 
						`local` = '".$local."', 
						`obs` = '".$comment."',
						`user` = ".$_SESSION["user_id"].", 
						`dsy` = NOW(),
						`reembolso` = ".$reembolso."
                WHERE `id_details` = '".$id."'";
				
		if ($conn->query($sql) === FALSE)
				echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');window.location.replace('detalhes.php');</script>";			
			
		$sql = "DELETE FROM `usuarios_details`
				WHERE `id_details` = '".$id."'";
		
		if ($conn->query($sql) === FALSE)
				echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');window.location.replace('detalhes.php');</script>";
			
			
		for($i=0; $i<count($id_usr); $i++){
			$sql = "INSERT INTO `usuarios_details` (`id_details`, `id_usr`, `user`, `dsy`)
					VALUES ('".$id."', '".$id_usr[$i]."', ".$_SESSION["user_id"].", NOW())";							
					
			if ($conn->query($sql) === FALSE)
				echo "<script type='text/javascript'>window.alert('Não foi possível atualizar o registro!');window.location.replace('detalhes.php');</script>";						
		}
		
		echo "<script type='text/javascript'>window.alert('Atualizado com sucesso!!');window.location.replace('detalhes.php');</script>";
		
		$conn->close();
	}	
	
	function deleta($id){
		include "query/conexao.php";
		
		$sql = "DELETE FROM `usuarios_details`
				WHERE `id_details` = '".$id."'";
		
		if ($conn->query($sql) === FALSE)
				echo "<script type='text/javascript'>window.alert('Não foi possível excluir o detalhe!');window.location.replace('detalhes.php');</script>";
		
		$sql = "DELETE FROM `details`
				WHERE `id_details` = '".$id."'";
		
		if ($conn->query($sql) === FALSE)
				echo "<script type='text/javascript'>window.alert('Não foi possível excluir o detalhe!');window.location.replace('detalhes.php');</script>";		
		
		
		echo "<script type='text/javascript'>window.alert('Detalhe excluído com sucesso!');window.location.replace('detalhes.php');</script>";
					
		$conn->close();
	}
	
	function getDetail(){
		include "query/conexao.php";
		$result = mysqli_query($conn, "SELECT `id_details` FROM `details` ORDER BY `id_details` DESC LIMIT 1");
		while ($row = mysqli_fetch_object($result)) {
			$id_details = $row->id_details;
		}
		$conn->close();
		return $id_details;
	}
	
	function getNota($id){
		include "query/conexao.php";
		$output="";
		
		if($id==(-1))			
			$output.="<option selected='selected' value='0'>Todas</option>";
		
		if($id<=0)
			$result = mysqli_query($conn, "SELECT NF.id_nf,
												  NF.numero_nf,
												  F.nome_fornecedor
											FROM nota_fiscal NF
												 LEFT JOIN fornecedor F 
														  ON NF.id_fornecedor=F.id_fornecedor
											ORDER BY NF.id_nf DESC");
		else
			$result = mysqli_query($conn, "SELECT DISTINCT NF.id_nf,
										   				   NF.numero_nf,
										   				   F.nome_fornecedor,
														   CASE WHEN D.id_nf = (SELECT id_nf FROM details WHERE id_details = ".$id.") THEN D.id_details ELSE -1 END as id_details
										   FROM details D
										   	 RIGHT JOIN nota_fiscal NF
										   			  ON D.id_nf=NF.id_nf
										   	 LEFT JOIN fornecedor F 
										   			  ON NF.id_fornecedor=F.id_fornecedor
										   WHERE NF.id_nf <> (SELECT id_nf FROM details WHERE id_details = ".$id.")
										   UNION
										   SELECT DISTINCT NF.id_nf,
														   NF.numero_nf,
														   F.nome_fornecedor,
														   D.id_details
										   FROM details D
										   	 RIGHT JOIN nota_fiscal NF
										   			  ON D.id_nf=NF.id_nf
										   	 LEFT JOIN fornecedor F 
										   			  ON NF.id_fornecedor=F.id_fornecedor
										   WHERE D.id_details = ".$id."
										   ORDER BY id_nf DESC");
										
										
		$num = mysqli_num_rows($result);

		if($num != 0){
			while ($row = mysqli_fetch_object($result)) {
				if(isset($row->id_details) && $row->id_details == $id)
					$output.="<option selected='selected' value='".$row->id_nf."'>".$row->numero_nf." - ".$row->nome_fornecedor."</option><br>";
				else
					$output.="<option value='".$row->id_nf."'>".$row->numero_nf." - ".$row->nome_fornecedor."</option><br>";
			}
		}
		$conn->close();

		return $output;
	}
	
	function getOptions($id){
		include "query/conexao.php";		
		$output="<option value='0'>Novo registro...</option>";
		
		if($id==(-1))			
			$output.="<option selected='selected' value='-1'>Consulta detalhes...</option>";
		else
			$output.="<option value='-1'>Consulta detalhes...</option>";
		
		$result = mysqli_query($conn, "
				SELECT d.id_details id_details, 
					   d.data_entrada data, 
					   group_concat(u.nome) nome, 
					   e.nome emp, 
					   d.local local, 
					   c.desc,
					   nf.numero_nf numero_nf,
					   f.nome_fornecedor fornecedor
				FROM details d 
				INNER JOIN usuarios_details ud 
					   ON d.id_details=ud.id_details
				INNER JOIN usuarios u 
					   ON u.id_usr=ud.id_usr
				LEFT JOIN empresa e 
					   ON e.id_emp = d.id_emp
				LEFT JOIN categoria c 
					   ON c.id_cat=d.id_cat
				LEFT JOIN nota_fiscal nf
					   ON nf.id_nf=d.id_nf
				LEFT JOIN fornecedor f
					   ON nf.id_fornecedor=f.id_fornecedor
				GROUP BY d.id_details
				ORDER BY d.id_details desc
		");
		
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {
				if($row->id_details == $id)
					$output.="<option selected='selected' value='".$row->id_details."'>ID:"
							 .$row->id_details." - NF:".$row->numero_nf." ".$row->fornecedor." - ".$row->data." - ".$row->nome." (".$row->emp.") - ".$row->desc.
							 "</option><br>";					
				else
					$output.="<option value='".$row->id_details."'>ID:"
							 .$row->id_details." - NF:".$row->numero_nf." ".$row->fornecedor." - ".$row->data." - ".$row->nome." (".$row->emp.") - ".$row->desc.
							 "</option></br>";
			}
		}	
		$conn->close();
		
		return $output;
	}
	
	function getOptions_Usr($id){
		include "query/conexao.php";
		$output="";
		
		if($id==(-1))			
			$output.="<option selected='selected' value='0'>Todos</option>";
		
		if($id!=0)
			$result = mysqli_query($conn, "
				SELECT DISTINCT u.id_usr, 
								u.nome, 
								ud.id_details, 
								e.nome enome
				FROM usuarios u
				LEFT JOIN usuarios_details ud 
					ON u.id_usr=ud.id_usr 
					AND ud.id_details= ".$id."
				LEFT JOIN empresa e 
					ON e.id_emp = u.id_emp
			");
		else
			$result = mysqli_query($conn, "
				SELECT u.id_usr, 
					   u.nome,
					   e.nome enome 
				FROM usuarios u
					LEFT JOIN empresa e 
						ON e.id_emp = u.id_emp
			");
			
		
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				if(isset($row->id_details) && $row->id_details == $id)
					$output.="<option selected='selected' value='".$row->id_usr."'>".$row->nome." (".$row->enome.")</option><br>";					
				else
					$output.="<option value='".$row->id_usr."'>".$row->nome." (".$row->enome.")</option><br>";					
			}			
		}		
		$conn->close();
		
		return $output;
	}
	
	function getOptions_Emp($id){
		include "query/conexao.php";
		$output="";
		
		if($id==(-1))			
			$output.="<option selected='selected' value='0'>Todas</option>";
		
		if($id!=0)
			$result = mysqli_query($conn, "
				SELECT e.id_emp id_emp, 
					   e.nome nome, 
					   d.id_details id_details
				FROM empresa e 
				LEFT JOIN details d 
					ON e.id_emp=d.id_emp 
          AND d.id_details = ".$id."
			");
		else
			$result = mysqli_query($conn, "SELECT * FROM empresa ORDER BY id_emp desc");
			
		
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				if(isset($row->id_details) && $row->id_details == $id)
					$output.="<option selected='selected' value='".$row->id_emp."'>".$row->nome."</option><br>";					
				else
					$output.="<option value='".$row->id_emp."'>".$row->nome."</option></br>";
			}
		}		
		$conn->close();
		
		return $output;
	}
	
	function getOptions_Cat($id){
		include "query/conexao.php";
		$output="";
		
		if($id==(-1))			
			$output.="<option selected='selected' value='0'>Todas</option>";
		
		if($id!=0)
			$result = mysqli_query($conn, "
				SELECT c.id_cat id_cat, 
					   c.desc, c.limite, 
					   d.id_details id_details
				FROM categoria c 
				LEFT JOIN details d 
					ON c.id_cat=d.id_cat 
					AND d.id_details = ".$id."
			");
		else
			$result = mysqli_query($conn, "SELECT * FROM categoria");
			
		
		$num = mysqli_num_rows($result);

		if($num != 0){			
			while ($row = mysqli_fetch_object($result)) {				
				if(isset($row->id_details) && $row->id_details == $id)
					$output.="<option selected='selected' value='".$row->id_cat."'>".$row->desc." (".$row->limite.")</option><br>";					
				else
					$output.="<option value='".$row->id_cat."'>".$row->desc." (".$row->limite.")</option><br>";
			}
		}		
		$conn->close();
		
		return $output;
	}
	
	function setInputFields($id, $output){
		include "query/conexao.php";
		
		$result = mysqli_query($conn, "
			SELECT data_entrada di, 
				   data_saida df, 
				   valor value, 
				   local local, 
				   obs obs,
				   reembolso
			FROM details
			WHERE id_details = ".$id."
		");
		
		while ($row = mysqli_fetch_object($result)) {
			$output= str_replace("[DI]", date('d/m/Y', strtotime($row->di)), $output);
			if($row->df == "0000-00-00")
				$output= str_replace("[DF]", "", $output);
			else
				$output= str_replace("[DF]", date('d/m/Y', strtotime($row->df)), $output);
			$valor = str_replace(".", ",", $row->value);
			$output= str_replace("[VALUE]", $valor, $output);
			
			if($row->reembolso)
				$output= str_replace("[REEMBOLSO]", "checked", $output);
			else
				$output= str_replace("[REEMBOLSO]", "", $output);
			
			$output= str_replace("[LOCAL]", $row->local, $output);
			$output= str_replace("[OBS]", $row->obs, $output);
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
		
		$output= setInputFields($id, $output);			
		
		$content= getNota($id);	
		$output= str_replace("[NF]", $content, $output);
		
		$content= getOptions_Usr($id);	
		$output= str_replace("[OPTIONS_USR]", $content, $output);
		
		$content= getOptions_Emp($id);	
		$output= str_replace("[OPTIONS_EMP]", $content, $output);
		
		$content= getOptions_Cat($id);	
		$output= str_replace("[OPTIONS_CAT]", $content, $output);
		
		$conn->close();
		
		return $output;
	}
?>
