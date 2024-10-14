(function(Drupal, once) {

  Drupal.behaviors.triggerClick = {
    attach(context) {
      once('button-click', '.toggle').forEach(
        el => {
          el.addEventListener("keypress", function(e){
            if(e.key==="Enter") {
              e.preventDefault();
              el.click(); 
            }
          })  
        }
      )
    }
  }

  Drupal.behaviors.js_check = {
    attach(context) {
      console.log('hello world')
      once('js-check', '.js-disabled').forEach(
        el => {
          if(el.classList.contains('js-disabled')){
            el.classList.remove('js-disabled')
          }
        }
      )
    }
  }

  Drupal.behaviors.footer_main_menu = {

    attach(context) {
      once('footer-nav', '.menu--main', context)
        .forEach(el => {
          let toggleMenus = el.querySelectorAll('.primary-nav__menu-item--level-1.primary-nav__menu-item--has-children');
          toggleMenus.forEach( (menu, idx) => {
            if(menu.querySelector('.is-active')){
              menu.classList.add('active-trail')
            }
            let toggle = menu.querySelector('.primary-nav__button-toggle');
            let child = menu.querySelector('.primary-nav__menu--level-2');
            child.setAttribute('id', 'footer-menu_'+idx);
            child.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-controls', 'footer-menu_'+idx);
            // todo abstract toggle function
            toggle.addEventListener('click', function(e) {
              e.preventDefault()
              if(!child.getAttribute('aria-expanded')=='true'){
                el.querySelectorAll('.primary-nav__menu--level-2').forEach(childMenu => {
                  if(childMenu.classList.contains('aria-expanded')){
                    toggleOpen(childMenu); 
                    toggleOpen(toggle);
                  }
                });
              }
              toggleOpen(child);
              toggleOpen(toggle);
             }, false);
          })
        });
    },
  };

  function toggleOpen(element) {
    element.getAttribute('aria-expanded') === 'true'
      ? element.setAttribute('aria-expanded', 'false')
      : element.setAttribute('aria-expanded', 'true')
  }
})(Drupal, once);
