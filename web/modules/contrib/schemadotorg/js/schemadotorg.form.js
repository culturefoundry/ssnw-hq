/**
 * @file
 * Schema.org form behaviors.
 */

((Drupal, once) => {
  /**
   * Schema.org form behaviors.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgFormSubmitOnce = {
    attach: function attach(context) {
      once(
        'schemadotorg-submit-once',
        'form.js-schemadotorg-submit-once',
        context,
      ).forEach((form) => {
        const submit = form.querySelector('.form-actions input[type="submit"]');

        // Disable the submit button, remove the cancel link,
        // and display a progress throbber.
        form.addEventListener('submit', () => {
          // Determine if the admin_dialog.module's spinner is enabled.
          // @see \Drupal\admin_dialogs\AdminDialogsModule::form_alter
          // eslint-disable-next-line
          const adminDialogSpinner = submit.parentNode.classList.contains('admin-dialogs-button-wrapper')
          if (!adminDialogSpinner) {
            submit.disabled = true;
            const throbber = Drupal.theme.ajaxProgressThrobber();
            submit.insertAdjacentHTML('afterend', throbber);
          }

          // eslint-disable-next-line
          const cancelLink = submit.parentNode.parentNode.querySelector('#edit-cancel');
          if (cancelLink) {
            cancelLink.remove();
          }
        });
      });
    },
  };
})(Drupal, once);
