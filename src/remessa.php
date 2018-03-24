<?php
//  ****  CRIADO POR CLAUDINEI PERAZZA FERRES  **** //
//  ****  MARÇO DE 2018 ****  //
//  **** cferres@bol.com.br **** //
namespace Boleto;

include('functions.php');

namespace Boleto\Remessa;
use \Exception;
use Boleto\Funcoes\functions as functions;

class Unicred extends functions{
	private $remessa = array();
	private $cr = "\r\n";
	private $numero_sequencia_registro = 0;
	private $linha_detalhe = 0;
	private $diretorio_salvar;
	//
	public function setDiretorio($directory){
		$this->diretorio_salvar = $directory;
	}
	//
	public function setHeader($header){
		$valid = true;
		$campos = array('codigo_empresa','nome_da_empresa','data_gravacao_arquivo','numero_sequencial_arquivo');
		for($i = 0; $i < count($campos);$i++){
			if (array_key_exists($campos[$i],$header) == false) {
				throw new \Exception('<strong>Error:</strong> Campo faltando ao Setar Header, nome do campo: '.$campos[$i]);
				$valid = false;
			}
		}
		if($valid){
			$this->numero_sequencia_registro += 1;
			$this->remessa['header']['id_registro'] = '0'; //001 a 001 -> 1
			$this->remessa['header']['id_arquivo_remessa'] = '1'; //002 a 002 -> 1
			$this->remessa['header']['literal_remessa'] = 'REMESSA'; //003 a 009 -> 7
			$this->remessa['header']['codigo_servico'] = '01'; //010 a 011 -> 2
			$this->remessa['header']['literal_servico'] = functions::ajusta_str('COBRANCA',15,' '); //012 a 026 -> 15
			$this->remessa['header']['codigo_empresa'] = functions::preenche_str($header['codigo_empresa'],20,'0'); //027 a 046 -> 20  '10' + nº conta da unicred 
			$this->remessa['header']['nome_da_empresa'] = functions::ajusta_str($header['nome_da_empresa'],30); //047 a 076 -> 30
			$this->remessa['header']['numero_banco'] = '237'; //077 a 079 -> 3  Numero do Banco Bradesco usado pela unicred para fazer compensação
			$this->remessa['header']['nome_banco'] = functions::ajusta_str('BRADESCO',15); //080 a 094 -> 15
			$this->remessa['header']['data_gravacao_arquivo'] = $header['data_gravacao_arquivo']; //095 a 100 -> 6    DDMMAA
			$this->remessa['header']['branco1'] = str_repeat(' ',8); //101 a 107 -> 8
			$this->remessa['header']['identificacao_sistema'] = 'MX'; //108 a 110 -> 2
			$this->remessa['header']['numero_sequencial_arquivo'] = functions::preenche_str($header['numero_sequencial_arquivo'],7,0); //111 a 117 -> 7
			$this->remessa['header']['branco2'] = str_repeat(' ',277); //118 a 394 -> 277
			$this->remessa['header']['numero_sequencial_registro'] = functions::preenche_str($this->numero_sequencia_registro,6,'0'); //395 a 400 -> 6
		}
		//
		$linha_header = '';
		foreach ($this->remessa['header'] as $h) {
			$linha_header .= $h;
		}
		if (strlen($linha_header) != 400) {
			unset($this->remessa['header']);
			throw new \Exception('<strong>Error:</strong> Linha do Header acima de 400 caracteres (CNAB400), verificar funcão setHeader');	
			$valid = false;
		}
	}
	//
	private function getHeader(){
		$linha_header = '';
		foreach ($this->remessa['header'] as $h) {
			$linha_header .= $h;
		}
		$linha_header .= $this->cr;
	}
	//
	public function setDetalhe($detalhe){
		$valid = true;
		$campos = array('codigo_agencia_ficticia','conta_corrente_ficticia','digito_corrente','controle_participante','indicacao_multa','percentual_multa','nosso_numero_dv','desconto_bonificacao','tipo_impressao','id_operacao_banco','numero_documento','data_vencimento','valor_titulo','especie_titulo','data_emissao','indicacao_protesto','dias_protesto','valor_mora_dia','data_desconto','valor_desconto','valor_abatimento','tipo_inscricao_pagador','inscricao_pagador','nome_pagador','endereco_pagador','bairro_pagador','cep_pagador','inscricao_cooperado','nome_cooperado');
		for($i = 0; $i < count($campos);$i++){
			if (array_key_exists($campos[$i],$detalhe) == false) {
				throw new \Exception('<strong>Error:</strong> Campo faltando ao Setar Detalhe, nome do campo: '.$campos[$i]);	
				$valid = false;
			}
		}
		if($valid){
			$i = $this->linha_detalhe;
			$this->numero_sequencia_registro += 1;
			//
			$this->remessa['detalhe'][$i]['id_registro'] = '1'; //001 a 001 -> 1
			$this->remessa['detalhe'][$i]['agencia_cedente'] = '00000'; //002 a 006 -> 5  Não precisa informar, somente a ficticia
			$this->remessa['detalhe'][$i]['digito_agencia'] = '0'; //007 a 007 -> 1   Não precisa informar, somente a ficticia
			$this->remessa['detalhe'][$i]['conta_corrente'] = functions::preenche_str('0',12,0); //008 a 019 -> 12   Não precisa informar, somente a ficticia
			$this->remessa['detalhe'][$i]['digito_conta'] = '0'; //020 a 020 -> 1   Não precisa informar, somente a ficticia
			$this->remessa['detalhe'][$i]['zero1'] = '0'; //021 a 021 -> 1
			$this->remessa['detalhe'][$i]['codigo_carteira'] = '009'; //022 a 024 -> 3  Somente registrado agora fixar 009
			$this->remessa['detalhe'][$i]['codigo_agencia_ficticia'] = functions::preenche_str($detalhe['codigo_agencia_ficticia'],5,0); //025 a 029 -> 5
			$this->remessa['detalhe'][$i]['conta_corrente_ficticia'] = functions::preenche_str($detalhe['conta_corrente_ficticia'],7,0); //030 a 036 -> 7
			$this->remessa['detalhe'][$i]['digito_corrente'] = '0'; //037 a 037 -> 1
			$this->remessa['detalhe'][$i]['controle_participante'] = functions::preenche_str($detalhe['controle_participante'],25,0); //038 a 062 -> 25
			$this->remessa['detalhe'][$i]['codigo_banco'] = '237'; //063 a 065 -> 3
			$this->remessa['detalhe'][$i]['indicacao_multa'] = $detalhe['indicacao_multa']; //066 a 066 -> 1
			$this->remessa['detalhe'][$i]['percentual_multa'] = functions::c_money($detalhe['percentual_multa'],4); //067 a 070 -> 4
			$this->remessa['detalhe'][$i]['nosso_numero_dv'] = $detalhe['nosso_numero_dv']; //071 a 082 -> 12
			$this->remessa['detalhe'][$i]['desconto_bonificacao'] = functions::c_money($detalhe['desconto_bonificacao'],10); //083 a 092 -> 10
			$this->remessa['detalhe'][$i]['tipo_impressao'] = $detalhe['tipo_impressao']; //093 a 093 -> 1
			$this->remessa['detalhe'][$i]['filler1'] = str_repeat(' ',1); //94 a 94 -> 1
			$this->remessa['detalhe'][$i]['id_operacao_banco'] = functions::preenche_str($detalhe['id_operacao_banco'],10,0); //095 a 104 -> 10
			$this->remessa['detalhe'][$i]['filler2'] = str_repeat(' ',1); //105 a 105 -> 1
			$this->remessa['detalhe'][$i]['zero2'] = '0'; //106 a 106 -> 1
			$this->remessa['detalhe'][$i]['branco1'] = str_repeat(' ',2); //107 a 108 -> 2
			$this->remessa['detalhe'][$i]['id_ocorrencia'] = '01'; //109 a 110 -> 2
			$this->remessa['detalhe'][$i]['numero_documento'] = functions::preenche_str($detalhe['numero_documento'],10,'0'); //111 a 120 -> 10
			$this->remessa['detalhe'][$i]['data_vencimento'] = functions::c_data($detalhe['data_vencimento']); //121 a 126 -> 6
			$this->remessa['detalhe'][$i]['valor_titulo'] = functions::c_money($detalhe['valor_titulo'],13); //127 a 139 -> 13
			$this->remessa['detalhe'][$i]['filler3'] = str_repeat(' ',3); //140 a 142 -> 3
			$this->remessa['detalhe'][$i]['agencia_depositaria'] = str_repeat('0',5); //143 a 147 -> 5  Não usado preencher com zeros
			$this->remessa['detalhe'][$i]['especie_titulo'] = $detalhe['especie_titulo']; //148 a 149 -> 2
			$this->remessa['detalhe'][$i]['aceite'] = 'N'; //150 a 150 -> 1  Usar sempre N
			$this->remessa['detalhe'][$i]['data_emissao'] = functions::c_data($detalhe['data_emissao']); //151 a 156 -> 6
			$this->remessa['detalhe'][$i]['indicacao_protesto'] = $detalhe['indicacao_protesto']; //157 a 158 -> 2
			$this->remessa['detalhe'][$i]['dias_protesto'] = functions::preenche_str($detalhe['dias_protesto'],2,0); //159 a 160 -> 2
			$this->remessa['detalhe'][$i]['valor_mora_dia'] = functions::c_money($detalhe['valor_mora_dia'],13); //161 a 173 -> 13
			$this->remessa['detalhe'][$i]['data_desconto'] = functions::c_data($detalhe['data_desconto']); //174 a 179 -> 6
			$this->remessa['detalhe'][$i]['valor_desconto'] = functions::c_money($detalhe['valor_desconto'],13); //180 a 192 -> 13
			$this->remessa['detalhe'][$i]['zero3'] = str_repeat('0',13); //193 a 205 -> 13
			$this->remessa['detalhe'][$i]['valor_abatimento'] = functions::c_money($detalhe['valor_abatimento'],13); //206 a 218 -> 13
			$this->remessa['detalhe'][$i]['tipo_inscricao_pagador'] = $detalhe['tipo_inscricao_pagador']; //219 a 220 -> 2
			$this->remessa['detalhe'][$i]['inscricao_pagador'] = functions::preenche_str($detalhe['inscricao_pagador'],14,'0'); //221 a 234 -> 14
			$this->remessa['detalhe'][$i]['nome_pagador'] = functions::ajusta_str($detalhe['nome_pagador'],40); //235 a 274 -> 40
			$this->remessa['detalhe'][$i]['endereco_pagador'] = functions::ajusta_str($detalhe['endereco_pagador'],40); //275 a 314 -> 40
			$this->remessa['detalhe'][$i]['bairro_pagador'] = functions::ajusta_str($detalhe['bairro_pagador'],12); //315 a 326 -> 12
			$this->remessa['detalhe'][$i]['cep_pagador'] = $detalhe['cep_pagador']; //327 a 334 -> 8
			$this->remessa['detalhe'][$i]['inscricao_cooperado'] = $detalhe['inscricao_cooperado']; //335 a 349 -> 15
			$this->remessa['detalhe'][$i]['branco2'] = str_repeat('0',2); //350 a 351 -> 2
			$this->remessa['detalhe'][$i]['nome_cooperado'] = functions::ajusta_str($detalhe['nome_cooperado'],43); //352 a 394 -> 43
			$this->remessa['detalhe'][$i]['numero_sequencial_registro'] = functions::preenche_str($this->numero_sequencia_registro,6,'0'); //395 a 400 -> 6
			//
			$i++;
			$this->linha_detalhe = $i;
		}
		//
		$linha_detalhe = '';
		foreach ($this->remessa['detalhe'][$i-1] as $d) {
			$linha_detalhe .= $d;
		}
		if (strlen($linha_detalhe) != 400) {
			unset($this->remessa['detalhe'][$i]);
			throw new \Exception('<strong>Error:</strong> Linha do Detalhe nº '.$i.' acima de 400 caracteres (CNAB400), verificar funcão setDetalhe');	
			$valid = false;
		}
	}
	//
	private function getDetalhe(){
		$linha_detalhe = '';
		$linha = '';
		$i = 0;
		foreach ($this->remessa['detalhe'] as $array => $value) {
			$i++;
			foreach ($value as $data) {	
				$linha .= $data;
			}
			$linha_detalhe .= $linha.$this->cr;
			$linha = '';
		}
		return $linha_detalhe;
	}
	//
	private function getTrailler(){
		$this->numero_sequencia_registro += 1;
		return '9'.str_repeat(' ',393).functions::preenche_str($this->numero_sequencia_registro,6,'0');
	}
	//
	public function save($codigo_cedente, $codigo_cooperativa, $sequencial){
		$filename = $this->diretorio_salvar.'R400'.$codigo_cedente.$codigo_cooperativa.date('dmY').$sequencial.'.rem';
		$cnab400 = $this->getHeader().$this->getDetalhe().$this->getTrailler();	
		file_put_contents($filename, $cnab400);
		return $filename;
	}
}
?>