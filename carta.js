/*
var AjustarDragDropSegunEtapa = function($contenedor,etapa_id) {
	try{
				 
		// Limpio los valores drag n drop de todas las cartas
		$contenedor.removeClass("dropeable");
		CartasSeleccionables($contenedor,false);

		switch(parseInt(etapa_id)){
			case 1:
										
			break;
			case 2:
										
			break;
			case 3:
										
			break;
			case 4:
										
			break;
			case 5:
										
			break;
			case 6:
										
			break;
			case 7:
				var span_tipo_carta = $contenedor.parent("span") ;
				var span_jugador = span_tipo_carta.parent("span") ;
				
				var span_tipo_carta = $contenedor.parent("span") ;
				if(span_tipo_carta.hasClass('carta_activa') ) {
					if(span_jugador.hasClass('jugador' + 1 ) ) {
						$contenedor.addClass("dropeable");
					}
				}
				if(span_tipo_carta.hasClass('cartas_mano')) {
					if(span_jugador.hasClass('jugador' + 1 ) ) {
						CartasSeleccionables($contenedor,true,[1,2]); 
					}									
				}
			break;
			case 8:
										
			break;
			default:
										
		}
	}catch(e) { alert(e); }
}


var CartasSeleccionables = function($contenedores,seleccionable,arrTipos) {
	try{
		var $cartas = $contenedores.find("div.carta_normal");
		if($cartas.length > 0) {
			if(seleccionable){
				if(typeof arrTipos == 'undefined'){
					$cartas.addClass("carta_seleccionable");
				} else {
					$.each($cartas, function( index, carta ) {
						var $carta = $(carta);
						var tipo = $carta.find(".tipo").html();
						tipo = parseInt(tipo);
						if(arrTipos.indexOf(tipo) >= 0 ){
							$carta.addClass("carta_seleccionable");
						}
					}); 
				}
			}else{
				$cartas.removeClass("carta_seleccionable");
			}
		}	
	}catch(e){ alert(e)}
}
*/
var Interrupcion = function (titulo,contenido,boton_cancelar) {
	try{
		$("#dialog-confirm-text").html(contenido);

		var arrBotones = new Array();
		arrBotones.push(
			{ 
				text: "OK",
				click: function() { 
					ObtenerEstado(-1);
					$( this ).dialog( "close" ); 
				} 
			} 
		);
		if(boton_cancelar){
			arrBotones.push(
				{ 
					text: "CANCELAR",
					click: function() { 
						ObtenerEstado(-2);
						$( this ).dialog( "close" ); 
					} 
				} 
			);
		}

		$( "#dialog-confirm" ).attr('title',titulo).dialog({
			closeOnEscape: false,
			open: function(event, ui) {
				// Oculto el boton cerrar ( X )
				$(".ui-dialog-titlebar-close").hide(); 
			},
			resizable: false,
			height: "auto", //($(document).height() / 2 ),
			width: "auto", //($(document).width() / 2 ),
			modal: false,
			buttons: arrBotones
		});		
	}catch(e){alert(e);}
}
var ActualizarContenedoresSegunEtapa = function($etapa_id) {
	try{
		switch($etapa_id){
		/*
			case 1:
				$.each($cartas_jugadorA_mano,function(key,value){
					cargarCarta(value);
				});							
			break;
			case 2:
				$.each($cartas_jugadorB_mano,function(key,value){
					cargarCarta(value);
				});							
			break;
		*/
			default:
				$.each($cartas,function(key,value){
					cargarCarta(value);
				});							
		}
	}catch(e) { alert(e); }
}

