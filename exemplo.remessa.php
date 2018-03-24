<?php
include('src/remessa.php');

$remessa = new Boleto\Remessa\Unicred;
//
//Registro Header
$header['codigo_empresa'] = '1077304';
$header['nome_da_empresa'] = 'LABORATORIOS DO BRASIL LTDA ME';
$header['data_gravacao_arquivo'] = '190318';
$header['numero_sequencial_arquivo'] = 2;
//
try{
	$remessa->setDiretorio('./');
	$remessa->setHeader($header);
	//Registro detalhe tipo 1
	for($i = 0;$i < 3; $i++){
		$detalhe['codigo_agencia_ficticia'] = '3381'; //Peguei a informação em um boleto impresso da unicred Cobexpress 'Agencia/Código Beneficiário'
		$detalhe['conta_corrente_ficticia'] = '1078204'; //Vide acima
		$detalhe['digito_corrente'] = '0'; //Vide acima
		$detalhe['controle_participante'] = '12302000149'; //Nosso numero sem o dv
		$detalhe['indicacao_multa'] = 2; //0 -> não considerar multa    2 -> considerar percentual multa
		$detalhe['percentual_multa'] = 2; //2%
		$detalhe['nosso_numero_dv'] = '123020001493'; //Nosso numero com dv
		$detalhe['desconto_bonificacao'] = 0; //Caso haja desconto ou bonificação
		$detalhe['tipo_impressao'] = '2'; //1 -> Banco registra e  emite   2 -> Banco registra e cliente emite
		$detalhe['id_operacao_banco'] = 0; //Código do pagador no sistema cobexpress -> somente se 'tipo_inscricao_pagador' for igual a 99
		$detalhe['numero_documento'] = '479';
		$detalhe['data_vencimento'] = '2018-04-20'; //YYYY-MM-DD
		$detalhe['valor_titulo'] = 1547.89;
		$detalhe['especie_titulo'] = '01'; //01 -> DM  02 -> NP  03 -> NS  04 -> CS  05 -> REC  10 -> LC  11 -> ND  12 -> DS  99 -> Outros
		$detalhe['data_emissao'] = '2018-03-20'; //DDMMAA
		$detalhe['indicacao_protesto'] = '00'; //00 -> Não protestar       06 -> Protestar
		$detalhe['dias_protesto'] = 0; //Mínimo 3 dias
		$detalhe['valor_mora_dia'] = 2.57; //(Valor do boleto x Percentual de Juros Mora) / 30 dias -> 1547.89 * 5% = 77.39 -> 77.39/30 = 2,57 por dia
		$detalhe['data_desconto'] = ''; //DDMMAA
		$detalhe['valor_desconto'] = 0;
		$detalhe['valor_abatimento'] = 0;
		$detalhe['tipo_inscricao_pagador'] = '01'; //01 -> CPF   02 -> CNPJ   03 -> PIS/PASEP    98 -> Não Tem    99 -> Outros
		$detalhe['inscricao_pagador'] = '26577260893'; //CPF ou CNPJ do pagador
		$detalhe['nome_pagador'] = 'COCA COLA DO BRASIL SA';
		$detalhe['endereco_pagador'] = 'ROD BR 163 KM 4';
		$detalhe['bairro_pagador'] = 'ZONA RURAL';
		$detalhe['cep_pagador'] = '78300000';
		$detalhe['inscricao_cooperado'] = '111222333000144';
		$detalhe['nome_cooperado'] = 'LABORATORIOS DO BRASIL LTDA ME';
		//
		$remessa->setDetalhe($detalhe);
		//
	}
	//
	$arquivo_cnab400 = $remessa->save('78204','2302',02);
	//
	print '<a href="'.$arquivo_cnab400.'" target="new">Clique Aqui</a>';
	//
}catch (Exception $e) {
	print $e->getMessage();
}
//
?>