((Drupal, once) => {

  function jumpTo(evt) {
    const url = evt.target.value;
    window.location.assign(url);
  }

  Drupal.behaviors.jumpmenu = {
    attach(context) {
      once(
        'jumpmenu',
        '[data-fedway-jumpmenu="active"]',
        context
      ).forEach((item) => {
        item.onchange = jumpTo;
      });
    }
  };
})(Drupal, once);

