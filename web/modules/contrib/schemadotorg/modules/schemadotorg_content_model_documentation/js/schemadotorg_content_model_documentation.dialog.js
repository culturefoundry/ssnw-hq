/**
 * @file
 * Schema.org Content Model Documentation dialog behaviors.
 */

((Drupal, once) => {
  /**
   * Open Schema.org Content Model Documentation links in a modal dialog.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgContentModelDocumentationDialog = {
    attach: function attach(context) {
      once(
        'schemadotorg-content-model-documentation-dialog',
        '.field--name-notes a[href*="/"][href$="/document"]',
        context,
      ).forEach((link) => {
        Drupal.ajax({
          progress: { type: 'fullscreen' },
          url: link.getAttribute('href'),
          event: 'click',
          dialogType: 'modal',
          dialog: { width: '800px', minHeight: '500px' },
          element: link,
        });
      });
    },
  };
})(Drupal, once);
