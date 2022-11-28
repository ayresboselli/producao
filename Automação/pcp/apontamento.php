<?php
$con = new PDO("mysql:host=localhost;dbname=zangraf_xkey_producao", "root", "1234");

$rs_op = $con->query("SELECT codigo op FROM producoes WHERE situacao != 'E'");

while($row_op = $rs_op->fetch(PDO::FETCH_OBJ)){
	$apt = false;
	$rs_apt = $con->query("SELECT ID_REGISTRO id, situacao, data_inicio, hora_inicio FROM apontamento WHERE producao = ".$row_op->op." ORDER BY item DESC");
	
	$ult_reg = null;
	while($row_apt = $rs_apt->fetch(PDO::FETCH_OBJ)){
		if(!$apt && $row_apt->situacao != "NAO INICIADA"){
			$apt = true;
		}else if($apt && $row_apt->situacao != "FINALIZADO"){
			$con->query("UPDATE apontamento SET situacao = 'FINALIZADO', data_inicio = curdate(), hora_inicio = curtime(), data_termino = curdate(), hora_termino = curtime() WHERE id_registro = ".$row_apt->id);
		}
		/*
		if($row_apt->situacao == "NAO INICIADA"){
			$ult_reg = $row_apt;
		}
		*/
	}
	/*
	if(!is_null($ult_reg)){
		$con->query("UPDATE apontamento SET situacao = 'EM ANDAMENTO', data_inicio = curdate(), hora_inicio = curtime(), data_termino = curdate(), hora_termino = curtime() WHERE id_registro = ".$ult_reg->id);
	}
	*/
}
?>