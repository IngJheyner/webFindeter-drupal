/**
 * @file
 * Global utilities.
 *
 */

(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.global = {

        attach: function (context, settings) {

            $(document, context).once('webfinde').each( function() {

                /*===========================================
                Desactovar google analitycs siempre y cuando se acepten
                terminos y politicas RGPD (Reglamento General de Protección de Datos).
                =============================================*/
                /*if (!Drupal.eu_cookie_compliance.hasAgreed()){
                    window['ga-disable-UA-xxxx-1'] = true;
                }*/
                /*===========================================
                MENU SIDEBAR MOVIL
                =============================================*/
                //$("section.menuMovil nav").attr("id", "sidebar");
                $("section.menuMovil nav ul li ul.submenu li.submenuItems > span").addClass('d-none');
                $("section.menuMovil nav ul li ul.submenu").addClass('collapse');

                //Abrir el menu

                $("#sidebarCollapseMd").on('click', function() {
                    $("section.menuMovil div.imgClose").toggle('slow');
                    $("#sidebar").toggle('slow');
                });

                $("section.menuMovil nav ul li").on('click', function() {

                    $(this).children('ul.submenu').toggle('slow');
                });

                $("section.menuMovil div.imgClose button").on('click', function() {
                    $("#sidebar").hide();
                    $("section.menuMovil div.imgClose").toggle();
                });

                /*===========================================
                CONTROL DE PAUSA DEL VIDEO DEL HOME
                =============================================*/
                $('.sliderHome #views-bootstrap-slider-slider-home .carousel-item.active .video-embed-field-provider-html-5 video').attr('id', 'videoHome');
                let videoHome = document.getElementById('videoHome');

                $('div.controlVideoHome button').on('click', function() {

                    if ($(this).attr('estadoVideo') == 'play') {

                        videoHome.pause();
                        $(this).attr('estadoVideo', 'pause').children('svg').removeClass('svg-inline--fa fa-pause').addClass('svg-inline--fa fa-play');

                    } else {

                        videoHome.play();
                        $(this).attr('estadoVideo', 'play').children('svg').removeClass('svg-inline--fa fa-play').addClass('svg-inline--fa fa-pause');

                    }

                });

                /*===========================================
                SE ABRE LA VENTANA MODAL PARA EL HOME
                =============================================*/
                $('#myLargeModalLabel').modal('show');


                /*===========================================
                PRODUCTOS Y SERVICIOS
                =============================================*/
                const fondoUp = {
                    "height": "400%",
                    "transition": ".3s all",
                    "background": "#112d6a",
                    "border-radius": "0.8em 0.8em 0.8em 0.8em"
                }

                const fondoDown = {
                    "height": "169%",
                    "transition": ".5s all",
                    "background": "none",
                    "background": "repeating-linear-gradient(188deg, transparent 0, transparent 15%, rgb(17, 45, 106) 0, #296294)",
                    "border-radius": "0 0 0.8em 0.8em"
                }

                /* ===== ===== FOCUS ===== ===== */
                $(".productosServicios .grid-container .grid-item .fondo a").on("focus", function() {
                    $(this).parent(".fondo").css(fondoUp);
                    $(this).css({ "bottom": "8rem" });
                    $(this).children("button").show();
                });

                $(".productosServicios .grid-container .grid-item .fondo a button").on("blur", function() {
                    $(this).parent().parent(".fondo").css(fondoDown);
                    $(this).parent().css({ "bottom": "4rem" });
                    $(this).hide();
                });

                /* ===== ===== MOUSE ===== ===== */
                $(".productosServicios .grid-container .grid-item").on('mouseover', function() {
                    $(this).children("figure").children(".fondo").css(fondoUp);
                    $(this).children("figure").children(".fondo").children("a").css({ "bottom": "8rem" });
                    $(this).children("figure").children(".fondo").children("a").children("button").show();
                });

                $(".productosServicios .grid-container .grid-item").on('mouseout', function() {

                    $(this).children("figure").children(".fondo").css(fondoDown);
                    $(this).children("figure").children(".fondo").children("a").css({ "bottom": "4rem" });
                    $(this).children("figure").children(".fondo").children("a").children("button").hide();

                });

                /* ===== ===== VENTANA MODAL ===== ===== */
                $(".productosServicios .grid-container .grid-item figure button").on("click", function() {

                    $("#modalProductosServicios .modal-body h3").html($(this).attr('titulo'));
                    $("#modalProductosServicios .modal-body .descripcion").html($(this).attr('descripcion'));
                    //$("#modalProductosServicios img").attr("src", $(this).attr('imagen'));
                    $("#modalProductosServicios").css({
                        "background-image": "url('" + $(this).attr('imagen') + "')"
                    });

                    if ($(this).attr('url') != "") {

                        $("#modalProductosServicios .modal-body a").show();
                        $("#modalProductosServicios .modal-body a").attr("href", $(this).attr('url')).html($(this).attr('tituloUrl'));

                    } else {

                        $("#modalProductosServicios .modal-body a").hide();

                    }
                });

                /* ===== ===== INTERNAS ===== ===== */
                let iterarTab = 0;

                /* ===== ===== Activar el tab principal al venir por url ===== ===== */

                let hash_productos_servicios = window.location.hash.toString().replace(/^#|-/g,'');

                if(hash_productos_servicios !== ""){

                    let tab_pane = "";

                    $('.productoServicios .cuerpoContenido ul.tabPrincipal li a').each(function(index, element){

                        if($(element).attr("nombre-items") === hash_productos_servicios){
                            tab_pane = $(element).attr("aria-controls");
                            $(element).addClass("active");
                        }else{
                            $(element).removeClass('active');
                        }

                    });

                    $('.productoServicios #myTabContentPrincipal > div.tab-pane').each(function(index, element){

                        $(element).removeClass('active show');

                        if(tab_pane === $(element).attr('id')){

                            $(element).addClass('active show');

                            $('html, body').animate({
                                scrollTop: $(".productoServicios .cuerpoContenido ul.tabPrincipal").offset().top
                            }, 2000);

                        }
                    });
                }

                /* ===== ===== Cambio de atributos al cambiar en los tabs principal, usabilidad interna ===== ===== */
                $('.productoServicios .cuerpoContenido ul.tabPrincipal li a').on("click", function() {

                    $('.productoServicios #myTabContentPrincipal .tab-pane.show.active .paragraph ul').children('li').each(function(index, element) {

                        $(element).children('a').attr('href', "");

                    });

                    tabProductosServicios();

                });

                /* ===== ===== Funcion de cambios de atributos, tabs internos ===== ===== */
                const tabProductosServicios = () => {

                    let nomTabInterno = "";
                    let tabCotenidoPrincipal = $('.productoServicios #myTabContentPrincipal .tab-pane.show.active .paragraph ul').attr('data-quickedit-entity-id');
                    iterarTab++;
                    if (tabCotenidoPrincipal != undefined) { nomTabInterno = tabCotenidoPrincipal.replace('/', '-') + "-" + String(iterarTab) };

                    /* ===== ===== Anclas ===== ===== */
                    $('.productoServicios #myTabContentPrincipal .tab-pane.show.active .paragraph ul').children('li').each(function(index, element) {

                        let hreA = $(element).children('a').attr('ancla');
                        let hreAn = hreA + '-' + nomTabInterno;
                        $(element).children('a').attr('href', hreAn);

                    });

                    /* ===== ===== Contenido de las anclas ===== ===== */
                    $('.productoServicios #myTabContentPrincipal .tab-pane.show.active #myTabContentInternas div.tab-pane').each(function(index, element) {

                        let hreA = $(element).attr('idAncla');
                        let hreAn = hreA + '-' + nomTabInterno;
                        $(element).attr('id', hreAn);

                    });


                }

                //Se ejecuta la funcion de cambio de atributos de anclas
                tabProductosServicios();

                /* ===== ===== Efecto para la descripcion de menu o grillas internas de las tabs ===== ===== */
                $("div#myTabContentInternas div.gridContainer div.grid div.gridContenido .paragraph .descripcion").hide();

                $("div#myTabContentInternas div.gridContainer div.grid div.gridContenido").on("click", function() {

                    if ($(this).attr('modo') === 'arriba') {

                        $(this).children('.paragraph').children('.descripcion').slideDown("slow");
                        $(this).children('.paragraph').children('.titulos').children('svg').removeClass('fa-chevron-left');
                        $(this).children('.paragraph').children('.titulos').children('svg').addClass('fa-chevron-down');
                        $(this).attr('modo', 'abajo');

                    } else {
                        $(this).children('.paragraph').children('.descripcion').slideUp("slow");
                        $(this).children('.paragraph').children('.titulos').children('svg').removeClass('fa-chevron-down');
                        $(this).children('.paragraph').children('.titulos').children('svg').addClass('fa-chevron-left');
                        $(this).attr('modo', 'arriba');
                    }


                });

                /* === efecto contador en cifras ===*/

                function contador() {
                    const counters = document.querySelectorAll('.counter');
                    const speed = 450;
                    counters.forEach(counter => {
                        const updateCount = () => {
                            const target = +counter.getAttribute('data-target');
                            const count = +counter.innerText;
                            const inc = target / speed;
                            if (count < target) {
                                if (count < 1) {
                                    if(count < 0.001){
                                        counter.innerText = (count + inc).toFixed(6);
                                    }else{
                                        counter.innerText = (count + inc).toFixed(3);
                                    }
                                } else {
                                    if (Number.isInteger(target)) {
                                        counter.innerText = Math.round(count + inc);
                                    } else {
                                        counter.innerText = (count + inc).toFixed(3);
                                    }
                                }
                                setTimeout(updateCount, 2);
                            } else {
                                counter.innerText = target;
                            }
                        };
                        updateCount();
                    });
                }

                if (document.getElementById("block-findeter-cifras-home")) {
                    var alturacifras = $("#block-findeter-cifras-home").offset().top;
                } else {
                    var alturacifras = '';
                }

                if (document.getElementById("block-findeter-cifras-home")) {

                    $(window).scroll(function() {

                        if ($(window).scrollTop() >= alturacifras - 200) {

                            contador();

                        }

                    });
                }



                /* ========= Fin efecto =========== */

                /*===========================================
                BLOG
                =============================================*/

                /* ===== ===== BUSCADOR ===== ===== */
                $(".encabezadoBlog .encabezado .iconos a#iconBuscador").on('click', function() {
                    $(".encabezadoBlog .encabezado .menu div#backBuscador").removeClass('d-none').addClass('show');
                    $(".encabezadoBlog .encabezado .menu div.buscador").slideDown(1000);
                });

                $(".encabezadoBlog .encabezado .menu div#backBuscador").on('click', function() {

                    $(".encabezadoBlog .encabezado .menu div.buscador").slideUp(1000);
                    setTimeout(function() {
                        $(".encabezadoBlog .encabezado .menu div#backBuscador").removeClass('show').addClass('d-none');
                    }, 1100);
                });

                /* ===== ===== BUSCADOR INTERNAS ===== ===== */
                $('div.busquedaBlogInterna div.buscador form#custom-search-block-form div#edit-actions input[type="image"]').attr({ 'type': 'submit', 'value': 'Buscar' }).addClass('btn btn-secondary');

                /* ===== ===== Articulo internos ===== ===== */
                var imgEncabezadoArticulo = $(".articuloInternasBlog").attr("imgFondoEncabezado");

                if (imgEncabezadoArticulo != null) {

                    if(imgEncabezadoArticulo !== undefined)
                        $(".encabezadoBlog").css("background-image", "url('" + imgEncabezadoArticulo + "')");

                    $(".encabezadoBlog .titulos h4").hide();
                    $(".encabezadoBlog .titulos h1").hide();

                } else {

                    var imgEncabezado = $(".encabezadoBlog").attr("imgFondoEncabezado");

                    if(imgEncabezado !== undefined)
                        $(".encabezadoBlog").css("background-image", "url('" + imgEncabezado + "')");
                }

                /*===========================================
                TRANSPARENCIA
                =============================================*/

                /* ===== ===== MENU ===== ===== */
                $(".menuTransparenciaAcceso ul.nav div.gridContainer li.gridItem").on("click", function() {

                    if ($(this).attr("mostrar") == "true") {

                        $(this).children('ul').children('li').fadeIn(1000, function() {

                            $(this).parent().parent().children('div.gridItemTitulo').children('div.flecha').children('img').attr({ 'src': '/themes/custom/webfinde/images/iconos/flecha2.png' })
                        });

                        $(this).attr({ "mostrar": "false" });

                    } else {

                        $(this).children('ul').children('li').fadeOut(1000, function() {

                            $(this).parent().parent().children('div.gridItemTitulo').children('div.flecha').children('img').attr({ 'src': '/themes/custom/webfinde/images/iconos/flecha.png' })
                        });

                        $(this).attr({ "mostrar": "true" });
                    }

                });

                /*===========================================
                CONVOCATORIAS
                =============================================*/
                let activarBusqueda = false;
                //console.log("🚀 ~ file: global.js ~ line 370 ~ $ ~ activarBusqueda", activarBusqueda)

                function formBusquedaAvanzada() {

                    $(".convocatoriaCiudadano form div.form-item-created-min input[type='text']").addClass('datepicker InicialApertura').attr('readonly', '');

                    $(".convocatoriaCiudadano form div.form-item-created-max input[type='text']").addClass('datepicker FinalApertura').attr('readonly', '');

                    $(".convocatoriaCiudadano form div.form-item-created-max label").html('Hasta');

                    /* ===== ===== Fechas ===== ===== */
                    $(".datepicker.InicialApertura").datepicker({

                        language: "es",
                        format: 'dd-mm-yyyy',
                        todayHighlight: true,
                    });

                    $(".datepicker.InicialApertura").on("change", function() {

                        var fechaInicial = $(this).val();

                        $(".datepicker.FinalApertura").datepicker({

                            language: "es",
                            datesDisabled: fechaInicial - 1,
                            startDate: fechaInicial,
                            format: 'dd-mm-yyyy',
                        });

                    });

                    $(".convocatoriaCiudadano form .form-actions a").on('click', function() {

                        if (activarBusqueda === false) {

                            $(".convocatoriaCiudadano form .form--inline div:nth-child(5)").slideDown("slow");
                            $(".convocatoriaCiudadano form .form--inline div:nth-child(6)").slideDown("slow");
                            $(".convocatoriaCiudadano form .form--inline div:nth-child(7)").slideDown("slow");
                            $(".convocatoriaCiudadano form .form--inline div:nth-child(8)").slideDown("slow");
                            $(".convocatoriaCiudadano form .form--inline div:nth-child(9)").slideDown("slow");
                            $(".convocatoriaCiudadano form .form--inline fieldset").slideDown("slow");
                            activarBusqueda = true;

                        } else {

                            $(".convocatoriaCiudadano form .form--inline div:nth-child(5)").slideUp("slow");
                            $(".convocatoriaCiudadano form .form--inline div:nth-child(6)").slideUp("slow");
                            $(".convocatoriaCiudadano form .form--inline div:nth-child(7)").slideUp("slow");
                            $(".convocatoriaCiudadano form .form--inline div:nth-child(8)").slideUp("slow");
                            $(".convocatoriaCiudadano form .form--inline div:nth-child(9)").slideUp("slow");
                            $(".convocatoriaCiudadano form .form--inline fieldset").slideUp("slow");
                            activarBusqueda = false;
                        }
                    });

                }

                $(document).ajaxStop(function() {
                    formBusquedaAvanzada();
                    //console.log("🚀 ~ file: global.js ~ line 370 ~ $ ~ activarBusqueda", activarBusqueda)
                });

                //formBusquedaAvanzada();
                $('#tableDetalleConvocatorias').DataTable({

                    "language": {
                        "info": "Mostrando pagina _PAGE_ de _PAGES_, total de archivos _TOTAL_",
                        "search": "Buscar:",
                        "emptyTable": "No se encontraron archivos adjuntos para la convocatoria.",
                        "zeroRecords": "No se encontraron resultados.",
                        "infoFiltered": " - de _MAX_ filtros",
                        "lengthMenu": "Mostrar _MENU_ registros por pagina.",
                        "paginate": {
                            "previous": "Anterior",
                            "next": "Siguiente"
                        }
                    },
                    order: [
                        [2, 'desc'],
                        //[0, 'asc']
                    ],
                    //"ordering" : false
                });

                /*===========================================
                PROSPERITY FUND
                =============================================*/
                $.fn.dataTable.render.moment = function ( from, to, locale ) {
                    // Argument shifting
                    if ( arguments.length === 1 ) {
                        locale = 'en';
                        to = from;
                        from = 'YYYY-MM-DD';
                    }
                    else if ( arguments.length === 2 ) {
                        locale = 'en';
                    }
                    return function ( d, type, row ) {
                        var m = window.moment( d, from, locale, true );

                        // Order and type get a number value from Moment, everything else
                        // sees the rendered value
                        return m.format( type === 'sort' || type === 'type' ? 'x' : to );
                    };
                };

                /* ===== ===== INTERNAS DETALLE DE PROCESO y prosperity fund ===== ===== */
                $('#tableDetalleProsperity').DataTable({

                    "language": {
                        "info": "Mostrando pagina _PAGE_ de _PAGES_, total de archivos _TOTAL_",
                        "search": "Buscar:",
                        "emptyTable": "No se encontraron archivos adjuntos para la convocatoria.",
                        "zeroRecords": "No se encontraron resultados.",
                        "infoFiltered": " - de _MAX_ filtros",
                        "lengthMenu": "Mostrar _MENU_ registros por pagina.",
                        "paginate": {
                            "previous": "Anterior",
                            "next": "Siguiente"
                        }
                    },
                    order: [
                        [2, 'desc'],
                        //[0, 'asc']
                    ],
                    columnDefs: [ {
                        targets: 2,
                        render: $.fn.dataTable.render.moment('DD/MM/YYYY', 'DD/MM/YYYY')
                    } ]
                    //"ordering" : false
                });


                /*===========================================
                TEXT RIZE( PARA ACCESIBILIDAD )
                =============================================*/

                const fontSize = (operador) => {

                    const incrementar_text_rize = 1;
                    const etiquetas_text_rize = ['h1', 'h2','h3','h5', 'h6', 'p', 'em', 'ul', 'ol','strong', 'table', 'label', 'a'];
                    let array_etiqueta_text_rize = [];

                    let text_rize = document.querySelector('.text-rize');

                    if(text_rize != null){

                        for(let i =0; i < etiquetas_text_rize.length; i++){

                            let nodeList = text_rize.querySelectorAll(etiquetas_text_rize[i]);
                            let nodeArray = Array.apply(null, nodeList);
                            array_etiqueta_text_rize.push(nodeArray);//2.5rem

                            array_etiqueta_text_rize.forEach(element => {

                                for(let e = 0; e < element.length; e++){

                                    let font_size = $(element[e]).css('font-size');

                                    font_size = font_size.replace("px", '');

                                    const font_size_text = (operador == "suma") ? parseInt(incrementar_text_rize) + parseInt(font_size) : parseInt(font_size) - parseInt(incrementar_text_rize);
                                    $(element[e]).attr("style", "font-size: " + font_size_text +"px !important");
                                }

                            });

                            array_etiqueta_text_rize = [];
                        }
                    }

                }

                $("#text_resize_decrease, #text_resize_increase").on('click', function(){
                    fontSize($(this).attr('operador'));
                });

                /*===========================================
                CONTRASTE
                =============================================*/
                const contrast = element => {
                    element.addEventListener('click', () => {
                        if ($('html').hasClass('contrasteweb')) {
                        $('html').removeClass('contrasteweb');
                        } else {
                        $('html').addClass('contrasteweb');
                        }
                    })
                }

                contrast(document.getElementById('high-contrast'));


            });//each context

            $(document, context).ready(function() {

                /*===========================================
                CASOS DE ÉXITO
                =============================================*/
                let gridDefecto = $("div.casosExito div.slideCasoExito .slick .slide--0 ul li.grid--0").children().children().children().children().children().children('.contenidoSlider').children('a');

                if($(gridDefecto).attr('imgFondo') !== undefined){
                    $("div.casosExito").css({ 'background': 'url("' + $(gridDefecto).attr('imgFondo') + '")' });
                }

                $("div.casosExito div.contenido h1.tituloCaso").html($(gridDefecto).attr('titulo'));
                $("div.casosExito div.contenido p.descripcionCaso").html($(gridDefecto).attr('descripcion'));
                $("div.casosExito div.contenido a.enlace").attr("href", $(gridDefecto).attr('enlace'));

                if ($(gridDefecto).attr('contenido') != "") {
                    $("div.casosExito div.contenido div#modalTrayectoria h5").html($(gridDefecto).attr('titulo'));
                    $("div.casosExito div.contenido div#modalTrayectoria div.modal-body").html($(gridDefecto).attr('contenido'));
                }

                //Mostrar imagen
                const mostrarImgCasoExito = (etiqueta) => {

                    /*let imgFondo = $('a', etiqueta).attr('imgFondo');
                    let titulo = $('a', etiqueta).attr('titulo');
                    let descripcion = $('a', etiqueta).attr('descripcion');
                    let enlace = $('a', etiqueta).attr('enlace');*/

                    let imgFondo = $(etiqueta).attr('imgFondo');
                    let titulo = $(etiqueta).attr('titulo');
                    let descripcion = $(etiqueta).attr('descripcion');
                    let contenido = $(etiqueta).attr('contenido');
                    let enlace = $(etiqueta).attr('enlace');
                    $("div.casosExito").css({
                        'background': 'url("' + imgFondo + '")'
                    });
                    $("div.casosExito div.contenido h1.tituloCaso").html(titulo);
                    $("div.casosExito div.contenido p.descripcionCaso").html(descripcion);
                    $("div.casosExito div.contenido a.enlace").attr("href", enlace);
                    if (contenido != "") {
                        $("div.casosExito div.contenido div#modalTrayectoria h5").html(titulo);
                        $("div.casosExito div.contenido div#modalTrayectoria div.modal-body").html(contenido);
                    }

                }


                /* ===== ===== Cambiar contenido de casos dinamico ===== ===== */
                $("div.casosExito div.slideCasoExito div.contenidoSlider").on("click", function() {
                    mostrarImgCasoExito($(this).children('a'));
                });

                $("div.casosExito div.slideCasoExito div.contenidoSlider a").on("keypress", function(e) {
                    let code = (e.keyCode ? e.keyCode : e.which);
                    if (code == 13) {
                        mostrarImgCasoExito($(this));
                    }
                });
            });
        }
    };
})(jQuery, Drupal);