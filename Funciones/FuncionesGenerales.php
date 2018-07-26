<?php
session_cache_expire(180);
session_start();

class FuncionesGenerales{
	public $fecha = NULL;
	public $hora = NULL;
	public $date = NULL;
	public $time = NULL;
	public $ip = NULL;
	
	private $UNIDADES = array(
		'',
		'UN ',
		'DOS ',
		'TRES ',
		'CUATRO ',
		'CINCO ',
		'SEIS ',
		'SIETE ',
		'OCHO ',
		'NUEVE ',
		'DIEZ ',
		'ONCE ',
		'DOCE ',
		'TRECE ',
		'CATORCE ',
		'QUINCE ',
		'DIECISEIS ',
		'DIECISIETE ',
		'DIECIOCHO ',
		'DIECINUEVE ',
		'VEINTE '
	);
	
	private $DECENAS = array(
		'VENTI',
		'TREINTA ',
		'CUARENTA ',
		'CINCUENTA ',
		'SESENTA ',
		'SETENTA ',
		'OCHENTA ',
		'NOVENTA ',
		'CIEN '
	);
	
	private $CENTENAS = array(
		'CIENTO ',
		'DOSCIENTOS ',
		'TRESCIENTOS ',
		'CUATROCIENTOS ',
		'QUINIENTOS ',
		'SEISCIENTOS ',
		'SETECIENTOS ',
		'OCHOCIENTOS ',
		'NOVECIENTOS '
	);
	
	private $MONEDAS = array(
		array('country' => 'Colombia', 'currency' => 'COP', 'singular' => 'PESO COLOMBIANO', 'plural' => 'PESOS COLOMBIANOS', 'symbol', '$'),array('country' => 'Colombia', 'currency' => 'COP', 'singular' => 'PESO COLOMBIANO', 'plural' => 'PESOS COLOMBIANOS', 'symbol', '$'),
		array('country' => 'Venezuela', 'currency' => 'COP', 'singular' => 'PESO COLOMBIANO', 'plural' => 'PESOS COLOMBIANOS', 'symbol', '$'),
		array('country' => 'Estados Unidos', 'currency' => 'USD', 'singular' => 'DÓLAR', 'plural' => 'DÓLARES', 'symbol', 'US$'),
		array('country' => 'Europa', 'currency' => 'EUR', 'singular' => 'EURO', 'plural' => 'EUROS', 'symbol', '€'),
		array('country' => 'México', 'currency' => 'MXN', 'singular' => 'PESO MEXICANO', 'plural' => 'PESOS MEXICANOS', 'symbol', '$'),
		array('country' => 'Perú', 'currency' => 'PEN', 'singular' => 'NUEVO SOL', 'plural' => 'NUEVOS SOLES', 'symbol', 'S/'),
		array('country' => 'Reino Unido', 'currency' => 'GBP', 'singular' => 'LIBRA', 'plural' => 'LIBRAS', 'symbol', '£')
	);
	
	function __construct(){
		date_default_timezone_set('America/Caracas');
		$this->fecha= date("d/m/Y");
		$this->date= date("Y-m-d");
		$this->hora= date("h:i A");
		$this->time= date("H:i:s");
		$this->ip=$this->obtener_ip();
	}

	function verificar_sesion(){
		if (empty($_SESSION['id_usr']))
		{
		 	header("location: login/cerrar_sesion.php");
		}
	}
	
	function verificar_sesion_inicio(){
		if (!empty($_SESSION['id_usr']))
		{
			header("location: menu_usuario.php");
		}
	}
	
	
	function verificar_sesion_admin(){
		if($_SESSION["tipo_usr"]!="S")
		{
			header("location: menu_usuario.php");
		}
	}
	
	function negar_acceso_url(){
		$url_orig=$_SERVER["HTTP_REFERER"];
		if ($_SESSION['id_usr']==null){ 
			echo "<script>window.parent.location='../index.php';</script>";
			exit();
		}
		else if($url_orig==""){
				//header("Status: 404 Not Found");
			//	exit();
		}
	}
	
	function negar_acceso_url_ini(){
		$url_orig=$_SERVER["HTTP_REFERER"];
		if($url_orig==""){
				header("HTTP/1.0 404 Not Found");
				exit();
		}
	}
	
	function limpiar_info(array &$informacion){
	foreach($informacion as $indice => $valor){
			$informacion[$indice]=  preg_replace("/(\')|(\")|(\n)|(\r)|(\n\r)/","",$valor);
		}
	}
	
	function mayusculas_info(array &$informacion){
	foreach($informacion as $indice => $valor){
			$informacion[$indice]=  strtoupper(utf8_decode(trim($valor)));
		}
	}
	
