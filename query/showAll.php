<?php
	
	function getAllContent($orderby){
		include "query/conexao.php";
		include "query/index.php";
				
		$output = "";
			
		$result = mysqli_query($conn, "			
			SELECT d.id_details id_details, 
				   d.data_entrada di, 
				   d.data_saida df, 
				   group_concat(u.nome) nome, 
				   e.nome emp, 
				   c.id_cat id_cat, 
				   c.desc cat, 
				   d.valor, 
				   d.local local, 
				   d.obs, 
				   c.limite limite,
				   nf.numero_nf,
				   nf.total_nf,
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
			WHERE d.id_details<>0 
				  ".$_SESSION['id']." 				   
				  ".$_SESSION['di']." 
				  ".$_SESSION['df']."
				  ".$_SESSION['nf']."
				  ".$_SESSION['fornecedor']."
				  ".$_SESSION['total_nf']."
				  ".$_SESSION['user']."
				  ".$_SESSION['reembolso']."
				  ".$_SESSION['cat']."
				  ".$_SESSION['valor']."
				  ".$_SESSION['obs']."
		  GROUP BY nf.numero_nf, d.id_details
		  ORDER BY ".$orderby."

		");
		 
		$color=0;
		$border=0;
		$_SESSION['numero_registros'] = 0;
		
		while ($row = mysqli_fetch_object($result)) {
			$_SESSION['numero_registros']++;
			$st = getST($row->id_details, $row->id_cat, $row->valor, $row->di, $row->df);
			$valor = str_replace(".", ",", $row->valor);			
			$total_nf = str_replace(".", ",", $row->total_nf);		

			$obs = substr($row->obs,0,15);
			
			if($obs != "")
				$obs = $obs . "...";			
			
			if($st == "OK"){				
						$output.= "<tr style='border-bottom: thin solid #A9A9A9;'>";	
			}else{
						$output.= "<tr style='color:red;border-bottom: thin solid #A9A9A9;'>";								
			}
			
			if($row->reembolso==0)
				$reembolso="Não";
			else
				$reembolso="Sim";
			
			$output.="
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:30px;'>".$row->id_details."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:80px;'>".date('d/m/Y', strtotime($row->di))."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:80px;'>".date('d/m/Y', strtotime($row->df))."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:50px;'>".$row->numero_nf."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:80px;'>".$row->nome_fornecedor."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:80px;'>".$total_nf."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:150px;'>".$row->nome."</td>						
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:30px;'>".$row->cat."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:80px;'>".$valor."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:30px;'>".$reembolso."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:3px; padding-bottom:3px; min-width:30px;'>".$obs."</td>
						<td align='center' style='padding-top:3px; padding-bottom:3px; min-width:75px;'>
							<a href='mdetalhes.php?id=".$row->id_details."&action=0' onClick='return confirmar();' style='text-decoration:none;'>
								<img src=img/del.ico style=margin-right:5px;width:17px;>
							</a>
							<a href='#modalDetalhes' data-toggle='modal' data-target='#modalDetalhes' onClick=setModalValue(".$row->id_details.",1) style='text-decoration:none;'>
								<img src=img/edit.png style=margin-right:5px;width:17px;>
							</a>
							<a href='#modalDetalhes' data-toggle='modal' data-target='#modalDetalhes' onClick=setModalValue(".$row->id_details.",2) style='text-decoration:none;'>
								<img src=img/find.ico style=width:17px;>
							</a>
						</td>
					</tr>";
					
			$color++;
			$border=$row->numero_nf;
		}			
		$conn->close();	
		
		return $output;
	}		
	
	function getTotal(){
		include "query/conexao.php";
		
		$output="";
		
		$result = mysqli_query($conn, "			
			SELECT SUM(v.valor) valor FROM (SELECT d.valor
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
										WHERE d.id_details<>0 
											  ".$_SESSION['id']." 				   
											  ".$_SESSION['di']." 
											  ".$_SESSION['df']."
											  ".$_SESSION['nf']."
											  ".$_SESSION['fornecedor']."
											  ".$_SESSION['total_nf']."
											  ".$_SESSION['user']."
											  ".$_SESSION['reembolso']."
											  ".$_SESSION['cat']."
											  ".$_SESSION['valor']."
											  ".$_SESSION['obs']."
									  GROUP BY nf.numero_nf, d.id_details
									  ORDER BY nf.numero_nf desc, d.data_entrada) v
		");	
		
		while ($row = mysqli_fetch_object($result)) {	
			$valor = str_replace(".", ",", $row->valor);		
			$output.= "<tr>";
			$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'></td>";
			$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'><h4>Total:
					  <td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'><h4>R$ ".$valor."</td>";
			$output.= "</tr>";		
		}
		$conn->close();
			
		return $output;
	}

	function getTotalParcial(){
		include "query/conexao.php";
		
		$output="";
		
		$result = mysqli_query($conn, "			
			SELECT v.desc, sum(v.valor) valor FROM (SELECT d.id_cat, c.desc, d.valor valor
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
			WHERE d.id_details<>0 
				  ".$_SESSION['id']." 				   
				  ".$_SESSION['di']." 
				  ".$_SESSION['df']."
				  ".$_SESSION['nf']."
				  ".$_SESSION['fornecedor']."
				  ".$_SESSION['total_nf']."
				  ".$_SESSION['user']."
				  ".$_SESSION['reembolso']."
				  ".$_SESSION['cat']."
				  ".$_SESSION['valor']."
				  ".$_SESSION['obs']."			  
            GROUP BY d.id_cat, d.id_details) v
            GROUP BY v.id_cat
		");
		
		$aux=0;
		
		while ($row = mysqli_fetch_object($result)) {			
			$valor = str_replace(".", ",", $row->valor);
			
			$output.= "<tr>";
			if($aux==0)
				$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'><b>Valor por categoria:</b></td>";
			else
				$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'></td>";
			$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'>".$row->desc."...</td>		
					  <td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'>R$ ".$valor."</td>";
			$output.= "</tr>";
			
			$aux++;
		}
		$conn->close();
			
		return $output;
	}	
	
	function getTotalNota(){
		include "query/conexao.php";
		
		$output="";
		
		$result = mysqli_query($conn, "			
			SELECT sum(v.valor) valor, v.total_nf total_nf, v.nome_fornecedor nome_fornecedor, v.numero_nf numero_nf
			FROM(SELECT nf.id_nf, d.id_details, nf.numero_nf, nf.total_nf, f.nome_fornecedor, d.valor valor
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
				WHERE d.id_details<>0 
					  ".$_SESSION['id']." 				   
					  ".$_SESSION['di']." 
					  ".$_SESSION['df']."
					  ".$_SESSION['nf']."
					  ".$_SESSION['fornecedor']."
					  ".$_SESSION['total_nf']."
					  ".$_SESSION['user']."
					  ".$_SESSION['reembolso']."
					  ".$_SESSION['cat']."
					  ".$_SESSION['valor']."
					  ".$_SESSION['obs']."
				GROUP BY nf.id_nf, d.id_details) v
			GROUP BY v.id_nf
		");
		
		$aux=0;
		
		while ($row = mysqli_fetch_object($result)) {			
			$valor = str_replace(".", ",", $row->valor);
			$total_nf = str_replace(".", ",", $row->total_nf);
			
			$output.= "<tr>";
			if($aux==0)
				$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'><b>Valor por NF:</b></td>";
			else
				$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'></td>";
			if($total_nf!=$valor)
				$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;color:red;'>".$row->numero_nf.
					   " (R$ ".$total_nf.") - ".$row->nome_fornecedor."...</td>
					  <td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;color:red;'>R$ ".$valor."</td>";
			else
				$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'>".$row->numero_nf.
					   " (R$ ".$total_nf.") - ".$row->nome_fornecedor."...</td>
					  <td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'>R$ ".$valor."</td>";
			$output.= "</tr>";
			
			$aux++;
		}
		$conn->close();
			
		return $output;
	}
?>	
