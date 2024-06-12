/* eslint-disable no-use-before-define */

/**
 * @file
 * Schema.org UI behaviors.
 */

(($, Drupal, debounce, once) => {
  /**
   * Schema.org UI field prefix.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgFieldPrefix = {
    attach: function attach(context) {
      // eslint-disable-next-line
      once('schemadotorg-field-prefix', 'input[name="label"]', context).forEach((labelInput) => {
          // eslint-disable-next-line
          const schemaDotOrgLabelInput = document.querySelector('input[name="schemadotorg_label"]');
          const formUpdatedEvent = new Event('formUpdated');
          schemaDotOrgLabelInput.addEventListener('change', () => {
            labelInput.value = schemaDotOrgLabelInput.value;
            labelInput.dispatchEvent(formUpdatedEvent);
          });
          labelInput.addEventListener('change', () => {
            schemaDotOrgLabelInput.value = labelInput.value;
            schemaDotOrgLabelInput.dispatchEvent(formUpdatedEvent);
          });
        },
      );
    },
  };
})(jQuery, Drupal, Drupal.debounce, once);
