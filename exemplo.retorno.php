<?php
include('src/retorno.php');
//
$retorno = new Boleto\Retorno\Unicred;
//
$file = 'C:\\Apache24\\htdocs\\boleto\\retornos\\2018_Março_CB010301_009_CB_09_4871164_20180301024219_00107720-4.RET';
//
try{
	$retorno->setFileRetorno($file);
	print_r($retorno->getRetorno());
}catch (Exception $e) {
	print $e->getMessage();
}
//
?>