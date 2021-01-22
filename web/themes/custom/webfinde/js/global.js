/**
 * @file
 * Global utilities.
 *
 */
(function($, Drupal) {

    'use strict';

    /*Drupal.behaviors.webfinde = {
        attach: function(context, settings) {*/

        $(document).ready(function(){    
            
            /*===========================================
            ADMIN
            =============================================*/

            /* ===== ===== Login ===== ===== */
            $('form#user-login-form').addClass('container my-5');

            /*===========================================
            MENU SIDEBAR MOVIL
            =============================================*/
            $("section.menuMovil nav").attr("id", "sidebar");
            $("section.menuMovil nav ul li ul.submenu li.submenuItems > span").addClass('d-none');
            $("section.menuMovil nav ul li ul.submenu").addClass('collapse');

            //Abrir el menu

            $("#sidebarCollapseMd").on('click', function(){
                $("section.menuMovil div.imgClose").toggle('slow');
                $("#sidebar").toggle('slow');                
            });

            $("section.menuMovil nav ul li").on('click', function(){
                
                $(this).children('ul.submenu').toggle('slow');
            });

            $("section.menuMovil div.imgClose button").on('click', function(){                
                $("#sidebar").hide();
                $("section.menuMovil div.imgClose").toggle();
            });

            /*===========================================
            MENU-GOVCO
            =============================================*/
            $("section.menuGovCo nav ul li a").html("");

            /*===========================================
           BUSCADOR(HOME)
           =============================================*/
            $("form#search-block-form .btnSearch").attr("value","");

            /*===========================================
            CONTROL DE PAUSA DEL VIDEO DEL HOME
            =============================================*/
            $('.sliderHome #views-bootstrap-slider-slider-home .carousel-item.active .video-embed-field-provider-html-5 video').attr('id', 'videoHome');
            let videoHome = document.getElementById('videoHome');
            
            $('div.controlVideoHome button').on('click', function(){

               if($(this).attr('estadoVideo') == 'play'){

                    videoHome.pause();
                    $(this).attr('estadoVideo', 'pause').children('svg').removeClass('svg-inline--fa fa-pause').addClass('svg-inline--fa fa-play');

               }else{

                    videoHome.play();
                    $(this).attr('estadoVideo', 'play').children('svg').removeClass('svg-inline--fa fa-play').addClass('svg-inline--fa fa-pause');

               }
               
            });            

            /*===========================================
            PRODUCTOS Y SERVICIOS
            =============================================*/
            $(".productosServicios .grid-container .grid-item figure button").hide();

            $(".productosServicios .grid-container .grid-item").on('mouseover', function(){

                $(this).children("figure").children(".fondo").css({
                    "height": "400%", 
                    "transition" : ".3s all",
                    "background" : "#112d6a",
                    "border-radius" : "0.8em 0.8em 0.8em 0.8em"
                });
                
                $(this).children("figure").children(".fondo").children("h1").children("button").show();
             
            });

            $(".productosServicios .grid-container .grid-item").on('mouseout', function(){

                $(this).children("figure").children(".fondo").css({
                    "height": "169%", 
                    "transition" : ".5s all",
					"background" : "none",
                    "background" : "repeating-linear-gradient(188deg, transparent 0, transparent 15%, rgb(17, 45, 106) 0, #296294)",
					//"background": "linear-gradient(70deg, black, white)",
					//"background" : "rgba(17, 45, 106, 0.1 )",
					//"background-image": "-webkit-linear-gradient(85deg, rgb(17, 45, 106) 45%, rgba(17, 45, 106, 0.1 )30%)",
                    "border-radius": "0 0 0.8em 0.8em"});
                
                    $(this).children("figure").children(".fondo").children("h1").children("button").hide();
                
            });

            /* ===== ===== VENTANA MODAL ===== ===== */
            $(".productosServicios .grid-container .grid-item figure button").on("click", function(){

                $("#modalProductosServicios .modal-body h3").html($(this).attr('titulo'));
                $("#modalProductosServicios .modal-body .descripcion").html($(this).attr('descripcion'));
                //$("#modalProductosServicios img").attr("src", $(this).attr('imagen'));
                $("#modalProductosServicios").css({
                    "background-image":"url('"+$(this).attr('imagen')+"')"
                });

                if($(this).attr('url') != ""){

                    $("#modalProductosServicios .modal-body a").show();
                    $("#modalProductosServicios .modal-body a").attr("href", $(this).attr('url')).html($(this).attr('tituloUrl'));

                }else{

                    $("#modalProductosServicios .modal-body a").hide();

                }    
            });


            
            /* ===== ===== INTERNAS ===== ===== */        
            let iterarTab = 0;
            
            /* ===== ===== Cambio de atributos al cambiar en los tabs principal, usabilidad interna ===== ===== */
            $('.productoServicios .cuerpoContenido ul.tabPrincipal li a').on("click", function(){

                $('.productoServicios #myTabContentPrincipal .tab-pane.show.active .paragraph ul').children('li').each(function(index, element){                 
                    
                    $(element).children('a').attr('href', "");

                });

                tabProductosServicios();                
                
            });

            /* ===== ===== Funcion de cambios de atributos, tabs internos ===== ===== */
            const tabProductosServicios = () =>{
                
                let nomTabInterno = "";
                let tabCotenidoPrincipal = $('.productoServicios #myTabContentPrincipal .tab-pane.show.active .paragraph ul').attr('data-quickedit-entity-id'); 
                iterarTab++;
                if(tabCotenidoPrincipal != undefined) { nomTabInterno = tabCotenidoPrincipal.replace('/','-') + "-" + String(iterarTab) };               

                /* ===== ===== Anclas ===== ===== */
                $('.productoServicios #myTabContentPrincipal .tab-pane.show.active .paragraph ul').children('li').each(function(index, element){                 
                    
                    let hreA = $(element).children('a').attr('ancla');                    
                    let hreAn = hreA + '-' + nomTabInterno;
                    $(element).children('a').attr('href', hreAn);                  

                }); 
                
                /* ===== ===== Contenido de las anclas ===== ===== */
                $('.productoServicios #myTabContentPrincipal .tab-pane.show.active #myTabContentInternas div.tab-pane').each(function(index, element){                 
                        
                    let hreA = $(element).attr('idAncla');                    
                    let hreAn = hreA + '-' + nomTabInterno;
                    $(element).attr('id', hreAn);

                });
            

            }            

            //Se ejecuta la funcion de cambio de atributos de anclas
            tabProductosServicios();

            /* ===== ===== Efecto para la descripcion de menu o grillas internas de las tabs ===== ===== */
            $("div#myTabContentInternas div.gridContainer div.grid div.gridContenido .paragraph .descripcion").hide();

            $("div#myTabContentInternas div.gridContainer div.grid div.gridContenido").on("click", function(){

                if($(this).attr('modo') === 'arriba'){

                    $(this).children('.paragraph').children('.descripcion').slideDown("slow");
                    $(this).children('.paragraph').children('.titulos').children('svg').removeClass('fa-chevron-left');
                    $(this).children('.paragraph').children('.titulos').children('svg').addClass('fa-chevron-down');
                    $(this).attr('modo', 'abajo');

                }else{
                    $(this).children('.paragraph').children('.descripcion').slideUp("slow");
                    $(this).children('.paragraph').children('.titulos').children('svg').removeClass('fa-chevron-down');
                    $(this).children('.paragraph').children('.titulos').children('svg').addClass('fa-chevron-left');
                    $(this).attr('modo', 'arriba');
                }
                

            });
            
            /*===========================================
            FINDETER CON CIFRAS
            =============================================*/
            
            /*$("div.findeterCifras .grid-container .grid-item .contenido").on("mouseover", function(){

                var imgSecundario = $('img', this).attr('imgSecundario');
                $('img', this).attr('style', 'content:url("'+imgSecundario+'");'
                                    +'transition: 1.5s;'
                                    +'-webkit-transition: 1.5s;'
                                    +'-moz-transition: 1.5s;'
                                    +'-ms-transition: 1.5s;'
                                    +'-o-transition: 1.5s;'
                                    +'-webkit-transform: rotateY(180deg);'
                                    +'-moz-transform: rotateY(180deg);'
                                    +'-o-transform: rotateY(180deg);'
                                    +'-ms-transform: rotateY(180deg);'
                                    +'transform: rotateY(180deg);'
                                );
                //$('img', this).attr('src', imgSecundario);   

            });

            $("div.findeterCifras .grid-container .grid-item .contenido").on("mouseout", function(){                
                var imgPrimario = $('img', this).attr('imgPrincipal');
                $('img', this).attr('style','content:url("'+imgPrimario+'");'
                                    +'transition: 1.5s;'
                                    +'-webkit-transition: 1.5s;'
                                    +'-moz-transition: 1.5s;'
                                    +'-ms-transition: 1.5s;'
                                    +'-o-transition: 1.5s;'
                                    
                                );
                //$('img', this).attr('src', imgPrimario);
               
            });*/

            
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
                            if(count < 1 ){
                                counter.innerText = (count + inc).toFixed(2);
                            }else{
                                if(Number.isInteger(target)){
                                    counter.innerText = Math.round(count + inc);
                                }else
                                {
                                    counter.innerText = (count + inc).toFixed(2);
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
            if(document.getElementById("block-findeter-cifras-home")){
                var alturacifras = $("#block-findeter-cifras-home").offset().top;
            }else{
                var alturacifras = '';
            }
            
            $(window).scroll(function(){
            
                if($(window).scrollTop() >= alturacifras + 10) {
            
                    contador();
            
                }
            
            });

            /* ========= Fin efecto =========== */
            


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
                
               
                $("div.casosExito").css({
                    'background':'url("'+imgFondo+'")'
                });

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

            /*===========================================
            TRANSPARENCIA
            =============================================*/

            /* ===== ===== MENU ===== ===== */
            $(".menuTransparenciaAcceso ul.nav div.gridContainer li.gridItem").on("click", function(){ 

                if($(this).attr("mostrar") == "true"){

                    $(this).children('ul').children('li').fadeIn(1000, function(){

                        $(this).parent().parent().children('div.gridItemTitulo').children('div.flecha').children('img').attr({'src':'/themes/custom/webfinde/images/iconos/flecha2.png'})
                    });

                    $(this).attr({"mostrar" : "false"});

                }else{

                    $(this).children('ul').children('li').fadeOut(1000, function(){

                        $(this).parent().parent().children('div.gridItemTitulo').children('div.flecha').children('img').attr({'src':'/themes/custom/webfinde/images/iconos/flecha.png'})
                    });

                    $(this).attr({"mostrar" : "true"});
                }                

            });            
            
             /*===========================================
            CONVOCATORIAS
            =============================================*/           
            let activarBusqueda = false;

            function formBusquedaAvanzada() {

                $(".convocatoriaCiudadano form div.form-item-created-min input[type='text']").addClass('datepicker InicialApertura').attr('readonly','');

                $(".convocatoriaCiudadano form div.form-item-created-max input[type='text']").addClass('datepicker FinalApertura').attr('readonly','');
                
                $(".convocatoriaCiudadano form div.form-item-created-max label").html('Hasta');

                /* ===== ===== Fechas ===== ===== */
                $(".datepicker.InicialApertura").datepicker({

                    language: "es",
                    format: 'dd-mm-yyyy',                
                    todayHighlight: true,                
                });

                $(".datepicker.InicialApertura").on("change", function(){

                    var fechaInicial = $(this).val();

                    $(".datepicker.FinalApertura").datepicker({

                        language: "es",
                        datesDisabled: fechaInicial - 1,
                        startDate: fechaInicial,    
                        format: 'dd-mm-yyyy',         
                    });

                });                

                $(".convocatoriaCiudadano form .form-actions input[id='edit-submit-convocatorias']").after(`
                    <a href="javascript:void(0)" class="text-white ml-5 busquedaAvz"><i class="fas fa-search-plus"></i> Mostar mas campos de busqueda</a>
                `);                

                if(activarBusqueda === false){

                    $(".convocatoriaCiudadano form .form--inline div:nth-child(3)").hide();
                    $(".convocatoriaCiudadano form .form--inline div:nth-child(4)").hide();
                    $(".convocatoriaCiudadano form .form--inline div:nth-child(5)").hide();
                    $(".convocatoriaCiudadano form .form--inline div:nth-child(6)").hide();
                    $(".convocatoriaCiudadano form .form--inline div:nth-child(7)").hide();
                    $(".convocatoriaCiudadano form .form--inline div:nth-child(8)").hide();
                    $(".convocatoriaCiudadano form .form--inline div:nth-child(9)").hide();
                }
               

                $(".convocatoriaCiudadano form .form-actions a").on('click', function(){

                    if(activarBusqueda === false){
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(3)").slideDown("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(4)").slideDown("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(5)").slideDown("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(6)").slideDown("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(7)").slideDown("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(8)").slideDown("slow");
                        activarBusqueda = true;
                    }else{
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(3)").slideUp("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(4)").slideUp("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(5)").slideUp("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(6)").slideUp("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(7)").slideUp("slow");
                        $(".convocatoriaCiudadano form .form--inline div:nth-child(8)").slideUp("slow");
                        activarBusqueda = false;
                    }
                });

                $(".convocatoriaCiudadano form .form-actions input[type='submit']").on('click', function(){
                    if(activarBusqueda){ activarBusqueda = true };
                    //console.log("🚀 ~ file: global.js ~ line 240 ~ $ ~ mostrarFiltros", activarBusqueda)
                });

            }

            $(document).ajaxStop(function() {                
                formBusquedaAvanzada();                  
            });

            formBusquedaAvanzada();

            /* ===== ===== INTERNAS DETALLE DE PROCESO y prosperity fund ===== ===== */
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
                order: [[ 2, 'desc' ], [ 0, 'asc' ]],
            });
        });
        /*}
    };*/

})(jQuery, Drupal);
            
