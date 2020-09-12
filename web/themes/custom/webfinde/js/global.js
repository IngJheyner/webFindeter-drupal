/**
 * @file
 * Global utilities.
 *
 */
(function($, Drupal) {

    'use strict';

    //Drupal.behaviors.webfinde = {
        //attach: function(context, settings) {
            
           /*===========================================
           BUSCADOR(HOME)
           =============================================*/
            $("form#search-block-form .btnSearch").attr("value","");

            $(".busIdioma a.buscador").on('click', function(){
               
                
                var vinculo = $(this).attr("href");
		
                $("html, body").animate({

                    scrollTop: $(vinculo).offset().top - 60

                }, 1000, "easeInOutBack")
                        
            });

            /*===========================================
            PRODUCTOS Y SERVICIOS
            =============================================*/
            $(".productosServicios .grid-container .grid-item figure button").hide();

            $(".productosServicios .grid-container .grid-item").on('mouseover', function(){

                $(this).children("figure").children(".fondo").css({
                    "height": "400%", 
                    "transition" : ".5s all",
                    "background" : "#112d6a",
                    "border-radius" : "0.8em 0.8em 0.8em 0.8em"
                });
                
                $(this).children("figure").children(".fondo").children("h1").children("button").show();
             
            });

            $(".productosServicios .grid-container .grid-item").on('mouseout', function(){

                $(this).children("figure").children(".fondo").css({
                    "height": "109%", 
                    "transition" : ".5s all",
                    //"background" : "repeating-linear-gradient(184deg, transparent 0, transparent 19%, #296294 0, #296294",                   
                    "border-radius": "0 0 0.8em 0.8em"});
                
                    $(this).children("figure").children(".fondo").children("h1").children("button").hide();
                
            });

            /* ===== ===== VENTANA MODAL ===== ===== */
            $(".productosServicios .grid-container .grid-item figure button").on("click", function(){

                $("#modalProductosServicios .modal-body h3").html($(this).attr('titulo'));
                $("#modalProductosServicios .modal-body .descripcion").html($(this).attr('descripcion'));
                //$("#modalProductosServicios img").attr("src", $(this).attr('imagen'));
                $("#modalProductosServicios").css({
                    "background-image":"linear-gradient(to bottom, transparent 193px,#112d6a 0,#112d6a),url('"+$(this).attr('imagen')+"')"
                });

                if($(this).attr('url') != ""){

                    $("#modalProductosServicios .modal-body a").show();
                    $("#modalProductosServicios .modal-body a").attr("href", $(this).attr('url')).html($(this).attr('tituloUrl'));

                }else{

                    $("#modalProductosServicios .modal-body a").hide();

                }    
            });

            /*===========================================
            BLOG
            =============================================*/

            /* ===== ===== Articulo internos ===== ===== */
            var imgEncabezadoArticulo = $(".articuloInternasBlog").attr("imgFondoEncabezado");

            if(imgEncabezadoArticulo != null){

                $(".encabezadoBlog").css("background-image", "url('"+imgEncabezadoArticulo+"')");
                
            }else{

                var imgEncabezado = $(".encabezadoBlog").attr("imgFondoEncabezado");

                $(".encabezadoBlog").css("background-image", "url('"+imgEncabezado+"')");
            }
            
        //}
    //};

})(jQuery);
