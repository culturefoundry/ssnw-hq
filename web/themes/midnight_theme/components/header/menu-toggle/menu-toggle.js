(function (Drupal, once) {

  /**
   * Main menu toggle.
   */
  Drupal.behaviors.toggleMainMenu = {
    attach: function (context) {
      once('toggleMainMenu', '.menu-toggle', context)
        .forEach(menuToggle => {
          menuToggle.addEventListener('click', (event) => {
            toggleOpen(menuToggle);
          })
      })
    },
  }




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
  function toggleOpen(element) {
    element.getAttribute('aria-expanded') === 'true'
      ? element.setAttribute('aria-expanded', 'false')
      : element.setAttribute('aria-expanded', 'true')
  }

})(Drupal, once)
