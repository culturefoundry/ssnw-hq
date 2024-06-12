/* eslint-disable strict, no-undef, no-use-before-define */

/**
 * @file
 * Schema.org JSON-LD preview behaviors.
 */

((Drupal, once) => {
  /**
   * Schema.org JSON-LD preview copy.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgJsonLdPreviewCopy = {
    attach: function attach(context) {
      once(
        'schemadotorg-jsonld-preview-copy',
        '.js-schemadotorg-jsonld-preview',
        context,
      ).forEach((container) => {
        const input = container.querySelector('input[type="hidden"]');
        // eslint-disable-next-line
        const message = container.querySelector('.schemadotorg-jsonld-preview-copy-message');
        const button = container.querySelector('input[type="submit"], button');

        message.addEventListener('transitionend', hideMessage);

        button.addEventListener('click', (event) => {
          // Copy code from textarea to the clipboard.
          // @see https://stackoverflow.com/questions/47879184/document-execcommandcopy-not-working-on-chrome/47880284
          if (window.navigator.clipboard) {
            window.navigator.clipboard.writeText(input.value);
          }

          showMessage();

          Drupal.announce(Drupal.t('JSON-LD copied to clipboard…'));

          event.preventDefault();
        });

        // Show/hide message handling.
        // @see https://stackoverflow.com/questions/29017379/how-to-make-fadeout-effect-with-pure-javascript
        function showMessage() {
          message.style.display = 'inline-block';
          // eslint-disable-next-line
          setTimeout(() => {message.style.opacity = '0'}, 1500);
        }

        function hideMessage() {
          message.style.display = 'none';
          message.style.opacity = '1';
        }
      });
    },
  };
})(Drupal, once);