var ObtenerEstado = function(contenedor_id){
	try{
		$("#estado_actualizacion").html('CONECTANDO...');
		
		$.ajax({
			// la URL para la petición
			url : 'etapa.php',
			// (también es posible utilizar una cadena de datos) 
			data : { partida_id : 1 , 
				contenedor_id : contenedor_id  
			},
			// especifica si será una petición POST o GET
			type : 'GET',
			// el tipo de información que se espera de respuesta
			dataType : 'json',
			// código a ejecutar si la petición es satisfactoria;
			// la respuesta es pasada como argumento a la función
			success : function(json) {
				try{
					var $etapa_id = $("#etapa_id") ;
					
					$("#etapa_texto").html(json.etapa_texto);
					$("#turno_jugador").html(json.turno_jugador);
					$("#estado_actualizacion").html('CONEXION OK');

					if(json.etapa_id != $etapa_id.html() ){ 
						$etapa_id.html(json.etapa_id);
					} 
					
					ActualizarContenedoresSegunEtapa(json.etapa_id);
					
					if(json.interrupcion){
						Interrupcion(
							json.interrupcion_titulo,
							json.interrupcion_contenido,
							json.interrupcion_boton_cancelar
						);
					}
					
					/*
					window.clearTimeout(timeoutCargarEstado);
					timeoutCargarEstado = window.setTimeout("CargarEstado();",1000);
					*/ 
				 }catch(e){
					alert(e);
				 }
			},
			// código a ejecutar si la petición falla;
			// son pasados como argumentos a la función
			// el objeto de la petición en crudo y código de estatus de la petición
			error : function(xhr, status) { 
				//alert('Disculpe, existió un problema');
				$("#estado_actualizacion").html('FALLO: ' + status);
			},
			// código a ejecutar sin importar si la petición falló o no
			complete : function(xhr, status) {
				//alert('Petición realizada');
				//$("#estado_actualizacion").html('COMPLETO: ' + status);
			}
		});	
	
	}catch(e){alert(e);}
}

var CargarEstado = function($contenedor_seleccionado) {
	try { 
		
		
		var contenedor_id  = 0 ;
		if(typeof $contenedor_seleccionado != 'undefined'){
			contenedor_id = $contenedor_seleccionado.parent().attr('contenedor_id');
		}		
		
		ObtenerEstado(contenedor_id);
	} catch(e) { alert(e); }
}

var seleccionarCarta = function($carta_normal) {
	try{
		var carta_draggable = $carta_normal.hasClass( "draggable" ) ;
		if(carta_draggable) {
			var $contenedor_seleccionado = $carta_normal.parent();
			var $contenedor_dropeable = $(".dropeable") ;
			if($contenedor_dropeable.length > 0 ) {
				CargarEstado($carta_normal); 				
				
				// Mover carta
				$contenedor_dropeable.html($contenedor_seleccionado.html()) ;
				$contenedor_seleccionado.html('');
				$contenedor_dropeable.removeClass("dropeable"); 
				$contenedor_dropeable.find(".draggable").removeClass("draggable");  
			} else {
				alert(" no hay dropeable") ;
			}
		}else{
			alert('Esta carta no es seleccionable');
		}
	}catch(e){ alert(e); }
}

var ocultarVisualizador = function() {
	var $visualizador = $("#visualizador_cartas") ;
	$visualizador.css("display", "none");
}
var cargarVisualizador = function($carta_normal) {
	try{
		var $visualizador = $("#visualizador_cartas") ;
		var $visualizador_imagen = $visualizador.find(".imagen") ;
		var $visualizador_vida = $visualizador.find(".vida") ;
		var $visualizador_ataque1 = $visualizador.find(".ataque1") ;
		var $visualizador_ataque2 = $visualizador.find(".ataque2") ;
		var $visualizador_ataque3 = $visualizador.find(".ataque3") ;
		var $visualizador_efecto_texto = $visualizador.find(".efecto_texto") ;
		var $visualizador_soporte_texto = $visualizador.find(".soporte_texto") ;
		var $visualizador_especialidad = $visualizador.find(".especialidad") ;
		var $visualizador_tipo = $visualizador.find(".tipo") ;
		
		
		var $carta_normal_imagen = $carta_normal.find(".imagen") ;
		var $carta_normal_vida = $carta_normal.find(".vida") ;
		var $carta_normal_ataque1 = $carta_normal.find(".ataque1") ;
		var $carta_normal_ataque2 = $carta_normal.find(".ataque2") ;
		var $carta_normal_ataque3 = $carta_normal.find(".ataque3") ;
		var $carta_normal_efecto_texto = $carta_normal.find(".efecto_texto") ;
		var $carta_normal_soporte_texto = $carta_normal.find(".soporte_texto") ;
		var $carta_normal_especialidad = $carta_normal.find(".especialidad") ;
		var $carta_normal_tipo = $carta_normal.find(".tipo") ;
		
		$visualizador_imagen.attr("src",$carta_normal_imagen.attr("src"));
		$visualizador_vida.html($carta_normal_vida.html());
		$visualizador_ataque1.html($carta_normal_ataque1.html());
		$visualizador_ataque2.html($carta_normal_ataque2.html());
		$visualizador_ataque3.html($carta_normal_ataque3.html());
		$visualizador_efecto_texto.html($carta_normal_efecto_texto.html());
		$visualizador_soporte_texto.html($carta_normal_soporte_texto.html());
		$visualizador_especialidad.html($carta_normal_especialidad.html());
		$visualizador_tipo.html($carta_normal_tipo.html());
		
		$visualizador.css("display", "block"); 
	}catch(e) { alert(e); }
}

