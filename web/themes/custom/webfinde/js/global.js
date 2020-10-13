/**
 * @file
 * Global utilities.
 *
 */
(function($, Drupal) {

    'use strict';

    Drupal.behaviors.webfinde = {
        attach: function(context, settings) {
            
           /*===========================================
           BUSCADOR(HOME)
           =============================================*/
            $("form#search-block-form .btnSearch").attr("value","");

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
            FINDETER CON CIFRAS
            =============================================*/
            var imgPrimario = null;
            var imgSecundario = null;
            $("div.findeterCifras .grid-container .grid-item .contenido").on("mouseover", function(){

                imgPrimario = $('img', this).attr('src');
                imgSecundario = $('img', this).attr('imgSecundario');

                $('img', this).attr('src', imgSecundario);   

            });

            $("div.findeterCifras .grid-container .grid-item .contenido").on("mouseout", function(){                
                $('img', this).attr('src', imgPrimario); 
            });

            /*===========================================
            CASOS DE ÉXITO
            =============================================*/
            var gridDefecto = $("div.casosExito div.slideCasoExito .slick .slide--0 ul li.grid--0").children().children().children().children().children().children('.contenidoSlider').children('p');

            $("div.casosExito").css(
                {'background':'url("'+$(gridDefecto).attr('imgFondo')+'")'}
            );
            $("div.casosExito div.contenido h1.tituloCaso").html($(gridDefecto).attr('titulo'));
            $("div.casosExito div.contenido p.descripcionCaso").html($(gridDefecto).attr('descripcion'));
            $("div.casosExito div.contenido a.enlace").attr("href",$(gridDefecto).attr('enlace'));

            /* ===== ===== Cambiar contenido de casos dinamico ===== ===== */
            $("div.casosExito div.slideCasoExito div.contenidoSlider").on("click", function(){

                var imgFondo = $('p', this).attr('imgFondo');
                var titulo = $('p', this).attr('titulo');
                var descripcion = $('p', this).attr('descripcion');
                var enlace = $('p', this).attr('enlace');

                $("div.casosExito").css(
                    {'background':'url("'+imgFondo+'")'}
                );
                $("div.casosExito div.contenido h1.tituloCaso").html(titulo);
                $("div.casosExito div.contenido p.descripcionCaso").html(descripcion);
                $("div.casosExito div.contenido a.enlace").attr("href",enlace);

            });

            
            /*===========================================
            BLOG
            =============================================*/

            /* ===== ===== Articulo internos ===== ===== */
            var imgEncabezadoArticulo = $(".articuloInternasBlog").attr("imgFondoEncabezado");

            if(imgEncabezadoArticulo != null){

                $(".encabezadoBlog").css("background-image", "url('"+imgEncabezadoArticulo+"')");
                $(".encabezadoBlog .titulos h4").hide();
                $(".encabezadoBlog .titulos h1").hide();
                
            }else{

                var imgEncabezado = $(".encabezadoBlog").attr("imgFondoEncabezado");

                $(".encabezadoBlog").css("background-image", "url('"+imgEncabezado+"')");
            }
            
        }
    };

})(jQuery, Drupal);
