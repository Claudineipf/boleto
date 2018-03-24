<?php
include('src/layout.php');
$boleto = new Boleto\Layout\Unicred;
//
$dados = array();
$dados['valor'] = 1631;
$dados['agencia_ficticia'] = '3381'; //Pegar em um boleto já emitido ou direto com a Unicred
$dados['dv_agencia_ficticia'] = '2'; //Pegar em um boleto já emitido ou direto com a Unicred
$dados['nosso_numero'] = '12302000148';//PAAAANNNNNN=> P->Produto[1->Registrada, 3->Caucionada, 4->Descontada] | AAAA=>Ag. Unicred+dv | NNNNNN => sequencial
$dados['conta_corrente_ficticia'] = '1078204'; //Pegar em um boleto já emitido ou direto com a Unicred
$dados['dv_conta_corrente_ficticia'] = '0'; //Pegar em um boleto já emitido ou direto com a Unicred
$dados['vencimento'] = '2018-03-23';
$dados['numero_documento'] = '148';
$dados['data_documento'] = '2018-03-16';
$dados['data_processamento'] = '2018-03-18';
$dados['especie_documento'] = 'DM'; // DM => Duplicata Mercantil
$dados['quantidade'] = '';
//
$beneficiario = array();
$beneficiario['nome'] = 'UNICRED MATO GROSSO';
$beneficiario['tipo_documento'] = 'CNPJ'; // CNPJ ou CPF
$beneficiario['documento'] = '036.900.256/0001-00'; //CNPJ da Unicred
$beneficiario['endereco'] = 'R BARAO DE MELGACO,2754,SL 16';
$beneficiario['bairro'] = 'CENTRO SUL';
$beneficiario['cidade'] = 'CUIABA';
$beneficiario['uf'] = 'MT';
$beneficiario['cep'] = '78.020-800';
//
$pagador = array();
$pagador['nome'] = 'COCA COLA DO BRASIL';
$pagador['tipo_documento'] = 'CNPJ'; // CNPJ ou CPF
$pagador['documento'] = '111.222.333/4444-55'; //CNPJ ou CPF do pagador formatado
$pagador['endereco'] = 'ROD BR 364 KM 859,5';
$pagador['bairro'] = 'ZONA RURAL';
$pagador['cidade'] = 'CUIABA';
$pagador['uf'] = 'MT';
$pagador['cep'] = '78.300-000';
//
$sacador = array();
$sacador['nome'] = 'LABORATORIO DO BRASIL LTDA ME'; // Razão Social da Empresa que vai receber o valor
$sacador['tipo_documento'] = 'CNPJ'; // CNPJ ou CPF
$sacador['documento'] = '111.222.333/0001-55'; //CNPJ ou CPF da Empresa que vai receber o valor
$sacador['endereco'] = 'RUA BAHIA, 741NE';
$sacador['bairro'] = 'CENTRO';
$sacador['cidade'] = 'CAMPO NOVO DO PARECIS';
$sacador['uf'] = 'MT';
$sacador['cep'] = '78.360-000';
//
try{
	$boleto->setBoleto($dados);
	$boleto->setBeneficiario($beneficiario);
	$boleto->setPagador($pagador);
	$boleto->setSacador($sacador);
	$boleto->setDiretorio('./src');
	//
	$boleto->setInstrucao('Após vencimento Mora dia R$ 2,72');
	$boleto->setInstrucao('Multa vencimento Multa de 2%');
	$boleto->setInstrucao('Protestar após 5 dias úteis do vencimento');
	//
	$boleto->setReferencia('Fatura n° 5555');
	$boleto->setReferencia('Nota Fiscal n° 1052255');
	//
	$boleto->geraBoleto()->Output('boleto.pdf','I');
}catch (Exception $e) {
	print $e->getMessage();
}
//
?>