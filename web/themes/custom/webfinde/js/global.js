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

                $(this).children("figure").css({
                    "height": "96%", 
                    "transition" : ".5s all",
                    "background" : "#112d6a",
                    "border-radius" : "0.8em"
                });
             
            });

            $(".productosServicios .grid-container .grid-item").on('mouseout', function(){

                $(this).children("figure").css({
                    "height": "25%", 
                    "transition" : ".5s all",
                    "background" : "repeating-linear-gradient(184deg, transparent 0, transparent 19%, #296294 0, #296294",
                    "border-radius" : "0"});
                
            });

        }
    };

})(jQuery, Drupal);
