<?php	
	function carrega($id, $output){
		include "query/conexao.php";
		
		$result = mysqli_query($conn, "
			SELECT d.id_details id_details, 
				   d.data_entrada di, 
				   d.data_saida df, 
				   group_concat(u.nome SEPARATOR ', ') nome, 
				   e.nome emp, 
				   c.desc cat, 
				   c.id_cat id_cat, 
				   d.valor, 
				   d.valor_total, 
				   c.limite,
				   d.local local, 
				   d.obs,
				   nf.numero_nf,
				   nf.total_nf,
				   nf.data_nf,
				   f.nome_fornecedor,
				   d.reembolso
			FROM details d 
			INNER JOIN usuarios_details ud 
				ON d.id_details=ud.id_details
			INNER JOIN usuarios u 
				ON u.id_usr=ud.id_usr
			LEFT JOIN empresa e 
				ON e.id_emp = d.id_emp
			LEFT JOIN categoria c 
				ON c.id_cat = d.id_cat
			LEFT JOIN nota_fiscal nf
				ON nf.id_nf = d.id_nf
			LEFT JOIN fornecedor f
				ON f.id_fornecedor=nf.id_fornecedor
            WHERE d.id_details = ".$id."
			GROUP BY d.id_details
		");
		
		while ($row = mysqli_fetch_object($result)) {
			$output= str_replace("[ID]", $row->id_details, $output);
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
			$valor_total = str_replace(".", ",", $row->valor_total);
			$output= str_replace("[VALOR_TOTAL]", $valor_total, $output);
			$vnf = str_replace(".", ",", $row->total_nf);
			$output= str_replace("[VNF]", $vnf, $output);
			$output= str_replace("[NF]", $row->numero_nf, $output);
			$output= str_replace("[DNF]", $row->data_nf, $output);
			$output= str_replace("[FORNECEDOR]", $row->nome_fornecedor, $output);
			$output= str_replace("[LOCAL]", $row->local, $output);
			$output= str_replace("[OBS]", $row->obs, $output);
			if($row->reembolso == 0)
				$output= str_replace("[REEMBOLSO]", "Não", $output);
			else
				$output= str_replace("[REEMBOLSO]", "Sim", $output);
			
			$tabela_limite = getST($row->id_details, $row->id_cat, $row->valor, $row->di, $row->df);
			$output= str_replace("[TABLE_LIMIT]", $tabela_limite, $output);
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
	
	function getST($id, $cat, $valor, $di, $df){
		// echo $id."<br>";
		// echo $cat."<br>";
		// echo $valor;
		// echo $di;
		// echo $df;
		include "query/conexao.php";
		$content="";
		
		$dias=0;
		$dias = date_diff(date_create($di), date_create($df));    
		$dias = $dias->format('%a');
		$dias++;
		
		$users = array();
		$result = mysqli_query($conn, "SELECT distinct id_usr FROM usuarios_details where id_details=".$id."");
		
		$aux=0;
		while ($row = mysqli_fetch_object($result)) {
			$users[$aux] = $row->id_usr;
			$aux++;			
		}
		
		//for($i=0;$i<$aux;$i++){		
			if($di==$df){		
				if(count($users)>1)
					$result = mysqli_query($conn, "
						select round(sum(v.soma),2) soma 
						from (select DISTINCT
									(d.valor/
									(select count(id_usr) from usuarios_details where id_details=d.id_details)/
									(DATEDIFF(d.data_saida, d.data_entrada)+1)) soma					
							  from details d 
								inner join usuarios_details ud on d.id_details=ud.id_details
							  where ud.id_usr IN (".join(',',$users).")
								  and (d.data_entrada BETWEEN '".$di."' and '".$df."'
								  OR d.data_saida BETWEEN '".$di."' and '".$df."'
								  OR '".$di."' BETWEEN d.data_entrada and d.data_saida)
								  and d.id_cat=".$cat."
								  and d.reembolso=0								  
								  and d.id_details=".$id.") v
					");
				else
					$result = mysqli_query($conn, "
						select round(sum(v.soma),2) soma 
						from (select DISTINCT
									(d.valor/
									(select count(id_usr) from usuarios_details where id_details=d.id_details)/
									(DATEDIFF(d.data_saida, d.data_entrada)+1)) soma					
							  from details d 
								inner join usuarios_details ud on d.id_details=ud.id_details
							  where ud.id_usr IN (".join(',',$users).")
								  and (d.data_entrada BETWEEN '".$di."' and '".$df."'
								  OR d.data_saida BETWEEN '".$di."' and '".$df."'
								  OR '".$di."' BETWEEN d.data_entrada and d.data_saida)
								  and d.id_cat=".$cat."
								  and d.reembolso=0) v
					");
			}else{
				if(count($users)>1)
					$result = mysqli_query($conn, "
						select distinct round(sum(
									d.valor/
									(select count(id_usr) from usuarios_details where id_details=d.id_details)/
									(CASE WHEN (DATEDIFF(d.data_saida, d.data_entrada)+1) > ".$dias." THEN (DATEDIFF(d.data_saida, d.data_entrada)+1) ELSE ".$dias." END))
								,2) as soma					
						from details d 
							inner join usuarios_details ud on d.id_details=ud.id_details
						where ud.id_usr IN (".join(',',$users).")
							  and (d.data_entrada BETWEEN '".$di."' and '".$df."'
							  OR d.data_saida BETWEEN '".$di."' and '".$df."'
							  OR '".$di."' BETWEEN d.data_entrada and d.data_saida)
							  and d.id_cat=".$cat."
							  and d.reembolso=0
							  and d.id_details=".$id."
					");
				else	
					$result = mysqli_query($conn, "
						select distinct round(sum(
									d.valor/
									(select count(id_usr) from usuarios_details where id_details=d.id_details)/
									(CASE WHEN (DATEDIFF(d.data_saida, d.data_entrada)+1) > ".$dias." THEN (DATEDIFF(d.data_saida, d.data_entrada)+1) ELSE ".$dias." END))
								,2) as soma					
						from details d 
							inner join usuarios_details ud on d.id_details=ud.id_details
						where ud.id_usr IN (".join(',',$users).")
							  and (d.data_entrada BETWEEN '".$di."' and '".$df."'
							  OR d.data_saida BETWEEN '".$di."' and '".$df."'
							  OR '".$di."' BETWEEN d.data_entrada and d.data_saida)
							  and d.id_cat=".$cat."
							  and d.reembolso=0
					");
			}
			
			while ($row = mysqli_fetch_object($result)) {
				$soma = $row->soma;
			}
		//}
	
		if($di==$df){		
			if(count($users)>1)
				$result = mysqli_query($conn, "
					select distinct d.id_details,
							d.data_entrada,
							d.data_saida,
							d.valor valor,
							(select count(id_usr) from usuarios_details where id_details=d.id_details) user,
							(DATEDIFF(d.data_saida, d.data_entrada)+1) dias,
							round((d.valor/
								(select count(id_usr) from usuarios_details where id_details=d.id_details)/
								(DATEDIFF(d.data_saida, d.data_entrada)+1)),2) liquido
					from details d 
						inner join usuarios_details ud on d.id_details=ud.id_details
					where ud.id_usr IN (".join(',',$users).")
						  and (d.data_entrada BETWEEN '".$di."' and '".$df."'
						  OR d.data_saida BETWEEN '".$di."' and '".$df."'
						  OR '".$di."' BETWEEN d.data_entrada and d.data_saida)
						  and d.id_cat=".$cat."
						  and d.reembolso=0
						  and d.id_details=".$id."
				");
			else	
				$result = mysqli_query($conn, "
					select distinct d.id_details,
							d.data_entrada,
							d.data_saida,
							d.valor valor,
							(select count(id_usr) from usuarios_details where id_details=d.id_details) user,
							(DATEDIFF(d.data_saida, d.data_entrada)+1) dias,
							round((d.valor/
								(select count(id_usr) from usuarios_details where id_details=d.id_details)/
								(DATEDIFF(d.data_saida, d.data_entrada)+1)),2) liquido
					from details d 
						inner join usuarios_details ud on d.id_details=ud.id_details
					where ud.id_usr IN (".join(',',$users).")
						  and (d.data_entrada BETWEEN '".$di."' and '".$df."'
						  OR d.data_saida BETWEEN '".$di."' and '".$df."'
						  OR '".$di."' BETWEEN d.data_entrada and d.data_saida)
						  and d.id_cat=".$cat."
						  and d.reembolso=0
				");
		}else{
			if(count($users)>1)
				$result = mysqli_query($conn, "
					select distinct d.id_details,
							d.data_entrada,
							d.data_saida,
							d.valor valor,
							(select count(id_usr) from usuarios_details where id_details=d.id_details) user,
							(CASE WHEN (DATEDIFF(d.data_saida, d.data_entrada)+1) > ".$dias." THEN (DATEDIFF(d.data_saida, d.data_entrada)+1) ELSE ".$dias." END) dias,
							(round(d.valor/
								(select count(id_usr) from usuarios_details where id_details=d.id_details)/
								(CASE WHEN (DATEDIFF(d.data_saida, d.data_entrada)+1) > ".$dias." THEN (DATEDIFF(d.data_saida, d.data_entrada)+1) ELSE ".$dias." END),2)) liquido
					from details d 
						inner join usuarios_details ud on d.id_details=ud.id_details
					where ud.id_usr IN (".join(',',$users).")
						  and (d.data_entrada BETWEEN '".$di."' and '".$df."'
						  OR d.data_saida BETWEEN '".$di."' and '".$df."'
						  OR '".$di."' BETWEEN d.data_entrada and d.data_saida)
						  and d.id_cat=".$cat."
						  and d.reembolso=0
						  and d.id_details=".$id."
				");
			else	
				$result = mysqli_query($conn, "
					select distinct d.id_details,
							d.data_entrada,
							d.data_saida,
							d.valor valor,
							(select count(id_usr) from usuarios_details where id_details=d.id_details) user,
							(CASE WHEN (DATEDIFF(d.data_saida, d.data_entrada)+1) > ".$dias." THEN (DATEDIFF(d.data_saida, d.data_entrada)+1) ELSE ".$dias." END) dias,
							(round(d.valor/
								(select count(id_usr) from usuarios_details where id_details=d.id_details)/
								(CASE WHEN (DATEDIFF(d.data_saida, d.data_entrada)+1) > ".$dias." THEN (DATEDIFF(d.data_saida, d.data_entrada)+1) ELSE ".$dias." END),2)) liquido
					from details d 
						inner join usuarios_details ud on d.id_details=ud.id_details
					where ud.id_usr IN (".join(',',$users).")
						  and (d.data_entrada BETWEEN '".$di."' and '".$df."'
						  OR d.data_saida BETWEEN '".$di."' and '".$df."'
						  OR '".$di."' BETWEEN d.data_entrada and d.data_saida)
						  and d.id_cat=".$cat."
						  and d.reembolso=0
				");
		}
		
		while ($row = mysqli_fetch_object($result)) {
			$content.= "<tr>
							<th style='text-align:center;'>".$row->id_details."</th>
							<th style='text-align:center;padding-right:2px;padding-left:2px;'>".date('d/m/Y', strtotime($row->data_entrada))."</th>
							<th style='text-align:center;padding-right:2px;padding-left:2px;'>".date('d/m/Y', strtotime($row->data_saida))."</th>
							<th style='text-align:center;'>".$row->valor."</th>
							<th style='text-align:center;'>".$row->user."</th>
							<th style='text-align:center;'>".$row->dias."</th>
							<th style='text-align:center;'>".$row->liquido."</th>
						</tr>";
		}
	
		$content.= "<tr><th colspan='7' style='text-align:right;padding-right:5px;'>Soma: ".$soma."</th></tr>";
		
		
		$conn->close();
		
		return $content;
	}
	
?>