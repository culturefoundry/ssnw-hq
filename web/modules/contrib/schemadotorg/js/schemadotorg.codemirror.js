/* eslint-disable max-len, func-names, no-undef */

/**
 * @file
 * Schema.org settings element behaviors.
 */

((Drupal, once, tabbable) => {
  /**
   * CodeMirror options.
   *
   * @type {object}
   */
  const options = {
    mode: 'yaml',
    lineNumbers: true,
    matchBrackets: true,
    extraKeys: {
      // Setting for using spaces instead of tabs.
      // @see https://github.com/codemirror/CodeMirror/issues/988
      Tab: (cm) => {
        const spaces = Array(cm.getOption('indentUnit') + 1).join(' ');
        cm.replaceSelection(spaces, 'end', '+element');
      },
      // On 'Escape' move to the next tabbable input.
      // @see http://bgrins.github.io/codemirror-accessible/
      Esc: (cm) => {
        const textarea = cm.getTextArea();
        // Must show and then textarea so that we can determine
        // its tabindex.
        textarea.classList.add('visually-hidden');
        textarea.setAttribute('style', 'display: block');
        const tabbableElements = tabbable.tabbable(document);
        const tabindex = tabbableElements.indexOf(textarea);
        textarea.setAttribute('style', 'display: none');
        textarea.classList.remove('visually-hidden');
        // Tabindex + 2 accounts for the CodeMirror's iframe.
        tabbableElements[tabindex + 2].focus();
      },
    },
  };

  /**
   * Schema.org settings element YAML behavior.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgSettingsElementYaml = {
    attach: function attach(context) {
      if (!window.CodeMirror) {
        return;
      }

      once(
        'schemadotorg-codemirror',
        '.schemadotorg-codemirror',
        context,
      ).forEach((element) => {
        // Track closed details and open them to initialize CodeMirror.
        // @see https://github.com/codemirror/codemirror5/issues/61
        const closedDetails = [];
        // Track display:none and show them to initialize CodeMirror.
        const displayNoneElements = [];
        let parentElement = element.parentNode;
        while (parentElement) {
          // eslint-disable-next-line
          if (parentElement.tagName === 'DETAILS' && !parentElement.getAttribute('open')) {
            parentElement.setAttribute('open', 'open');
            closedDetails.push(parentElement);
          }
          if (parentElement.style && parentElement.style.display === 'none') {
            parentElement.style.display = 'block';
            displayNoneElements.push(parentElement);
          }
          parentElement = parentElement.parentNode;
        }

        // Set mode from data attribute.
        options.mode = element.getAttribute('data-mode') || options.mode;

        // Initialize CodeMirror.
        CodeMirror.fromTextArea(element, options);

        // Close opened details.
        if (closedDetails) {
          // eslint-disable-next-line
          closedDetails.forEach((detailsElement) => detailsElement.removeAttribute('open'));
        }
        // Display none.
        if (displayNoneElements) {
          // eslint-disable-next-line
          displayNoneElements.forEach((displayNoneElement) => displayNoneElement.setAttribute('style', 'display:none'));
        }
      });
    },
  };

  /**
   * Code mirror preformatted code.
   *
   * @type {Drupal~behavior}
   * @see http://codemirror.net/demo/runmode.html
   */
  Drupal.behaviors.schemaDotOrgCodemirrorPre = {
    attach: function attach(context) {
      if (!window.CodeMirror || !window.CodeMirror.runMode) {
        return;
      }

      once(
        'schemadotorg-codemirror-mode',
        'pre[data-schemadotorg-codemirror-mode]',
        context,
      ).forEach((element) => {
        element.classList.add('cm-s-default');
        element.classList.add('schemadotorg-codemirror-mode');

        CodeMirror.runMode(
          element.textContent,
          element.dataset.schemadotorgCodemirrorMode,
          element,
        );
      });
    },
  };
})(Drupal, once, tabbable);