	function eliminar_letras(&$info){
		$info=preg_replace("/[^0-9]/", '', $info);
	}
	
	
	function campo_vacio(&$informacion,$ruta){
	
			if(empty($informacion)){
				echo "<script language=\"JavaScript\">alert(\"No se pueden Registrar Campos Vacios!\");</script>";    
				echo "<script language=\"JavaScript\">document.location.href='$ruta';</script>";
				//	echo "<meta http-equiv=\"Refresh\" content=\"0;url=$url_redirec\">"; 
				exit();
			}
	}
	
	function negar_acceso_usuario(){
		if($_SESSION["tipo_usr"]!="S")
		{
			echo "<script languaje='javascript'>alert('No Tienes Permiso para Acceder!!')</script>";
			echo "<meta http-equiv=\"Refresh\" content=\"0;url=fondo_frame.php\">";
			exit();
		}
	}

	function comprobar_email($email){ 
    	    $mail_correcto = 0; 
    	    //compruebo unas cosas primeras 
    	    if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){ 
    	       if ((!strstr($email,"'")) && (!strstr($email,"'")) && (!strstr($email,"\\")) && (!strstr($email,"$")) && (!strstr($email," "))) { 
    	          //miro si tiene caracter . 
    	          if (substr_count($email,".")>= 1){ 
    	             //obtengo la terminacion del dominio 
    	             $term_dom = substr(strrchr ($email, '.'),1); 
    	             //compruebo que la terminación del dominio sea correcta 
    	             if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){ 
    	                //compruebo que lo de antes del dominio sea correcto 
    	                $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1); 
    	                $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1); 
    	                if ($caracter_ult != "@" && $caracter_ult != "."){ 
    	                   $mail_correcto = 1; 
    	                } 
    	             } 
    	          } 
    	       } 
    	    } 
    	    if ($mail_correcto) 
    	       return 1; 
    	    else 
    	       return 0; 
    	}

	function formato_cedula(&$cedula)
	{
		$this->eliminar_letras($cedula);
		number_format($cedula,0,'','.');
	}
	
	function formato_monto(&$monto){
		$monto=number_format($monto,4,',','.');
		return $monto;
	}
	
	function convertir_doble(&$monto){
		$monto=str_replace(".","", $monto);
		$monto=str_replace(",",".", $monto);
		$monto=floatval($monto);
	}
	
	function formato_cantidad($cantidad)
	{
		$cantidad_total=number_format($cantidad,0,'','.');
		return ($cantidad_total);
	}
	
	//////////////////////////////////////////////////////////////////////// 
	//CONVIERTE LA FECHA DE FORMATO DATE (AAAA-MM-DD) A FECHA (DD-MM-AAAA)//
	////////////////////////////////////////////////////////////////////////
	function convertir_fecha_normal($fecha){ 
	   	preg_match("/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/", $fecha, $ftemp); 
	   	$fecha_format=$ftemp[3]."/".$ftemp[2]."/".$ftemp[1]; 
	   	return $fecha_format; 
	}
	
	/////////////////////////////////////////////////////////////// 
	//CONVIERTE LA FECHA (DD-MM-AAAA) A FORMATO DATE (AAAA-MM-DD)//
	/////////////////////////////////////////////////////////////// 
	function convertir_fecha_mysql($fecha){ 
	   	preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/", $fecha, $ftemp); 
	   	$fecha_format=$ftemp[3]."-".$ftemp[2]."-".$ftemp[1]; 
	   	return $fecha_format; 
	} 
	
	//////////////////////////////////////////////////// 
	////CONVIERTE LA HORA DE FORMATO 24HRS A 12HRS)/////
	//////////////////////////////////////////////////// 
	function convertir_hora_normal($hora){ 
	    $hora = strtotime($hora);
	    $hora = date("g:i a", $hora);
	  	return $hora; 
	}

	//////////////////////////////////////////////////// 
	/////CONVIERTE LA HORA DE FORMATO 12HRS A 24HRS/////
	//////////////////////////////////////////////////// 
	function convertir_hora_mysql($hora){ 
	    $hora = strtotime($hora);
	    $hora = date("H:i:s", $hora);
	  	return $hora; 
	} 

	///////////////////////////////////////////////////////////////////////////
	//Compara dos fechas devolviendo un valor positivo, negativo o nulo si la//
	//primera fecha es respectivamente mayor, menor o igual que la segunda/////
	///////////////////////////////////////////////////////////////////////////
	function compara_fechas($fecha1,$fecha2)       
	{
		if(preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha1))
			list($dia1,$mes1,$ano1)=split("/",$fecha1);
		if(preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha1))
			list($dia1,$mes1,$ano1)=split("-",$fecha1);
	    if(preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha2))
			list($dia2,$mes2,$ano2)=split("/",$fecha2);
		if(preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha2))
			list($dia2,$mes2,$ano2)=split("-",$fecha2);				
		$dif = mktime(0,0,0,$mes1,$dia1,$ano1) - mktime(0,0,0, $mes2,$dia2,$ano2);
	    return ($dif);                         
	}

	function restarFechas($fecha_inicial,$fecha_final)
	{
		$fecha_ini=explode("-",$fecha_inicial);
		$ano_ini=$fecha_ini[0];
		$mes_ini=$fecha_ini[1];
		$dia_ini=$fecha_ini[2];
	
	  	$fecha_fin=explode("-",$fecha_final);
		$ano_fin=$fecha_fin[0];
		$mes_fin=$fecha_fin[1];
		$dia_fin=$fecha_fin[2];
		
		settype($dia_fin, "integer");
		settype($mes_fin, "integer");
		settype($ano_fin, "integer");
		
		settype($dia_ini, "integer");
		settype($mes_ini, "integer");
		settype($ano_ini, "integer");
		
		$fechaFin=mktime(0,0,0,$mes_fin,$dia_fin,$ano_fin);
		$fechaIni=mktime(0,0,0,$mes_ini,$dia_ini,$ano_ini);
		$segundos = $fechaFin - $fechaIni; 
		$total_dias = $segundos / (60 * 60 * 24); 
	
		return $total_dias;
	}

	function ordenarArregloMultidimensional ($arregloOrdenar, $campo, $inversa) {  
		    $posicion = array();  
		    $nuevaFila = array();  
			    foreach ($arregloOrdenar as $indice => $fila) {  
			            $posicion[$indice]  = $fila[$campo];  
			            $nuevaFila[$indice] = $fila;  
			    }  
		    if ($inversa) {  
		        arsort($posicion);  
			    }  
			    else {  
			        asort($posicion);  
			    }  
		    	$resultado = array();  
			    foreach ($posicion as $indice => $pos) {       
			        $resultado[] = $nuevaFila[$indice];  
			    }  
		    return $resultado;  
	}
	
	function obtener_ip(){
		if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
			return array_shift(explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]));
		else if(!empty($_SERVER["HTTP_CLIENT_IP"]))
			return $_SERVER["HTTP_CLIENT_IP"];
		else
			return $_SERVER["REMOTE_ADDR"];
	}
	
	//CONVIERTE CANTIDADES A LETRAS
	public function to_word($number, $miMoneda = null)
	{
		if ($miMoneda !== null) {
			try {
	
				$moneda = array_filter($this->MONEDAS, function($m) use ($miMoneda) {
					return ($m['currency'] == $miMoneda);
				});
	
				$moneda = array_values($moneda);
	
				if (count($moneda) <= 0) {
					throw new Exception("Tipo de moneda inválido");
					return;
				}
	
				if ($number < 2) {
					$moneda = $moneda[0]['singular'];
				} else {
					$moneda = $moneda[0]['plural'];
				}
			} catch (Exception $e) {
				echo $e->getMessage();
				return;
			}
		} else {
			$moneda = " ";
		}
	
		$converted = '';
	
		if (($number < 0) || ($number > 999999999)) {
			return 'No es posible convertir el numero a letras';
		}
	
		$numberStr = (string) $number;
		$numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
		$millones = substr($numberStrFill, 0, 3);
		$miles = substr($numberStrFill, 3, 3);
		$cientos = substr($numberStrFill, 6);
	
		if (intval($millones) > 0) {
			if ($millones == '001') {
				$converted .= 'UN MILLON ';
			} else if (intval($millones) > 0) {
				$converted .= sprintf('%sMILLONES ', $this->convertGroup($millones));
			}
		}
	
		if (intval($miles) > 0) {
			if ($miles == '001') {
				$converted .= 'MIL ';
			} else if (intval($miles) > 0) {
				$converted .= sprintf('%sMIL ', $this->convertGroup($miles));
			}
		}
	
		if (intval($cientos) > 0) {
			if ($cientos == '001') {
				$converted .= 'UN ';
			} else if (intval($cientos) > 0) {
				$converted .= sprintf('%s ', $this->convertGroup($cientos));
			}
		}
	
		$converted .= $moneda;
	
		return $converted;
	}
	
	private function convertGroup($n)
	{
		$output = '';
	
		if ($n == '100') {
			$output = "CIEN ";
		} else if ($n[0] !== '0') {
			$output = $this->CENTENAS[$n[0] - 1];
		}
	
		$k = intval(substr($n,1));
	
		if ($k <= 20) {
			$output .= $this->UNIDADES[$k];
		} else {
			if(($k > 30) && ($n[2] !== '0')) {
				$output .= sprintf('%sY %s', $this->DECENAS[intval($n[1]) - 2], $this->UNIDADES[intval($n[2])]);
			} else {
				$output .= sprintf('%s%s', $this->DECENAS[intval($n[1]) - 2], $this->UNIDADES[intval($n[2])]);
			}
		}
	
		return $output;
	}
}

?>