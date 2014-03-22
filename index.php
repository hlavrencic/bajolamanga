<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="carta.js"></script>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="carta.css" media="screen" />
</head>
<body>
	<div id="dialog-confirm" title="Empty the recycle bin?" style="display:none">
		<p><div id="dialog-confirm-text">These items will be permanently deleted and cannot be recovered. Are you sure?</div></p>
	</div> 
	
	<!-- DEPOSITO DE CARTAS -->
	<div id="deposito_cartas" style="display:none;"> 
		<div id="carta_oculta_bak" >
			<div class="carta_oculta" >
				
			</div>	
		</div>
		<div id="carta_fallo_bak" >
			<div class="carta_fallo" onclick="cargarCarta($(this).parent());" > 
				RECARGAR
			</div>	
		</div>
		<div id="carta_cargando_bak" >
			<div class="carta_cargando">
				Cargando...
			</div>	
		</div>
		<div id="carta_normal_bak" >
			<div class="carta_normal" onmouseover="cargarVisualizador($(this))" onmouseout="ocultarVisualizador()" ondblclick="seleccionarCarta($(this))"  >
				<img class="imagen" src="" width="100%"/>
				<div class="vida">0</div> 
				<div class="ataque1" style="display:none">0</div>
				<div class="ataque2" style="display:none">0</div>
				<div class="ataque3" style="display:none">0</div>
				<div class="efecto_id" style="display:none">0</div>
				<div class="efecto_texto" style="display:none">0</div>
				<div class="soporte_id" style="display:none">0</div>
				<div class="soporte_texto" style="display:none" >0</div> 
				<div class="especialidad" style="display:none" >0</div>
				<div class="tipo" style="display:none" >0</div>
			</div>
		</div>		
	</div>
	<!-- DEPOSITO DE CARTAS : FIN -->
	
	<!-- ETAPA -->
	<table width="100%" id="visor_etapa" class="" onclick="CargarEstado();">
		<tr>
		<td id="etapa_id" >0</td>
		<td id="etapa_texto" > - </td>
		<td > - </td>
		<td id="turno_jugador" > - </td> 
		<td id="estado_actualizacion" ></td>  
		</tr>
	</table>
	<!-- ETAPA: FIN -->	
	
	<!-- VISUALIZADOR -->
	<div id="visualizador_cartas">
		<table width="100%">
			<tr>
				<td width="30%">
					<div class="especialidad"  >0</div>
					<div class="tipo"  >0</div>
					<img class="imagen" src="" width="100%" />
				</td>
				<td width="30%">
					<table width="100%">
						<tr>
							<td>VIDA</td>
							<td class="vida"></td>
						</tr>
						<tr>
							<td>ATAQUE 1</td>
							<td class="ataque1"></td>
						</tr>			
						<tr>
							<td>ATAQUE 2</td>
							<td class="ataque2"></td>
						</tr>
						<tr>
							<td>ATAQUE 3</td>
							<td class="ataque3"></td>
						</tr>
						<tr>
							<td>EFECTO</td>
							<td class="efecto_texto"></td>
						</tr>						
					</table>					
				</td>
				<td width="40%" valign="top">
					<b>SOPORTE</b>
					<p class="soporte_texto"></p>
				</td>
			</tr>
		</table>	
	</div>
	
	<!-- VISUALIZADOR : FIN -->
	
	<?php 
		$cont_contenedor_id = 1;
		$px_separacion_evolucion_top = 30 ; 
		$px_evolucion_topA = 10 ; 
		$px_evolucion_topB = 300 ; 
	?>
	
	<span class="jugador1">	
		<span class="cartas_mano" > 
			<div class="contenedor mano1" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
			<div class="contenedor mano2" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
			<div class="contenedor mano3" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>	
			<div class="contenedor mano4" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
		</span>
		<span class="carta_activa"  >
			<div class="contenedor"  contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
		</span>
		<span class="carta_soporte" >
			<div class="contenedor"  contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
		</span>		
		<span class="carta_evolucion" >
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topA ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topA += $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topA += $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topA += $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topA += $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topA += $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topA += $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topA += $px_separacion_evolucion_top ?>px"></div>		
		</span>

	</span>
	<span class="jugador2">
		<span class="cartas_mano" >
			<div class="contenedor mano1" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
			<div class="contenedor mano2" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
			<div class="contenedor mano3" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
			<div class="contenedor mano4" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
		</span>
		<span class="carta_activa" >
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
		</span>
		<span class="carta_soporte" >
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" ></div>
		</span>		
		<span class="carta_evolucion" >
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topB ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topB -= $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topB -= $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topB -= $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topB -= $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topB -= $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topB -= $px_separacion_evolucion_top ?>px"></div>
			<div class="contenedor" contenedor_id="<?php echo $cont_contenedor_id++ ; ?>" style=" top:<?php echo $px_evolucion_topB -= $px_separacion_evolucion_top ?>px"></div>		
		</span>

	</span>
	<script>
			try{

				var partida_id = 1;
				
				var $cartas = $("div.contenedor") ;
				var $contenedores = $("div.contenedores") ;
								
				var $jugadorA = $("span.jugador1") ;
				var $cartas_jugadorA = $jugadorA.find("div.contenedor") ;
				var $cartas_jugadorA_mano = $jugadorA.find(".cartas_mano div.contenedor") ;
				var $cartas_jugadorA_activa = $jugadorA.find(".carta_activa div.contenedor") ;
				var $cartas_jugadorA_soporte = $jugadorA.find(".carta_soporte div.contenedor") ;
				var $cartas_jugadorA_evolucion = $jugadorA.find(".carta_evolucion div.contenedor") ;

				var $jugadorB = $("span.jugador2") ;
				var $cartas_jugadorB = $jugadorB.find("div.contenedor") ;
				var $cartas_jugadorB_mano = $jugadorB.find(".cartas_mano div.contenedor") ;
				var $cartas_jugadorB_activa = $jugadorB.find(".carta_activa div.contenedor") ;
				var $cartas_jugadorB_soporte = $jugadorB.find(".carta_soporte div.contenedor") ;
				var $cartas_jugadorB_evolucion = $jugadorB.find(".carta_evolucion div.contenedor") ;
				
				
				var $jugador = new Array();
				var $contenedores = new Array();
				var $contenedores_mano = new Array();
				var $contenedores_activa = new Array();
				var $contenedores_soporte = new Array();
				var $contenedores_evolucion = new Array(); 
				
				$jugador[1] = $("span.jugador1") ;
				$jugador[2] = $("span.jugador2") ;  
				
				for(i=1;i<=2;i++){
					$contenedores[i] = $jugador[i].find("div.contenedor") ;
					$contenedores_mano[i] = $jugador[i].find(".cartas_mano div.contenedor") ;
					$contenedores_activa[i] = $jugador[i].find(".carta_activa div.contenedor") ;
					$contenedores_soporte[i] = $jugador[i].find(".carta_soporte div.contenedor") ;
					$contenedores_evolucion[i] = $jugador[i].find(".carta_evolucion div.contenedor") ;			
				}
								
				var timeoutCargarEstado ;
				
				$(document).ready(function(){
					CargarEstado();
					
					//Interrupcion("tit","conidooooo",false);
					
				});
			}catch(e){ 
				alert(e); 
			}
		
	</script>
</body>
</html> 