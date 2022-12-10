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

        $('#sectors-icon').not('.slick-initialized').slick({
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

        document.querySelectorAll('#sectors-icon .sectors-icon__item')
        .forEach(el => {

          let icon = el.querySelector('.icon');
          let iconSecond = el.querySelector('.iconSecond');

          el.addEventListener('click', event => {

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

            let nid = el.getAttribute('nid');

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

                let active = idx === 0 ? "active" : "";
                let showActive = idx === 0 ? 'show active': "";

                moreNavHTML += '<li class="nav-item" role="presentation"><button class="nav-link btn-block '+ active +'" id="pills-subsectors-'+ idx +'-tab" data-toggle="pill" data-target="#subsectors-'+ idx +'" type="button" role="tab" aria-controls="subsectors-'+ idx +'" aria-selected="true">'+ sect.title +'</button></li>';

                moreContentHTML += '<div class="tab-pane fade '+ showActive +'" id="subsectors-'+ idx +'" role="tabpanel" aria-labelledby="pills-subsectors-'+ idx +'-tab">'+ sect.description +'</div>';

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
            icon.classList.toggle('d-none');
            iconSecond.classList.toggle('d-block');
          });

          el.addEventListener('mouseout', event => {
            icon.classList.toggle('d-none');
            iconSecond.classList.toggle('d-block');
          });
        });

      });

    }
  };
})(jQuery, Drupal);