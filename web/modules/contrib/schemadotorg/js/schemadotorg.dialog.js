/**
 * @file
 * Schema.org dialog behaviors.
 */

((Drupal, once) => {
  /**
   * Open Schema.org type and property report links in a modal dialog.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgDialog = {
    attach: function attach(context) {
      once(
        'schemadotorg-dialog',
        'a[href*="/admin/reports/schemadotorg"]',
        context,
      ).forEach((link) => {
        // Skip links in the toolbar-bar.
        if (link.closest('nav.toolbar-bar')) {
          return;
        }

        Drupal.ajax({
          progress: { type: 'fullscreen' },
          url: link.getAttribute('href'),
          event: 'click',
          dialogType: 'modal',
          dialog: { width: '1000px' },
          element: link,
        });
      });
    },
  };

  /**
   * Programmatically open a Schema.org type or property in a dialog.
   *
   * @param {string} url
   *   Webform URL.
   */
  Drupal.schemaDotOrgOpenDialog = function schemaDotOrgOpenDialog(url) {
    if (url.indexOf('/admin/reports/schemadotorg/') === -1) {
      window.location.href = url;
    } else {
      // Create a link but don't attach it to the page.
      const link = document.createElement('a');
      link.setAttribute('href', url);

      // Init the dialog behavior.
      Drupal.behaviors.schemaDotOrgDialog.attach(link);

      // Trigger the link.
      link.click();
    }
  };
})(Drupal, once);
