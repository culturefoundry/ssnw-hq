/**
 * @file
 * JavaScript behaviors for settings element.
 *
 * @see webform/js/webform.element.more.js
 */

((Drupal, once) => {
  /**
   * Settings element example.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgSettingsElementExample = {
    attach: function attach(context) {
      once(
        'schemadotorg-settings-example',
        '.schemadotorg-settings-example',
        context,
      ).forEach((element) => {
        const a = element.querySelector('a');
        // eslint-disable-next-line
        const content = element.querySelector('.schemadotorg-settings-example--content');

        // Add aria-* attributes.
        a.setAttribute('aria-expanded', false);
        a.setAttribute('aria-controls', content.getAttribute('id'));

        // Move example after description and style it like the description.
        const description = element.parentNode;
        description.after(element);
        element.classList.add(description.classList);

        // Define the toggle event handler.
        function toggle(event) {
          const expanded = a.getAttribute('aria-expanded') === 'true';

          // Toggle `aria-expanded` attributes on link.
          a.setAttribute('aria-expanded', !expanded);

          // Toggle content and more .is-open state.
          if (expanded) {
            element.classList.remove('is-open');
          } else {
            element.classList.add('is-open');
          }

          event.preventDefault();
        }

        // Add event handlers.
        a.parentNode.addEventListener('click', toggle);
        a.parentNode.addEventListener('keydown', (event) => {
          // Space or Return.
          if (event.which === 32 || event.which === 13) {
            toggle(event);
          }
        });
      });
    },
  };
})(Drupal, once);
