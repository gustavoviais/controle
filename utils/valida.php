<?php
	
	function get_post_action($name){
		$params = func_get_args();

		foreach ($params as $name) {
			if (isset($_POST[$name])) {
				return $name;
			}
		}
	}
	
	function validaCampo($campo){
		if($campo==""){
			return "<script type='text/javascript'>window.alert('Digite um nome!');window.location.replace('./empresa.php');</script>";
		}
	}

?>