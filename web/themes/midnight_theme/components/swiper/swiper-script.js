import Swiper from 'swiper';
import { Navigation, Pagination, Scrollbar, Autoplay } from 'swiper/modules';
import 'swiper/css/bundle';


(function(Drupal, once, Swiper) {
  function setRunning(el, swiper) {
    if (swiper.autoplay.running) {
      el.classList.add('running');
    } else {
      el.classList.remove('running');
    }
  }

  const isReduced = window.matchMedia(`(prefers-reduced-motion: reduce)`) === true || window.matchMedia(`(prefers-reduced-motion: reduce)`).matches === true;

  Drupal.behaviors.swiper = {

    attach(context) {
      once('swiper-init', '.swiper', context)
        .forEach(el => {
          const config = {
            modules: [Navigation, Pagination, Scrollbar, Autoplay],
            direction: 'horizontal',
            speed: 500,
            loop: true,
            autoplay: {
              delay: 5000,
              pauseOnMouseEnter: true,
            },
            keyboard: true,
            pagination: {
              el: '.swiper-pagination',
              type: 'bullets',
              clickable: true,
            },
            navigation: {
              nextEl: '.swiper-button-next',
              prevEl: '.swiper-button-prev',
            },
            grabCursor: false,
            slidesPerView : 'auto',
            spaceBetween : 80,
            centeredSlides: true
          };
       
          const mySlider = new Swiper(el, config);

          // Prefers Reduced Motion browser flag check
          if (!!isReduced) {
            mySlider.autoplay.stop();
          }

          mySlider.on('autoplayPause', (swiper) => {
            setRunning(el, swiper);
          });
          mySlider.on('autoplayResume', (swiper) => {
            setRunning(el, swiper);
          });

          el.style.setProperty('--swiper-pagination-bullet-width', `calc(80% / ${mySlider.slides.length}`)
          if (mySlider.slides.length < 2) {
            el.classList.add('hide-all');
          }
          // el.style.setProperty('--swiper-count', mySlider.slides.length);
          const playBtn = el.querySelector('.play-btn');
          playBtn.addEventListener('click', () => {
            mySlider.autoplay.start();
            setRunning(el, mySlider);
          });
          const pauseBtn = el.querySelector('.pause-btn');
          pauseBtn.addEventListener('click', () => {
            mySlider.autoplay.stop();
            setRunning(el, mySlider);
          });

          setRunning(el, mySlider);
        });
    },
  };

})(Drupal, once, Swiper);
