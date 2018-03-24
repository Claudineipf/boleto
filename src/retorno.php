<?php
//  ****  CRIADO POR CLAUDINEI PERAZZA FERRES  **** //
//  ****  MARÇO DE 2018 ****  //
//  **** cferres@bol.com.br **** //
// Documentacao e instruções de uso https://github.com/Claudineipf/boleto

namespace Boleto;

include('functions.php');

namespace Boleto\Retorno;
use \Exception;
use Boleto\Funcoes\functions as functions;

class Unicred extends functions{
	private $filename;
	private $retorno = array();
	//
	public function setFileRetorno($file){
		$this->filename = $file;
	}
	//
	public function getRetorno(){
		$linhas = file($this->filename);
		//
        foreach ($linhas as $numLn => $linha) {
			$id_registro = substr($linha,0,1);
			if($id_registro == 0){
				$this->getHeader($linha);
			}else if($id_registro == 1){
				$this->getDetalhe($linha);
			}
		}
		//
		return $this->retorno;
	}
	//
	public function getHeader($linha){
		$literal_retorno = substr($linha,2,7);
		if($literal_retorno != 'RETORNO'){
			throw new \Exception('<strong>Error:</strong> Arquivo retorno inválido!');
		}
	}
	//
	public function getDetalhe($linha){
		$ocorrencia = array(2 => 'Entrada Confirmada', 3 => 'Entrada Confirmada', 6 => 'Liquidação Normal', 9 => 'Baixado via Arquivo', 10 => 'Baixado conforme instrução da Agência', 12 => 'Abatimento Concedido', 13 => 'Abatimento Cancelado', 14 => 'Vencimento Alterado', 15 => 'Liquidação em Cartório', 19 => 'Confirmação Recebimento Instrução Protesto', 20 => 'Confirmação Recebimento Instrução Sustação de Protesto', 21 => 'Confirma Recebimento de Instrução de Não Protestar', 24 => 'Entrada rejeitada por CEP Irregular', 27 => 'Baixa Rejeitada', 30 => 'Alteração de Outros Dados Rejeitados', 32 => 'Instrução Rejeitada', 33 => 'Confirmação Pedido Alteração Outros Dados');
		//
		if(isset($this->retorno)){
			$i = count($this->retorno);
		}else{
			$i = 0;
		}
		$this->retorno[$i]['id_ocorrencia'] = substr($linha,108,2);
		$id_ocorrencia = (int)substr($linha,108,2);
		$this->retorno[$i]['ocorrencia'] = $ocorrencia[$id_ocorrencia];
		$this->retorno[$i]['data_ocorrencia'] = functions::c_ddmmaa(substr($linha,110,6));
		$this->retorno[$i]['nosso_numero'] = substr($linha,126,20);
		$this->retorno[$i]['vencimento'] = functions::c_ddmmaa(substr($linha,146,6));
		$this->retorno[$i]['valor_titulo'] = functions::c_valor(substr($linha,152,13));
		$this->retorno[$i]['despesas_cobranca'] = functions::c_valor(substr($linha,175,13));
		$this->retorno[$i]['abatimento'] = functions::c_valor(substr($linha,227,13));
		$this->retorno[$i]['desconto'] = functions::c_valor(substr($linha,240,13));
		$this->retorno[$i]['valor_pago'] = functions::c_valor(substr($linha,253,13));
		$this->retorno[$i]['juros'] = functions::c_valor(substr($linha,266,13));
		$this->retorno[$i]['data_credito'] = functions::c_ddmmaa(substr($linha,295,6));
	}
}
?>