var cargarCarta = function(contenedor) {
	try{
		var contenedor_id =	$(contenedor).attr('contenedor_id');
		llenarContenedor(contenedor,"#carta_cargando_bak");
		$.ajax({
			// la URL para la petición
			url : 'contenedor_json.php',
			// (también es posible utilizar una cadena de datos) 
			data : {  
				usuario_id : 1 ,
				contenedor_id : contenedor_id  ,
				partida_id : window.partida_id 
			},
			// especifica si será una petición POST o GET
			type : 'GET',
			// el tipo de información que se espera de respuesta
			dataType : 'json',
			// código a ejecutar si la petición es satisfactoria;
			// la respuesta es pasada como argumento a la función
			success : function(json) {
				try{
					if(json.contenedor_lleno){
						llenarContenedor(contenedor,"#carta_normal_bak");
						$(contenedor).find("img.imagen").attr("src",json.imagen);
						$(contenedor).find("div.vida").html(json.vida);
						$(contenedor).find("div.ataque1").html(json.ataque1);
						$(contenedor).find("div.ataque2").html(json.ataque2);
						$(contenedor).find("div.ataque3").html(json.ataque3);
						$(contenedor).find("div.efecto_id").html(json.efecto_id);
						$(contenedor).find("div.efecto_texto").html(json.efecto_texto);
						$(contenedor).find("div.soporte_id").html(json.soporte_id);
						$(contenedor).find("div.soporte_texto").html(json.soporte_texto);
						$(contenedor).find("div.especialidad").html(json.especialidad);
						$(contenedor).find("div.tipo").html(json.tipo);
						
						var $carta = $(contenedor).find("div.carta_normal") ;
						if(json.draggable) { 
							$carta.addClass("draggable");
						}						
						
					}else{
						$(contenedor).html('');
					}
					
					if(json.dropeable) {
						$(contenedor).addClass("dropeable");
					}
				 }catch(e){
					llenarContenedor(contenedor,"#carta_fallo_bak");
					alert(e);
				 }
			},
			// código a ejecutar si la petición falla;
			// son pasados como argumentos a la función
			// el objeto de la petición en crudo y código de estatus de la petición
			error : function(xhr, status) { 
				//alert('Disculpe, existió un problema');
				llenarContenedor(contenedor,"#carta_fallo_bak");
			},
			// código a ejecutar sin importar si la petición falló o no
			complete : function(xhr, status) {
				//alert('Petición realizada');
			}
		});
	}catch(e){
		llenarContenedor(contenedor,"#carta_fallo_bak");
		alert(e);
	}
}

var llenarContenedor = function(contenedor,id_carta_bak) {
	try{
		var html_carta_bak = $(id_carta_bak).html() ;
		$(contenedor).html(html_carta_bak) ;
	}catch(er){ alert(er);} 
}
