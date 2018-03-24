<?php
//  ****  CRIADO POR CLAUDINEI PERAZZA FERRES  **** //
//  ****  MARÇO DE 2018 ****  //
//  **** cferres@bol.com.br **** //
// Documentacao e instruções de uso https://github.com/Claudineipf/boleto

namespace Boleto\Funcoes;

class functions{
	//
	public function ajusta_str($texto, $tamanho){
		$len = strlen($texto);
		if( $len == $tamanho){
			return $texto;
		}else if($len < $tamanho){
			$diff = $tamanho - $len;
			return $texto.str_repeat(' ',$diff);
		}else if($len > $tamanho){
			return substr($texto,0,$tamanho);
		}
	}
	//
	public function preenche_str($texto, $tamanho, $preencher){
		$diff = $tamanho - strlen($texto);
		return str_repeat($preencher,$diff).$texto;
	}
	//
	public function corta_str($texto, $tamanho){
		return substr($texto,0,$tamanho);
	}
	//
	public function c_money($numero, $tamanho){
		$money = number_format($numero,2,'.','');
		$money = str_replace('.','',$money);
		$diff = $tamanho - strlen($money);
		//
		return str_repeat(0,$diff).$money;
	}
	//
	public function c_data($data){
		if($data == ''){
			$dia = '00';
			$mes = '00';
			$ano = '00';
		}else{
			$d = explode('-',$data);
			$dia = $d[2];
			$mes = $d[1];
			$ano = substr($d[0],-2);
		}
		return $dia.$mes.$ano; //DDMMAA
	}
	//
	public function c_ddmmaa($data){
		return '20'.substr($data,4,2).'-'.substr($data,2,2).'-'.substr($data,0,2);
	}
	//
	public function c_valor($valor){
		$valor = (int)substr($valor,0,11).'.'.substr($valor,-2);
		return $valor;
	}
	//
	public function modulo10($num){
		$numtotal10 = 0;
		$fator = 2;
		// Separacao dos numeros
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num, $i - 1, 1);
			// Efetua multiplicacao do numero pelo (falor 10)
			// 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
			$temp = $numeros[$i] * $fator;
			$temp0 = 0;
			foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
				$temp0 += $v;
			}
			$parcial10[$i] = $temp0; //$numeros[$i] * $fator;
			// monta sequencia para soma dos digitos no (modulo 10)
			$numtotal10 += $parcial10[$i];
			if ($fator == 2) {
				$fator = 1;
			}else{
				$fator = 2; // intercala fator de multiplicacao (modulo 10)
			}
		}
		// Calculo do modulo 10
		$resto = $numtotal10 % 10;
		$digito = 10 - $resto;
		if ($resto == 0) {
			$digito = 0;
		}
		return $digito;
	}
	//
	public function modulo11($num, $base = 9, $r = 0){
		$soma = 0;
		$fator = 2;
		/* Separacao dos numeros */
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num, $i - 1, 1);
			// Efetua multiplicacao do numero pelo falor
			$parcial[$i] = $numeros[$i] * $fator;
			// Soma dos digitos
			$soma += $parcial[$i];
			if ($fator == $base) {
				// restaura fator de multiplicacao para 2
				$fator = 1;
			}
			$fator++;
		}
		/* Calculo do modulo 11 */
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			if ($digito == 10) {
				$digito = 0;
			}
			return $digito;
		}elseif($r == 1){
			$resto = $soma % 11;
			return $resto;
		}
	}
	//
	public function FatorVencimento($data){
		$data = explode("-", $data);
		$dia = $data[2];
		$mes = $data[1];
		$ano = $data[0];
		return (abs((self::_dateToDays("1997", "10", "07")) - (self::_dateToDays($ano, $mes, $dia))));
	}
	//
	public function _dateToDays($ano, $mes, $dia){
		$periodo = substr($ano, 0, 2);
		$ano = substr($ano, 2, 2);
		if ($mes > 2) {
			$mes -= 3;
		}else{
			$mes += 9;
			if ($ano) {
				$ano--;
			}else{
				$ano = 99;
				$periodo--;
			}
		}
		return (floor((146097 * $periodo) / 4) +
			floor((1461 * $ano) / 4) +
			floor((153 * $mes + 2) / 5) +
			$dia + 1721119);
	}
	//
	public function formata_data($data){
		$data = explode("-", $data);
		$dia = $data[2];
		$mes = $data[1];
		$ano = $data[0];
		return $dia.'/'.$mes.'/'.$ano;
	}
	//
	public function formata_moeda($valor){
		return 'R$'.number_format($valor,2,',','.');
	}
	//
	public function px2milimetros($valor){
		return ((25.4 * $valor) / 96);
	}
	//
    public static function direita($entra, $comp){
		return substr($entra, strlen($entra) - $comp, $comp);
	}
}



?>