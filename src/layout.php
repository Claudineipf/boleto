<?php
//  ****  CRIADO POR CLAUDINEI PERAZZA FERRES  **** //
//  ****  MARÇO DE 2018 ****  //
//  **** cferres@bol.com.br **** //
// Documentacao e instruções de uso https://github.com/Claudineipf/boleto

namespace Boleto;

include('functions.php');
include('fpdf/fpdf.php');

namespace Boleto\Layout;
use \Exception;
use Boleto\Funcoes\functions as functions;

use FPDF;

class Unicred extends functions{
	private $dados = array();
	private $id_banco = '237'; //Unicred usa compensação do Bradesco atualmente
	private $codigo_moeda = '9'; // 9 => Real, 0 => Outras
	private $carteira = '09'; // 09 registro
	private $logo = '/images/bradesco.jpg';
	private $InstrucaoPagamento = 'Pagável preferencialmente na Rede Bradesco ou Bradesco Expresso';
	private $aceite = 'N';
	private $uso_banco = '00018';
	private $diretorio = '';
	//
	public function setDiretorio($diretorio){
		$this->diretorio = $diretorio;
	}
	//
	public function setBoleto($dados){
		$valid = true;
		$campos = array('valor','agencia_ficticia','dv_agencia_ficticia','nosso_numero','conta_corrente_ficticia','vencimento','numero_documento','data_documento','data_processamento','especie_documento', 'quantidade');
		for($i = 0; $i < count($campos);$i++){
			if (array_key_exists($campos[$i],$dados) == false) {
				throw new \Exception('<strong>Error:</strong> Campo faltando ao Setar Boleto, nome do campo: '.$campos[$i]);	
				$valid = false;
			}
		}
		if(strlen($dados['nosso_numero']) != 11){
			throw new \Exception('<strong>Error:</strong> Nosso número deve possuir 11 carateres!');	
			$valid = false;
		}
		if ((bool)strtotime($dados['vencimento']) == false) {
			throw new \Exception('<strong>Error:</strong> Data de vencimento inválida, usar formato YYYY-MM-DD...');	
			$valid = false;
		}
		if ((bool)strtotime($dados['data_documento']) == false) {
			throw new \Exception('<strong>Error:</strong> Data do Documento inválida, usar formato YYYY-MM-DD...');	
			$valid = false;
		}
		if ((bool)strtotime($dados['data_processamento']) == false) {
			throw new \Exception('<strong>Error:</strong> Data do Processamento inválida, usar formato YYYY-MM-DD...');	
			$valid = false;
		}
		if($valid){
			$this->dados['boleto']['valor'] = $dados['valor'];
			$this->dados['boleto']['agencia_ficticia'] = $dados['agencia_ficticia'];
			$this->dados['boleto']['dv_agencia_ficticia'] = $dados['dv_agencia_ficticia'];
			$this->dados['boleto']['nosso_numero'] = $dados['nosso_numero'];
			$this->dados['boleto']['conta_corrente_ficticia'] = $dados['conta_corrente_ficticia'];
			$this->dados['boleto']['dv_conta_corrente_ficticia'] = $dados['dv_conta_corrente_ficticia'];
			$this->dados['boleto']['vencimento'] = $dados['vencimento'];
			$this->dados['boleto']['numero_documento'] = $dados['numero_documento'];
			$this->dados['boleto']['data_documento'] = $dados['data_documento'];
			$this->dados['boleto']['data_documento'] = $dados['data_processamento'];
			$this->dados['boleto']['especie_documento'] = $dados['especie_documento'];
			$this->dados['boleto']['quantidade'] = $dados['quantidade'];		
		}
	}
	public function setBeneficiario($dados){
		$valid = true;
		$campos = array('nome','tipo_documento','documento','endereco','bairro','cidade','uf','cep');
		for($i = 0; $i < count($campos);$i++){
			if (array_key_exists($campos[$i],$dados) == false) {
				throw new \Exception('<strong>Error:</strong> Campo faltando ao Setar Beneficiário, nome do campo: '.$campos[$i]);	
				$valid = false;
			}
		}
		if($dados['tipo_documento'] != 'CPF' && $dados['tipo_documento'] != 'CNPJ'){
			throw new \Exception('<strong>Error:</strong> Tipo de documento inválido do Beneficiário!');	
			$valid = false;
		}
		if($dados['tipo_documento'] == 'CPF'){
			if(strlen($dados['documento']) != 14){
				throw new \Exception('<strong>Error:</strong> CPF do Beneficiário inválido, usar formato XXX.XXX.XXX-XX');
				$valid = false;
			}
		}
		if($dados['tipo_documento'] == 'CNPJ'){
			if(strlen($dados['documento']) != 19){
				throw new \Exception('<strong>Error:</strong> CNPJ do Beneficiário inválido, usar formato XXX.XXX.XXX/XXXX-XX');
				$valid = false;
			}
		}
		if(strlen($dados['cep']) != 10){
			throw new \Exception('<strong>Error:</strong> CEP do Beneficiário inválido, usar formato XX.XXX-XXX');
			$valid = false;
		}
		if($valid){
			$this->dados['beneficiario']['nome'] = functions::corta_str($dados['nome'],30);
			$this->dados['beneficiario']['tipo_documento'] = $dados['tipo_documento'];
			$this->dados['beneficiario']['documento'] = $dados['documento'];
			$this->dados['beneficiario']['endereco'] = functions::corta_str($dados['endereco'],30);
			$this->dados['beneficiario']['bairro'] = functions::corta_str($dados['bairro'],10);
			$this->dados['beneficiario']['cidade'] = functions::corta_str($dados['cidade'],10);
			$this->dados['beneficiario']['uf'] = $dados['uf'];
			$this->dados['beneficiario']['cep'] = $dados['cep'];
		}
	}
	public function setPagador($dados){
		$valid = true;
		$campos = array('nome','tipo_documento','documento','endereco','bairro','cidade','uf','cep');
		for($i = 0; $i < count($campos);$i++){
			if (array_key_exists($campos[$i],$dados) == false) {
				throw new \Exception('<strong>Error:</strong> Campo faltando ao Setar Pagador, nome do campo: '.$campos[$i]);	
				$valid = false;
			}
		}
		if($dados['tipo_documento'] != 'CPF' && $dados['tipo_documento'] != 'CNPJ'){
			throw new \Exception('<strong>Error:</strong> Tipo de documento inválido do Pagador!');	
			$valid = false;
		}
		if($dados['tipo_documento'] == 'CPF'){
			if(strlen($dados['documento']) != 14){
				throw new \Exception('<strong>Error:</strong> CPF do Pagador inválido, usar formato XXX.XXX.XXX-XX');
				$valid = false;
			}
		}
		if($dados['tipo_documento'] == 'CNPJ'){
			if(strlen($dados['documento']) != 19){
				throw new \Exception('<strong>Error:</strong> CNPJ do Pagador inválido, usar formato XXX.XXX.XXX/XXXX-XX');
				$valid = false;
			}
		}
		if(strlen($dados['cep']) != 10){
			throw new \Exception('<strong>Error:</strong> CEP do Pagador inválido, usar formato XX.XXX-XXX');
			$valid = false;
		}
		if($valid){
			$this->dados['pagador']['nome'] = functions::corta_str($dados['nome'],38);
			$this->dados['pagador']['tipo_documento'] = $dados['tipo_documento'];
			$this->dados['pagador']['documento'] = $dados['documento'];
			$this->dados['pagador']['endereco'] = functions::corta_str($dados['endereco'],30);
			$this->dados['pagador']['bairro'] = functions::corta_str($dados['bairro'],10);
			$this->dados['pagador']['cidade'] = functions::corta_str($dados['cidade'],10);
			$this->dados['pagador']['uf'] = $dados['uf'];
			$this->dados['pagador']['cep'] = $dados['cep'];
		}
	}
	//
	public function setSacador($dados){
		$valid = true;
		$campos = array('nome','tipo_documento','documento','endereco','bairro','cidade','uf','cep');
		for($i = 0; $i < count($campos);$i++){
			if (array_key_exists($campos[$i],$dados) == false) {
				throw new \Exception('<strong>Error:</strong> Campo faltando ao Setar Sacador, nome do campo: '.$campos[$i]);	
				$valid = false;
			}
		}
		if($dados['tipo_documento'] != 'CPF' && $dados['tipo_documento'] != 'CNPJ'){
			throw new \Exception('<strong>Error:</strong> Tipo de documento inválido do Sacador!');	
			$valid = false;
		}
		if($dados['tipo_documento'] == 'CPF'){
			if(strlen($dados['documento']) != 14){
				throw new \Exception('<strong>Error:</strong> CPF do Sacador inválido, usar formato XXX.XXX.XXX-XX');
				$valid = false;
			}
		}
		if($dados['tipo_documento'] == 'CNPJ'){
			if(strlen($dados['documento']) != 19){
				throw new \Exception('<strong>Error:</strong> CNPJ do Sacador inválido, usar formato XXX.XXX.XXX/XXXX-XX');
				$valid = false;
			}
		}
		if(strlen($dados['cep']) != 10){
			throw new \Exception('<strong>Error:</strong> CEP do Sacador inválido, usar formato XX.XXX-XXX');
			$valid = false;
		}
		if($valid){
			$this->dados['sacador']['nome'] = functions::corta_str($dados['nome'],38);
			$this->dados['sacador']['tipo_documento'] = $dados['tipo_documento'];
			$this->dados['sacador']['documento'] = $dados['documento'];
			$this->dados['sacador']['endereco'] = functions::corta_str($dados['endereco'],30);
			$this->dados['sacador']['bairro'] = functions::corta_str($dados['bairro'],10);
			$this->dados['sacador']['cidade'] = functions::corta_str($dados['cidade'],10);
			$this->dados['sacador']['uf'] = $dados['uf'];
			$this->dados['sacador']['cep'] = $dados['cep'];
		}
	}
	//
	public function setInstrucao($instrucao){
		$valid = true;
		if(isset($this->dados['boleto']['instrucoes'])){
			$i = count($this->dados['boleto']['instrucoes']);
		}else{
			$i = 0;
		}
		if($i >= 3){
			throw new \Exception('<strong>Error:</strong> São permitido no máximo 3 instruções!');	
			$valid = false;
		}
		if($valid){
			$this->dados['boleto']['instrucoes'][$i] = $instrucao;
		}
	}
	//
	public function setReferencia($instrucao){
		$valid = true;
		if(isset($this->dados['boleto']['referencias'])){
			$i = count($this->dados['boleto']['referencias']);
		}else{
			$i = 0;
		}
		if($i >= 2){
			throw new \Exception('<strong>Error:</strong> São permitido no máximo 2 referências!');	
			$valid = false;
		}
		if($valid){
			$this->dados['boleto']['referencias'][$i] = $instrucao;
		}
	}
	//
	public function geraBoleto(){
		$valid = true;
		if(!isset($this->dados['boleto'])){
			throw new \Exception('<strong>Error:</strong> Dados do boleto não setados, usar a função setBoleto!');
			$valid = false;
		}
		if(!isset($this->dados['beneficiario'])){
			throw new \Exception('<strong>Error:</strong> Dados do Beneficiário não setados, usar a função setBeneficiario!');
			$valid = false;
		}
		if(!isset($this->dados['pagador'])){
			throw new \Exception('<strong>Error:</strong> Dados do Pagador não setados, usar a função setPagador!');
			$valid = false;
		}
		if(!isset($this->dados['sacador'])){
			throw new \Exception('<strong>Error:</strong> Dados do Sacador não setados, usar a função setSacador!');
			$valid = false;
		}
		if($this->diretorio == ''){
			throw new \Exception('<strong>Error:</strong> Diretório da classe não setado, usar a função setDiretorio!');
			$valid = false;
		}
		if($valid){
		
			$PDF = new FPDF("P", 'mm', 'A4');
			$PDF->AddPage();
			$PDF->SetFont('Arial', '', 8);
			$PDF->Ln();
			//
			//Instruçoes de pagamento
			$PDF->SetFont('Arial', 'B', 6);
			$PDF->Cell(190, 4, "Instrução de Impressão:", '', 1, 'C');
			$PDF->Cell(
				190,
				4,
				"Imprimir em impressora jato de tinta (ink jet) ou laser em qualidade normal. (Não use modo econômico).",
				'',
				1,
				'C'
			);
			$PDF->Cell(
				190,
				4,
				"Utilize folha A4 (210 x 297 mm) ou carta (216 x 279 mm) - Corte na linha indicada:",
				'',
				1,
				'C'
			);
			//
			$PDF->Ln();
			$PDF->SetFont('Arial', 'B', 6);
			$PDF->Cell(190, 2, 'Recibo do Pagador', '', 1, 'R');
			$PDF->Ln();
			$PDF->Ln(15);
			$PDF->SetFont('Arial', '', 9);
			$PDF->Cell(50, 10, '', 'B', 0, 'L');
			$PDF->Image($this->diretorio.$this->logo, 10, 43, 40, 10);
			//
			$PDF->SetFont('Arial', 'B', 14);
			$PDF->Cell(20, 10, $this->getIdBandoComDV(), 'LBR', 0, 'C');
			//
			$PDF->SetFont('Arial', 'B', 9);
			$PDF->Cell(120, 10, $this->getLinhaDigitavel(), 'B', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(85, 3, 'Beneficiário: '.$this->dados['beneficiario']['nome'].' - '.$this->dados['beneficiario']['tipo_documento'].': '.$this->dados['beneficiario']['documento'], 'RL', 0, 'L');
			$PDF->Cell(30, 3, 'Agência/Código do Cedente', 'R', 0, 'L');
			$PDF->Cell(15, 3, 'Espécie', 'R', 0, 'L');
			$PDF->Cell(20, 3, 'Quantidade', 'R', 0, 'L');
			$PDF->Cell(40, 3, 'Nosso número', '', 1, 'L');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(85, 5, $this->dados['beneficiario']['endereco'].' - '.$this->dados['beneficiario']['bairro'].' - '.$this->dados['beneficiario']['cidade'].'/'.$this->dados['beneficiario']['uf'].' - '.$this->dados['beneficiario']['cep'], 'BLR', 0, 'L');
			$PDF->SetFont('Arial', '', 7);
			$PDF->Cell(30, 5,$this->dados['boleto']['agencia_ficticia'].'-'.$this->dados['boleto']['dv_agencia_ficticia']." / ".$this->dados['boleto']['conta_corrente_ficticia'].'-'.$this->dados['boleto']['dv_conta_corrente_ficticia'],'BR',0,'L');
			$PDF->Cell(15, 5, $this->codigo_moeda, 'BR', 0, 'L');
			$PDF->Cell(20, 5, '001', 'BR', 0, 'L');
			$PDF->Cell(40, 5, $this->getNossoNumeroComDV(), 'B', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(60, 3, 'Número do Documento', 'LR', 0, 'L');
			$PDF->Cell(35, 3, 'CPF/CEI/CNPJ', 'R', 0, 'L');
			$PDF->Cell(35, 3, 'Vencimento', 'R', 0, 'L');
			$PDF->Cell(60, 3, 'Valor Documento', 'L', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			$PDF->Cell(60, 5, $this->dados['boleto']['numero_documento'], 'BLR', 0, 'L');
			$PDF->Cell(35, 5, $this->dados['beneficiario']['documento'], 'BR', 0, 'L');
			$PDF->Cell(35, 5, functions::formata_data($this->dados['boleto']['vencimento']), 'BR', 0, 'L');
			$PDF->Cell(60, 5, functions::formata_moeda($this->dados['boleto']['valor']), 'B', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(33, 3, '(-)Desconto/Abatimentos', 'LR', 0, 'L');
			$PDF->Cell(32, 3, '(-)Outras deduções', 'R', 0, 'L');
			$PDF->Cell(32, 3, '(+)Mora/Multa', 'R', 0, 'L');
			$PDF->Cell(33, 3, '(+)Outros acréscimos', '', 0, 'L');
			$PDF->Cell(60, 3, '(=)Valor Cobrado', 'L', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			$PDF->Cell(33, 5, '', 'BLR', 0, 'L');
			$PDF->Cell(32, 5, '', 'BR', 0, 'L');
			$PDF->Cell(32, 5, '', 'BR', 0, 'L');
			$PDF->Cell(33, 5, '', 'BR', 0, 'L');
			$PDF->Cell(60, 5, '', 'B', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(190, 3, 'Pagador: '.$this->dados['pagador']['nome'].' - '.$this->dados['pagador']['tipo_documento'].': '.$this->dados['pagador']['documento'], 'L', 1, 'L');
			$PDF->Cell(190, 3, $this->dados['pagador']['endereco'].' - '.$this->dados['pagador']['bairro'].' - '.$this->dados['pagador']['cidade'].'/'.$this->dados['pagador']['uf'].' - '.$this->dados['pagador']['cep'], 'L', 1, 'L');
			$PDF->Cell(190, 3, '', 'L', 1, 'L');
			$PDF->Cell(190, 3, 'Sacador/Avalista: '.$this->dados['sacador']['nome'].' - '.$this->dados['sacador']['tipo_documento'].': '.$this->dados['sacador']['documento'],'L',1,'L');
			$PDF->Cell(190,3,$this->dados['sacador']['endereco'].' - '.$this->dados['sacador']['bairro'].' - '.$this->dados['sacador']['cidade'].'/'.$this->dados['sacador']['uf'].' - '.$this->dados['sacador']['cep'],'BL',1,'L');
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(190, 3, 'Autênticação Mecânica', '', 1, 'R');
			$PDF->Cell(190, 3, 'Instruções:', '', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			if(isset($this->dados['boleto']['instrucoes'])){
				foreach ($this->dados['boleto']['instrucoes'] as $instrucao) {
					$PDF->Cell(190, 4, '  '.$instrucao, '', 1, 'L');
				}
			}else{
				$PDF->Cell(190, 4, '', '', 1, 'L');
			}
			//
			$PDF->Ln(1);
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(170, 3, 'Referente a:', '', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			if(isset($this->dados['boleto']['referencias'])){
				foreach ($this->dados['boleto']['referencias'] as $referencia) {
					$PDF->Cell(190, 4, '  '.$referencia, '', 1, 'L');
				}
			}else{
				$PDF->Cell(190, 4, '', '', 1, 'L');
			}
			//
			$PDF->SetY(128);
			$PDF->SetFont('Arial', 'B', 6);
			$PDF->Cell(190, 2, 'Corte na linha pontilhada', '', 1, 'R');
			$PDF->SetFont('Arial', '', 12);
			$PDF->Cell(190,2,
				'--------------------------------------------------------------------------------------------------------------------------------------',
				'',0,'L'
	        );
			$PDF->Ln(10);
			//**********************************************************************************************************************************************
			//**********************************************************************************************************************************************
			$PDF->Cell(50, 10, '', 'B', 0, 'L');
			$PDF->Image($this->diretorio.$this->logo, 10, 138, 40, 10);
			$PDF->SetFont('Arial', 'B', 14);
			$PDF->Cell(20, 10, $this->getIdBandoComDV(), 'LBR', 0, 'C');
			$PDF->SetFont('Arial', 'B', 9);
			$PDF->Cell(120, 10, $this->getLinhaDigitavel(), 'B', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(130, 3, 'Local Pagamento', 'LR', 0, 'L');
			$PDF->Cell(60, 3, 'Vencimento', '', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			$PDF->Cell(130, 5, $this->InstrucaoPagamento, 'BLR', 0, 'L');
			$PDF->Cell(60, 5, functions::formata_data($this->dados['boleto']['vencimento']), 'B', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(130, 4, 'Beneficiário: '.$this->dados['beneficiario']['nome'].' - '.$this->dados['beneficiario']['tipo_documento'].': '.$this->dados['beneficiario']['documento'], 'LR', 0, 'L');
			$PDF->Cell(60, 3, 'Agência/Código cedente', '', 1, 'L');
        	$PDF->Cell(130, 5, $this->dados['beneficiario']['endereco'].' - '.$this->dados['beneficiario']['bairro'].' - '.$this->dados['beneficiario']['cidade'].'/'.$this->dados['beneficiario']['uf'].' - '.$this->dados['beneficiario']['cep'], 'BLR', 0, 'L');
			$PDF->SetFont('Arial', '', 7);
    	    $PDF->Cell(60,5, $this->dados['boleto']['agencia_ficticia'].'-'.$this->dados['boleto']['dv_agencia_ficticia']." / ".$this->dados['boleto']['conta_corrente_ficticia'].'-'.$this->dados['boleto']['dv_conta_corrente_ficticia'],'B',1,'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(28, 3, 'Data Documento', 'LR', 0, 'L');
			$PDF->Cell(40, 3, 'Número do Documento', 'R', 0, 'L');
        	$PDF->Cell(20, 3, 'Espécie doc.', 'R', 0, 'L');
	        $PDF->Cell(20, 3, 'Aceite', 'R', 0, 'L');
    	    $PDF->Cell(22, 3, 'Data processamento', '', 0, 'L');
        	$PDF->Cell(60, 3, 'Nosso número', 'L', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			$PDF->Cell(28, 5, $this->dados['boleto']['data_documento'], 'BLR', 0, 'L');
			$PDF->Cell(40, 5, $this->dados['boleto']['numero_documento'], 'BR', 0, 'L');
			$PDF->Cell(20, 5, $this->dados['boleto']['especie_documento'], 'BR', 0, 'L');
			$PDF->Cell(20, 5, $this->aceite, 'BR', 0, 'L');
			$PDF->Cell(22, 5, functions::formata_data($this->dados['boleto']['data_documento']), 'BR', 0, 'L');
			$PDF->Cell(60, 5, $this->getNossoNumeroComDV(), 'B', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(28, 3, 'Uso do Banco', 'LR', 0, 'L');
			$PDF->Cell(25, 3, 'Carteira', 'R', 0, 'L');
			$PDF->Cell(15, 3, 'Espécie', 'R', 0, 'L');
			$PDF->Cell(40, 3, 'Quantidade', 'R', 0, 'L');
			$PDF->Cell(22, 3, '(x)Valor', '', 0, 'L');
			$PDF->Cell(60, 3, '(=)Valor Documento', 'L', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			$PDF->Cell(28, 5, $this->uso_banco, 'BLR', 0, 'L');
			$PDF->Cell(25, 5, '0'.$this->carteira, 'BR', 0, 'L');
			$PDF->Cell(15, 5, $this->codigo_moeda, 'BR', 0, 'L');
			$PDF->Cell(40, 5, $this->dados['boleto']['quantidade'], 'BR', 0, 'L');
			$PDF->Cell(22, 5, '', 'BR', 0, 'L');
			$PDF->Cell(60, 5, self::formata_data($this->dados['boleto']['vencimento']), 'B', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
			$y = $PDF->getY();
			$PDF->Cell(130, 3, 'Instruções:', 'L', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			if(isset($this->dados['boleto']['instrucoes'])){
				foreach ($this->dados['boleto']['instrucoes'] as $instrucao) {
					$PDF->Cell(190, 4, '  '.$instrucao, '', 1, 'L');
				}
			}else{
				$PDF->Cell(190, 4, '', '', 1, 'L');
			}
			$PDF->Ln(1);
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(170, 3, 'Referente a:', '', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			if(isset($this->dados['boleto']['referencias'])){
				foreach ($this->dados['boleto']['referencias'] as $referencia) {
					$PDF->Cell(190, 4, '  '.$referencia, '', 1, 'L');
				}
			}else{
				$PDF->Cell(190, 4, '', '', 1, 'L');
			}
			//
			$PDF->setY($y);
			$PDF->Cell(130, 5, '', 'L', 0, 'L');
			$PDF->Cell(60, 3, '(-)Desconto/Abatimentos', 'L', 1, 'L');
			//
			$l = 0;
			$PDF->Cell(130, 5, '', 'L', 0, 'L');
			$PDF->Cell(60, 5, '', 'LB', 1, 'R');
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(130, 5, '', 'L', 0, 'L');
			$PDF->Cell(60, 3, '(-)Outras deduções', 'L', 1, 'L');
			$PDF->Cell(130, 5, '', 'L', 0, 'L');
			$PDF->Cell(60, 5, '', 'LB', 1, 'R');
			$PDF->SetFont('Arial', '', 6);
			$PDF->Cell(130, 5, '', 'L', 0, 'L');
			$PDF->Cell(60, 3, '(+)Mora/Multa', 'L', 1, 'L');
			//
			$PDF->SetFont('Arial', '', 7);
    	    $PDF->Cell(130, 5, '', 'L', 0, 'L');
	        $PDF->Cell(60, 5, '', 'LB', 1, 'R');
        	$PDF->Cell(130, 3, '', 'L', 0, 'L');
    	    $PDF->SetFont('Arial', '', 6);
	        $PDF->Cell(60, 3, '(+)Outros acréscimos', 'L', 1, 'L');
			//
			$PDF->SetFont('Arial', '', 7);
    	    $PDF->Cell(130, 5, '', 'L', 0, 'L');
	        $PDF->Cell(60, 5, '', 'LB', 1, 'R');
			//
			$PDF->Cell(130, 3, 'Para segunda via acesse: https://unicredmatogrosso.cobexpress.com.br/default/segunda-via', 'L', 0, 'L');
    	    $PDF->SetFont('Arial', '', 6);
	        $PDF->Cell(60, 3, '(=)Valor cobrado', 'L', 1, 'L');
        	$PDF->SetFont('Arial', '', 7);
    	    $PDF->Cell(130, 5, '', 'LB', 0, 'L');
	        $PDF->Cell(60, 5, '', 'LB', 1, 'R');
			//
			$PDF->SetFont('Arial', '', 6);
        	$PDF->Cell(190, 3, 'Pagador: ', 'L', 1, 'L');
	        $PDF->SetFont('Arial', '', 7);
    	    $PDF->Cell(190, 4,$this->dados['pagador']['nome'].' - '.$this->dados['pagador']['tipo_documento'].': '.$this->dados['pagador']['documento'], 'L', 1, 'L');
        	$PDF->Cell(190, 4, $this->dados['pagador']['endereco'].' - '.$this->dados['pagador']['bairro'],'L',1,'L');
	        $PDF->Cell(190, 4, $this->dados['pagador']['cidade'].'/'.$this->dados['pagador']['uf'].' - '.$this->dados['pagador']['cep'],'L',1,'L');
    	    $PDF->Cell(190, 4, '','BL',1,'L');
			$PDF->SetFont('Arial', '', 6);
	        $PDF->Cell(190, 3, 'Sacador/Avalista: ', 'L', 1, 'L');
			$PDF->SetFont('Arial', '', 7);
			$PDF->Cell(190, 4, $this->dados['sacador']['nome'].' - '.$this->dados['sacador']['tipo_documento'].': '.$this->dados['sacador']['documento'],'L',1,'L');
			$PDF->Cell(190, 4, $this->dados['sacador']['endereco'].' - '.$this->dados['sacador']['bairro'],'L',1,'L');
			$PDF->Cell(190, 4, $this->dados['sacador']['cidade'].'/'.$this->dados['sacador']['uf'].' - '.$this->dados['sacador']['cep'],'L',1,'L');
			$PDF->Cell(190, 4, '','BL',1,'L');
	        $PDF->Cell(190, 4, 'Autênticação Mecânica - Ficha de Compensação', '', 1, 'R');
			//
			$PDF->setY(280); // O início da barra deve estar 0,5 cm da margem esquerda da folha (zona desilêncio);
						     // O meio da barra deve estar a 12 mm do final da folha;
						     // Comprimento total igual a 103 (cento e três) mm e altura igual a 13 (treze) mm;
							 // Posição testada para leitor de codigo de barras sem necessidade de dobrar o papel
			$this->fbarcode($this->getCodigoBarras(), $PDF);
			//
			return $PDF;
		}
	}
	//
	public function getLinhaDigitavel(){
		//Link para validar Linha Digitavel Unicred => https://www.cobexpress.com.br/validalinhadigitavel.php
		//23793.38102 91230.200015 48107.820408 9 74720000163100
		$codigo_barras = $this->getCodigoBarras();
		//Campo 1 => Composto pelo código de Banco, código da moeda, as cinco primeiras posições do campo livre e o dígito verificador deste campo;
		//Campo 2 => Composto pelas posições 6ª a 15ª do campo livre e o dígito verificador deste campo;
		//Campo 3 => Composto pelas posições 16ª a 25ª do campo livre e o dígito verificador deste campo;
		//Campo 4 => Composto pelo dígito verificador do código de barras, ou seja, a 5ª posição do código de barras;
		//Campo 5 => Composto pelo fator de vencimento com 4(quatro) caracteres e o valor do documento com 10(dez) caracteres, sem separadores e sem edição
		$p1 = substr($codigo_barras, 0, 4);
		$p2 = substr($codigo_barras, 19, 5);
		$p3 = functions::modulo10($p1.$p2);
		$p4 = $p1.$p2.$p3;
		$p5 = substr($p4, 0, 5);
		$p6 = substr($p4, 5);
		$campo1 = $p5.'.'.$p6;
		//
		$p1 = substr($codigo_barras, 24, 10);
		$p2 = functions::modulo10($p1);
		$p3 = $p1.$p2;
		$p4 = substr($p3, 0, 5);
		$p5 = substr($p3, 5);
		$campo2 = $p4.'.'.$p5;
		//
		$p1 = substr($codigo_barras, 34, 10);
		$p2 = functions::modulo10($p1);
		$p3 = $p1.$p2;
		$p4 = substr($p3, 0, 5);
		$p5 = substr($p3, 5);
		$campo3 = $p4.'.'.$p5;
		//
		$campo4 = substr($codigo_barras, 4, 1);
		//
		$p1 = substr($codigo_barras, 5, 4);
		$p2 = substr($codigo_barras, 9, 10);
		$campo5 = $p1.$p2;

		return $campo1.' '.$campo2.' '.$campo3.' '.$campo4.' '.$campo5;
	}
	//
	private function getCodigoBarras(){
		//237 9 9 7472 0000163100 3381 09 12302000148 1078204 0
		//001 a 003 -> 3 => Identificacao do banco
		//004 a 004 -> 1 => Código da Moeda (Real => 9; Outras =>0)
		//005 a 005 -> 1 => Digito Verificador Codigo Barras
		//006 a 009 -> 4 => Fator de Vencimento
		//010 a 019 -> 10 => Valor
		//020 a 023 -> 4 => Agencia Beneficiaria Ficticia sem o digito verificador, completar com zeros a esquerda, pegar em boleto já criado
		//024 a 025 -> 2 => Carteira
		//026 a 036 -> 11 => Nosso Numero sem o digito
		//037 a 043 -> 7 => Conta do beneficiário ficticia, sem o digito verificador, 10+Conta Corrente Cooperado com dv+dv, pegar em boleto já criado
		//044 a 044 -> 1 => Zero
		$codigo_barras = $this->id_banco.$this->codigo_moeda.
						 $this->getFatorVencimento().
						 functions::c_money($this->dados['boleto']['valor'],10).
						 $this->dados['boleto']['agencia_ficticia'].
						 $this->carteira.
						 $this->dados['boleto']['nosso_numero'].
						 $this->dados['boleto']['conta_corrente_ficticia'].
						 '0';
		$dv = $this->getDVCodigoBarras($codigo_barras);
		//
		return substr($codigo_barras,0,4).$dv.substr($codigo_barras,4,39);
	}
	//
	public function fbarcode($valor, FPDF $PDF){
		//
		$fino = functions::px2milimetros(1); // valores em px
		$largo = functions::px2milimetros(2.3); // valor em px
		$altura = functions::px2milimetros(50); // valor em px
		//
		$barcodes[0] = "00110";
		$barcodes[1] = "10001";
		$barcodes[2] = "01001";
		$barcodes[3] = "11000";
		$barcodes[4] = "00101";
		$barcodes[5] = "10100";
		$barcodes[6] = "01100";
		$barcodes[7] = "00011";
		$barcodes[8] = "10010";
		$barcodes[9] = "01010";
		//
		for ($f1 = 9; $f1 >= 0; $f1--) {
			for ($f2 = 9; $f2 >= 0; $f2--) {
				$f = ($f1 * 10) + $f2;
				$texto = "";
				for ($i = 1; $i < 6; $i++) {
					$texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
				}
				$barcodes[$f] = $texto;
			}
		}

		// Guarda inicial
		$PDF->Image($this->diretorio.'/images/p.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
		$PDF->SetX($PDF->GetX() + $fino);
		$PDF->Image($this->diretorio.'/images/b.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
		$PDF->SetX($PDF->GetX() + $fino);
		$PDF->Image($this->diretorio.'/images/p.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
		$PDF->SetX($PDF->GetX() + $fino);
		$PDF->Image($this->diretorio.'/images/b.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
		$PDF->SetX($PDF->GetX() + $fino);
		//
		$texto = $valor;
		if ((strlen($texto) % 2) <> 0) {
			$texto = "0" . $texto;
		}
		//
		while (strlen($texto) > 0) {
			$i = round(substr($texto, 0, 2));
			$texto = substr($texto, strlen($texto) - (strlen($texto) - 2), (strlen($texto) - 2));
			$f = $barcodes[$i];
			for ($i = 1; $i < 11; $i += 2) {
				if (substr($f, ($i - 1), 1) == "0") {
					$f1 = $fino;
				}else{
					$f1 = $largo;
				}
				$PDF->Image($this->diretorio.'/images/p.png', $PDF->GetX(), $PDF->GetY(), $f1, $altura);
				$PDF->SetX($PDF->GetX() + $f1);
				if (substr($f, $i, 1) == "0") {
					$f2 = $fino;
				}else{
					$f2 = $largo;
				}
				$PDF->Image($this->diretorio.'/images/b.png', $PDF->GetX(), $PDF->GetY(), $f2, $altura);
				$PDF->SetX($PDF->GetX() + $f2);
			}
		}
		// Draw guarda final
		$PDF->Image($this->diretorio.'/images/p.png', $PDF->GetX(), $PDF->GetY(), $largo, $altura);
		$PDF->SetX($PDF->GetX() + $largo);
		$PDF->Image($this->diretorio.'/images/b.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
		$PDF->SetX($PDF->GetX() + $fino);
		$PDF->Image($this->diretorio.'/images/p.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
		$PDF->SetX($PDF->GetX() + $fino);
		$PDF->Image($this->diretorio.'/images/b.png', $PDF->GetX(),	$PDF->GetY(), functions::px2milimetros(1), $altura);
		$PDF->SetX($PDF->GetX() + functions::px2milimetros(1));
	}
	//
	private function getDVCodigoBarras($codigo_barras){
		$resto2 = functions::modulo11($codigo_barras, 9, 1);
        if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
            $dv = 1;
        } else {
            $dv = 11 - $resto2;
        }

        return $dv;
	}
	//
	public function getFatorVencimento(){
		return functions::FatorVencimento($this->dados['boleto']['vencimento']);
	}
	//
	private function getNossoNumeroComDV(){
		$nosso_numero = $this->dados['boleto']['nosso_numero'];
		$dv = self::getDVNossonumero($nosso_numero);
		return '0'.$this->carteira.'/'.$nosso_numero.'-'.$dv;
	}
	//
	private function getDVNossonumero($nosso_numero){
        $resto2 = functions::modulo11($this->carteira.$nosso_numero, 7, 1);
        $digito = 11 - $resto2;
        if ($digito == 10) {
            $dv = "P";
        } elseif ($digito == 11) {
            $dv = 0;
        } else {
            $dv = $digito;
        }
        return $dv;
    }
	//
	public function getIdBandoComDV(){
		$dv = functions::modulo11($this->id_banco);
		return $this->id_banco.'-'.$dv;
	}
}



?>