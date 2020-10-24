/**
 * @file
 * Global utilities.
 *
 */
(function($, Drupal) {

    'use strict';

    /*Drupal.behaviors.webfinde = {
        attach: function(context, settings) {*/

            var Rcar = document.getElementsByClassName('stlRCarIn');
            var Rcentro = document.getElementsByClassName('stlRCtroIn');
            var Rpaci = document.getElementsByClassName('stlRPIn');
            var Rejeca = document.getElementsByClassName('stlRECIn');
            var Rnoro = document.getElementsByClassName('stlRNorIn'); 
            var Rnocc = document.getElementsByClassName('stlRNocIn'); 


            var stlRCarIn = document.getElementById("stlRCarIn");
            var stlRCtroIn = document.getElementById("stlRCtroIn");
            var stlRPIn = document.getElementById("stlRPIn");
            var stlRECIn = document.getElementById("stlRECIn");
            var stlRNorIn = document.getElementById("stlRNorIn");
            var stlRNocIn = document.getElementById("stlRNocIn");


            for (var i = 0; i<Rnocc.length; i++){
                Rnocc[i].addEventListener("click",function(){
                    for (var j = 0; j<Rnocc.length; j++){
                        Rnocc[j].style.fill = '#233568';
                    }
                    for (var j = 0; j<Rcar.length; j++){
                        Rcar[j].style.fill = '#ccecf9';
                    }

                    for (var j = 0; j<Rcentro.length; j++){
                        Rcentro[j].style.fill = '#cfd8e0';
                    }
                    for (var j = 0; j<Rpaci.length; j++){
                        Rpaci[j].style.fill = '#d2e4eb';
                    }
                    for (var j = 0; j<Rejeca.length; j++){
                        Rejeca[j].style.fill = '#ebf1f9';
                    }
                    for (var j = 0; j<Rnoro.length; j++){
                        Rnoro[j].style.fill = '#ccdbe9';
                    }
                    stlRCarIn.setAttribute('hidden', true);
                    stlRCtroIn.setAttribute('hidden', true);
                    stlRPIn.setAttribute('hidden', true);
                    stlRECIn.setAttribute('hidden', true);
                    stlRNorIn.setAttribute('hidden', true);
                    stlRNocIn.removeAttribute('hidden');
                });
            }

            for (var i = 0; i<Rnoro.length; i++){
                Rnoro[i].addEventListener("click",function(){
                    for (var j = 0; j<Rnoro.length; j++){
                        Rnoro[j].style.fill = '#00498f';
                    }
                    for (var j = 0; j<Rcar.length; j++){
                        Rcar[j].style.fill = '#ccecf9';
                    }

                    for (var j = 0; j<Rcentro.length; j++){
                        Rcentro[j].style.fill = '#cfd8e0';
                    }
                    for (var j = 0; j<Rpaci.length; j++){
                        Rpaci[j].style.fill = '#d2e4eb';
                    }
                    for (var j = 0; j<Rejeca.length; j++){
                        Rejeca[j].style.fill = '#ebf1f9';
                    }
                    for (var j = 0; j<Rnocc.length; j++){
                        Rnocc[j].style.fill = '#d3d7e1';
                    }

                    stlRCarIn.setAttribute('hidden', true);
                    stlRCtroIn.setAttribute('hidden', true);
                    stlRPIn.setAttribute('hidden', true);
                    stlRECIn.setAttribute('hidden', true);
                    stlRNorIn.removeAttribute('hidden');
                    stlRNocIn.setAttribute('hidden', true);
                });
            }

            for (var i = 0; i<Rejeca.length; i++){
                Rejeca[i].addEventListener("click",function(){
                    for (var j = 0; j<Rejeca.length; j++){
                        Rejeca[j].style.fill = '#9db9e2';
                    }
                    for (var j = 0; j<Rcar.length; j++){
                        Rcar[j].style.fill = '#ccecf9';
                    }

                    for (var j = 0; j<Rcentro.length; j++){
                        Rcentro[j].style.fill = '#cfd8e0';
                    }
                    for (var j = 0; j<Rpaci.length; j++){
                        Rpaci[j].style.fill = '#d2e4eb';
                    }
                    for (var j = 0; j<Rnoro.length; j++){
                        Rnoro[j].style.fill = '#ccdbe9';
                    }
                    for (var j = 0; j<Rnocc.length; j++){
                        Rnocc[j].style.fill = '#d3d7e1';
                    }
                    stlRCarIn.setAttribute('hidden', true);
                    stlRCtroIn.setAttribute('hidden', true);
                    stlRPIn.setAttribute('hidden', true);
                    stlRECIn.removeAttribute('hidden');
                    stlRNorIn.setAttribute('hidden', true);
                    stlRNocIn.setAttribute('hidden', true);
                });
            }

            for (var i = 0; i<Rpaci.length; i++){
                Rpaci[i].addEventListener("click",function(){
                    for (var j = 0; j<Rpaci.length; j++){
                        Rpaci[j].style.fill = '#1f7a9a';
                    }
                    for (var j = 0; j<Rcar.length; j++){
                        Rcar[j].style.fill = '#ccecf9';
                    }

                    for (var j = 0; j<Rcentro.length; j++){
                        Rcentro[j].style.fill = '#cfd8e0';
                    }
                    for (var j = 0; j<Rejeca.length; j++){
                        Rejeca[j].style.fill = '#ebf1f9';
                    }
                    for (var j = 0; j<Rnoro.length; j++){
                        Rnoro[j].style.fill = '#ccdbe9';
                    }
                    for (var j = 0; j<Rnocc.length; j++){
                        Rnocc[j].style.fill = '#d3d7e1';
                    }

                    stlRCarIn.setAttribute('hidden', true);
                    stlRCtroIn.setAttribute('hidden', true);
                    stlRPIn.removeAttribute('hidden');
                    stlRECIn.setAttribute('hidden', true);
                    stlRNorIn.setAttribute('hidden', true);
                    stlRNocIn.setAttribute('hidden', true);
                });
            }

            for (var i = 0; i<Rcentro.length; i++){
                Rcentro[i].addEventListener("click",function(){
                    for (var j = 0; j<Rcentro.length; j++){
                        Rcentro[j].style.fill = '#0d3e66';
                    }
                    for (var j = 0; j<Rcar.length; j++){
                        Rcar[j].style.fill = '#ccecf9';
                    }
                    for (var j = 0; j<Rpaci.length; j++){
                        Rpaci[j].style.fill = '#d2e4eb';
                    }
                    for (var j = 0; j<Rejeca.length; j++){
                        Rejeca[j].style.fill = '#ebf1f9';
                    }
                    for (var j = 0; j<Rnoro.length; j++){
                        Rnoro[j].style.fill = '#ccdbe9';
                    }
                    for (var j = 0; j<Rnocc.length; j++){
                        Rnocc[j].style.fill = '#d3d7e1';
                    }
                    stlRCarIn.setAttribute('hidden', true);
                    stlRCtroIn.removeAttribute('hidden');
                    stlRPIn.setAttribute('hidden', true);
                    stlRECIn.setAttribute('hidden', true);
                    stlRNorIn.setAttribute('hidden', true);
                    stlRNocIn.setAttribute('hidden', true);
                });
            }
    
            for (var i = 0; i<Rcar.length; i++){
                Rcar[i].addEventListener("click",function(){
                    for (var j = 0; j<Rcar.length; j++){
                        Rcar[j].style.fill = '#009fe3';
                    }
                    for (var j = 0; j<Rcentro.length; j++){
                        Rcentro[j].style.fill = '#cfd8e0';
                    }
                    for (var j = 0; j<Rpaci.length; j++){
                        Rpaci[j].style.fill = '#d2e4eb';
                    }
                    for (var j = 0; j<Rejeca.length; j++){
                        Rejeca[j].style.fill = '#ebf1f9';
                    }
                    for (var j = 0; j<Rnoro.length; j++){
                        Rnoro[j].style.fill = '#ccdbe9';
                    }
                    for (var j = 0; j<Rnocc.length; j++){
                        Rnocc[j].style.fill = '#d3d7e1';
                    }

                    stlRCarIn.removeAttribute('hidden');
                    stlRCtroIn.setAttribute('hidden', true);
                    stlRPIn.setAttribute('hidden', true);
                    stlRECIn.setAttribute('hidden', true);
                    stlRNorIn.setAttribute('hidden', true);
                    stlRNocIn.setAttribute('hidden', true);
                });
            }
        /*}
    };*/
    
})(jQuery, Drupal);