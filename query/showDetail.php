<?php	
	function carrega($id, $output){
		include "query/conexao.php";
		
		$result = mysqli_query($conn, "
			SELECT d.id_details id_details, 
				   d.data_entrada di, 
				   d.data_saida df, 
				   group_concat(u.nome) nome, 
				   e.nome emp, 
				   c.desc cat, 
				   d.valor, 
				   c.limite,
				   d.local local, 
				   d.obs
			FROM details d 
			INNER JOIN usuarios_details ud 
				ON d.id_details=ud.id_details
			INNER JOIN usuarios u 
				ON u.id_usr=ud.id_usr
			LEFT JOIN empresa e 
				ON e.id_emp = d.id_emp
			LEFT JOIN categoria c 
				ON c.id_cat = d.id_cat
            WHERE d.id_details = ".$id."
			GROUP BY d.id_details
		");
		
		while ($row = mysqli_fetch_object($result)) {
			$output= str_replace("[DI]", $row->di, $output);
			if($row->df == "0000-00-00")
				$output= str_replace("[DF]", "-", $output);
			else
				$output= str_replace("[DF]", $row->df, $output);
			$output= str_replace("[USER]", $row->nome, $output);
			$output= str_replace("[EMP]", $row->emp, $output);
			$limite = str_replace(".", ",", $row->limite);
			$output= str_replace("[CAT]", $row->cat." (R$ ".$limite.")", $output);
			$valor = str_replace(".", ",", $row->valor);
			$output= str_replace("[VALOR]", $valor, $output);
			$output= str_replace("[LOCAL]", $row->local, $output);
			$output= str_replace("[OBS]", $row->obs, $output);
		}
		$conn->close();
		
		return $output;
	}
	
	function deleta($id){
		include "query/conexao.php";
		
		$sql = "DELETE FROM `details`
				WHERE `id_details` = '".$id."'";
		
		if ($conn->query($sql) === FALSE)
				echo "<script type='text/javascript'>window.alert('Não foi possível excluir o detalhe!');window.location.replace('index.php');</script>";
				
		$sql = "DELETE FROM `usuarios_details`
				WHERE `id_details` = '".$id."'";
		
		if ($conn->query($sql) === FALSE)
				echo "<script type='text/javascript'>window.alert('Não foi possível excluir o detalhe!');window.location.replace('index.php');</script>";
		
		echo "<script type='text/javascript'>window.alert('Detalhe excluído com sucesso!');window.location.replace('index.php');</script>";
					
		$conn->close();
	}
	
?>