/**
 * @file
 * Global utilities.
 *
 */

 (function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.rediscount = {

    attach: function (context, settings) {

      $(document, context).once('.rediscount').each(function() {

        const widthWindow = () =>  $(window).width() < 480 ? true : false;

        $('#sectors-icon').slick({
          infinite: true,
          slidesToShow: 6,
          slidesToScroll: 6,
          dots: true,
          mobileFirst: widthWindow(),
          responsive: [
            {
              breakpoint: 480,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
                arrows: false
              }
            }
          ],
        });

        $('[data-toggle="tooltip"]').tooltip();

        /*===========================================
        SECTORES
        =============================================*/
        let sectorsInfo = document.querySelector('.sectors-info');
        let title = document.querySelector('.sectors-info .titles');
        let description = document.querySelector('.sectors-info .description');
        let containerImage = document.querySelector('.sectors-info .images');
        let image = document.querySelector('.sectors-info .images img');

        let moreInfo = document.querySelector('.sectors-info .more-info');
        let subtitle = moreInfo.querySelector('.subtitle');
        let moreNav = moreInfo.querySelector('.more-info__nav ul');
        let moreContent = moreInfo.querySelector('.more-info__nav .tab-content');

        let elementDivIcon = Node;

        document.querySelectorAll('#sectors-icon .sectors-icon__item')
        .forEach(el => {

          let icon = el.querySelector('.icon');
          let iconSecond = el.querySelector('.iconSecond');

          // Funcion que cambia y selecciona el icono del sector.
          const iconImgblue = (element = null) => {

            if (element !== null) {
              if (elementDivIcon !== Node) {
                let removeClassIcon = elementDivIcon.querySelector('.icon');
                removeClassIcon.classList.remove('d-none');
                let removeClassIconSecond = elementDivIcon.querySelector('.iconSecond');
                removeClassIconSecond.classList.remove('d-block');
              }
            }

            icon.classList.toggle('d-none');
            iconSecond.classList.toggle('d-block');
          }

          el.addEventListener('click', event => {

            // Se obtiene el nid del nodo desde el atributo.
            let nid = el.getAttribute('nid');

            // Se valida un selected al icono del sector seleccionado.
            if (elementDivIcon !== el)
              iconImgblue(el);
            elementDivIcon = el; // Se pasa un nodo nuevo para ser borrado al seleccionar otro sector.

            // Removemos la data con alguna animacion mientras se espera la nueva peticion con
            // informacion enviada
            title.textContent = '';
            title.classList.add('skeleton');

            description.textContent = '';
            description.classList.add('skeleton');

            image.removeAttribute('src');
            containerImage.classList.add('skeleton');

            sectorsInfo.classList.add('card-skeleton');
            sectorsInfo.classList.remove('show-info');

            moreInfo.classList.add('skeleton');

            subtitle.textContent = '';
            moreNav.innerHTML = '';
            moreContent.innerHTML = '';

            fetch('/findeter-rediscount/sectorsinfo/'+nid, {
              method: "GET",
            })
            .then(response => {
              if (response.ok){

                title.classList.remove('skeleton');
                description.classList.remove('skeleton');
                containerImage.classList.remove('skeleton');
                moreInfo.classList.remove('skeleton');

                sectorsInfo.classList.remove('card-skeleton');
                sectorsInfo.classList.add('show-info');

                return response.json()
              }
            })
            .then((data) => {

              title.textContent = data.sectors.title;
              description.textContent = data.sectors.description;
              image.setAttribute('src', data.sectors.image);

              let moreNavHTML = "";
              let moreContentHTML = "";

              data.sectors.subsectors.map((sect, idx) => {

                if (sect.title !== null) {

                  let active = idx === 0 ? "active" : "";
                  let showActive = idx === 0 ? 'show active': "";

                  moreNavHTML += '<li class="nav-item" role="presentation"><button class="nav-link btn-block '+ active +'" id="pills-subsectors-'+ idx +'-tab" data-toggle="pill" data-target="#subsectors-'+ idx +'" type="button" role="tab" aria-controls="subsectors-'+ idx +'" aria-selected="true">'+ sect.title +'</button></li>';

                  moreContentHTML += '<div class="tab-pane fade '+ showActive +'" id="subsectors-'+ idx +'" role="tabpanel" aria-labelledby="pills-subsectors-'+ idx +'-tab">'+ sect.description +'</div>';

                  subtitle.textContent = "Conozca los subsectores financiables por findeter";
                }

              });

              moreNav.innerHTML = moreNavHTML;
              moreContent.innerHTML = moreContentHTML;

            })
            .catch(function (error) {
              console.error("ERROR SECTORS: ", error.message)
            })
            .finally(()=>{
              // console.log('final');
            })
          });

          el.addEventListener('mouseover', event => {
            iconImgblue();
          });

          el.addEventListener('mouseout', event => {
            iconImgblue();
          });

        });

        /*===========================================
        BUSCAR CODIGO CIIU
        =============================================*/

        // Aceptar solo numeros.
        const valideKey = (e) => {
          var key = e.charCode;
          return key >= 48 && key <= 57;
        }

        const textEditCode = document.querySelector("#edit-code");
        if(textEditCode !== null)
          textEditCode.addEventListener("keypress", event => { if(!valideKey(event)) event.preventDefault(); });

        // Consultar codigo.
        const btnEditCode = document.querySelector("#edit-send-code");

        if(btnEditCode !== null) {
          const ResponseCode = document.querySelector("#response-search-ciiu");
          const loader = document.querySelector(".loader");

          btnEditCode.addEventListener("click", event => {

            event.preventDefault();

            if(textEditCode.value !== "") {

              loader.classList.toggle("d-block");
              ResponseCode.classList.toggle("d-none");

              // Enviamos la peticion como una promesa y ejecutamos la consulta.
              setTimeout(() => {
                fetch('/findeter-rediscount/search-code-ciiu/'+textEditCode.value, {
                  method: "GET",
                })
                .then(response => {

                  if (response.ok){

                    loader.classList.toggle("d-block");
                    ResponseCode.classList.toggle("d-none");
                    return response.json();

                  }
                })
                .then((data) => {

                  let countData = Object.entries(data.ciiu);
                  let datas = data.ciiu;

                  if( countData.length !== 0 ) {

                    let HtmlResponseCode = '';

                    if (datas.bankables == 1) {

                      HtmlResponseCode += "<p>El CIIU consultado hace parte de la actividad <strong>"+datas.activity+"</strong>";

                      if (datas.sectors !== undefined ) {
                        datas.sectors.forEach(item => {
                          HtmlResponseCode += ", la cual aplica a sector(s) "+item.title+" ";
                        });
                      }

                      HtmlResponseCode += "</p><ul class='text-white mt-3'><li>Esta información podrá variar y debe ser consultada según el proyecto a financiar.</li></ul>";

                    } else {

                      HtmlResponseCode += "<p>El CIIU consultado <strong>NO</strong> hace parte de los sectores financibles por Findeter, de igual forma puede consultar con un asesor comercial.</p>";

                    }

                    ResponseCode.innerHTML = HtmlResponseCode;


                  } else {

                    ResponseCode.innerHTML = "<p>Por favor contacte con un gerente de cuenta para revisar si la actividad económica es financiable por Findeter.</p>";

                  }

                })
                .catch(function (error) {
                  console.error("ERROR SECTORS: ", error.message)
                })
              }, 2000);
            }

          });
        }

      });

    }
  };
})(jQuery, Drupal);