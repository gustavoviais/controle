<?php	
	function getDayContent($date){
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
				   c.limite limite
			FROM details d 
			INNER JOIN usuarios_details ud 
				ON d.id_details=ud.id_details
			INNER JOIN usuarios u 
				ON u.id_usr=ud.id_usr
			LEFT JOIN empresa e 
				ON e.id_emp = d.id_emp
			LEFT JOIN categoria c 
				ON c.id_cat = d.id_cat
            WHERE d.data_entrada= '".$date."' 
				  OR (d.data_entrada<'".$date."' AND d.data_saida>='".$date."')
			GROUP BY d.id_details
		");
		
		$color=0;
		
		while ($row = mysqli_fetch_object($result)) {
			$st = getST($row->id_details, $row->id_cat, $row->valor, $row->di, $row->df);
			$valor = str_replace(".", ",", $row->valor);
			
			if($st == "OK")
				if($color%2==0)
					$output.= "<tr style='background-color:#DCDCDC;border-bottom: thin solid #A9A9A9;border-top: thin solid #A9A9A9;'>";
				else
					$output.= "<tr style='border-bottom: thin solid #A9A9A9;'>";
			else{
				if($color%2==0)
					$output.= "<tr style='background-color:#DCDCDC;color:red;border-bottom: thin solid #A9A9A9;border-top: thin solid #A9A9A9;'>";
				else
					$output.= "<tr style='color:red;border-bottom: thin solid #A9A9A9;'>";
			}
			
			$output.="  <td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:30px'>".$row->id_details."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:80px'>".$row->di."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:80px'>".$row->df."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:150px'>".$row->nome."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:100px'>".$row->emp."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:30px'>".$row->cat."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:30px'>".$valor."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:80px'>".$row->local."</td>
						<td align='center' style='padding-left:5px;padding-right:5px;padding-top:5px; padding-bottom:5px; min-width:30px'>".$row->obs."</td>
					<tr>";
			
			$color++;
		}		
		$conn->close();	
		
		
		return $output;
	}	

	function getTotal($date	){
		include "query/conexao.php";
		
		$output="";
		$result = mysqli_query($conn, "			
			SELECT c.desc, 
				   SUM(d.valor) valor
			FROM details d 
			LEFT JOIN categoria c 
				ON d.id_cat=c.id_cat
			WHERE d.data_entrada= '".$date."' 
				  OR (d.data_entrada<'".$date."' AND d.data_saida>='".$date."')
			GROUP BY d.id_cat
		");
		
		$aux=0;
		
		while ($row = mysqli_fetch_object($result)) {			
			$valor = str_replace(".", ",", $row->valor);
			
			$output.= "<tr>";
			if($aux==0)
				$output.="<td align='right' style='padding-left:5px;padding-right:5px;padding-top:5px;'><b>Total por categoria:</b></td>";
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
?>
