<?php
header('Content-Type: application/json');

// Inicializo variables
$etapa_id = 0 ;
$etapa_texto = '' ;
$turno_jugador = '' ;
$partida_id = 0 ;
$contenedor_id = 0 ;
$interrupcion = false;
$interrupcion_titulo = '';
$interrupcion_contenido = '';
$interrupcion_boton_cancelar = false;


// Obtengo valores GET
if(isset($_GET['partida_id'])){
	$partida_id = $_GET['partida_id'] ;
}

require_once('bdd_coneccion.inc.php'); 

if(isset($_GET['contenedor_id'])){
	$contenedor_id = $_GET['contenedor_id'] ;
	
	/*
	require_once('phpquery/partidas_cartas.php');
	$partidas_cartas = new partidas_cartas();
	$partidas_cartas->partida_id = $partida_id;
	$partidas_cartas->carta_id = 1;
	echo "partida_id:".$partidas_cartas->partida_id ;  
	echo "carta_id:".$partidas_cartas->carta_id ;  
	 
	$partidas_cartas->Leer();
	echo "vida:".$partidas_cartas->vida ; 
	$partidas_cartas->vida = 58 ;
	$mysqli->query("BEGIN");
	$partidas_cartas->Guardar();
	//$mysqli->commit();
	//$mysqli->rollback();
	$mysqli->query("ROLLBACK");  
	$partidas_cartas->Leer();
	echo "vida:".$partidas_cartas->vida ; 
	*/
	 
	require_once('phpquery/partidas_eventos.php');
	$partidas_eventos = new partidas_eventos($mysqli);
	$partidas_eventos->partida_id = $partida_id;
	$partidas_eventos->uid = uniqid();
	$partidas_eventos->contenedor_id = $contenedor_id;
	$partidas_eventos->execute() ;
	$partidas_eventos->commit() ; 
	unset($partidas_eventos); 
} 

require_once('phpquery/clsEtapas.php');
$usuario_id = 1 ;
$clsEtapas = new clsEtapas(
	$partida_id,
	$usuario_id);
	
$clsEtapas->ProcesarEtapa($contenedor_id);

$arr = array(
	'etapa_id' => $clsEtapas->Etapa()
	, 'etapa_texto' => $clsEtapas->EtapaDescripcion()
	, 'turno_jugador' => $clsEtapas->TurnoJugador()
	, 'interrupcion' => $clsEtapas->Interrupcion()
	, 'interrupcion_titulo' => $clsEtapas->Interrupcion_titulo()
	, 'interrupcion_contenido' => $clsEtapas->Interrupcion_contenido()
	, 'interrupcion_boton_cancelar' => $clsEtapas->Interrupcion_boton_cancelar()
	); 
echo json_encode($arr); 
?>