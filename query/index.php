<?php
	function getContent(){
		include "query/conexao.php";
		$content="";
		
		$result = mysqli_query($conn, "
			SELECT  d.id_details, 
					d.id_cat, 
					d.valor, 
					d.data_entrada,
					d.data_saida,
					c.desc
			FROM details d 
					left join categoria c on d.id_cat=c.id_cat
			ORDER BY d.data_entrada
		");
		
		$i=4;
		$data_entrada="";
		$he=0;	$hs=1; $aux=0; 
		
		while ($row = mysqli_fetch_object($result)) {			
			$id_details = $row->id_details;
			$id_cat = $row->id_cat;
			$valor = $row->valor;
			$valor = str_replace(".", ",", $valor);
						
			if($data_entrada == $row->data_entrada){		
				$aux = ($row->data_entrada == $row->data_saida) ? 1 : 0;
				
				if($aux==1){
					$he = $he+1;
					$hs = $hs+1;		
				}
				
			}else{
				$he=0;	$hs=1;
			}
			
			$data_entrada = $row->data_entrada;
				$di = explode("-", $data_entrada);	
				
			$data_saida = $row->data_saida;
				$df = explode("-", $data_saida);	
				
			$di[1] = $di[1] - 1;
			$df[1] = $df[1] - 1;
			

				
			$content .= "
				appointment.push ({
						id: '".$id_details."',
						subject: '[SUB]',
						calendar: '[ST]',
						start: new Date(".$di[0].", ".$di[1].", ".$di[2].", ".$he.", 0, 0),
						end: new Date(".$df[0].", ".$df[1].", ".$df[2].", ".$hs.", 0, 0)
				})		
			";
			
			//$i = $i + 3;
						
			$subject = getSubject($id_details)." ".$valor." - ".$row->desc;
			$content= str_replace("[SUB]", $subject, $content);
			$st = getST($id_details, $id_cat, $valor, $data_entrada, $data_saida);
			$content= str_replace("[ST]", $st, $content);
			
			$content.= "appointments.push(appointment[".$i."]);";
			$i++;
		}
		
		$conn->close();
		
		return $content;
	}
	
	function getST($id, $cat, $valor, $di, $df){
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
			
			/*$result = mysqli_query($conn, "
				select sum(d.valor/(select count(id_usr) from usuarios_details where id_details=d.id_details)/(DATEDIFF(d.data_saida, d.data_entrada)+1)) as soma
				from details d 
					inner join usuarios_details ud on d.id_details=ud.id_details
				where ud.id_usr=".$users[$i]."
					  and '".$di."' between d.data_entrada and d.data_saida
					  /*and d.data_entrada='".$di."'
					  and d.id_cat=".$cat."
			");*/
			
			if($di==$df){			
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
				$result = mysqli_query($conn, "
					select  round(sum(
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
		
		$result = mysqli_query($conn, "SELECT limite FROM categoria where id_cat=".$cat."");
		
		while ($row = mysqli_fetch_object($result)) {
			$limite = $row->limite;
		}
		
		//$conta=($valor/$dias)/$aux;
		
		$conn->close();
		
		if($soma>$limite)
			return "Erro";
		else
			return "OK";
	}
	
	function getSubject($id){
		include "query/conexao.php";
		$content="";
		
		$result = mysqli_query($conn, "SELECT COUNT(*) users FROM usuarios_details where id_details=".$id."");
		
		while ($row = mysqli_fetch_object($result)) {
			$users = $row->users;
		}
		
		if($users==1)
			$content = "[".$users."]";
		else
			$content = "[".$users."]";
		
		$conn->close();
		
		return $content;
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
