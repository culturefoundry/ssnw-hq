/* eslint-disable prefer-destructuring */

/**
 * @file
 * Schema.org jsTree behaviors.
 */

(($, Drupal, once) => {
  // @see https://www.jstree.com/docs/config/
  const jsTreeConfig = {
    core: {
      themes: {
        icons: false,
      },
    },
  };

  /**
   * Schema.org Report jsTree behaviors.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgJsTree = {
    attach: function attach(context) {
      // eslint-disable-next-line
      once('schemadotorg-jstree', '.schemadotorg-jstree', context).forEach((tree) => {
          // Remove <div> from nested list markup.
          tree.innerHTML = tree.innerHTML.replace(/<\/?div[^>]*>/g, '');

          // Initialize the jstree.
          const $jstree = $(tree.parentNode).jstree(jsTreeConfig);

          // Enable links.
          // @see https://stackoverflow.com/questions/8378561/js-tree-links-not-active
          // eslint-disable-next-line
          $jstree.on(
            'activate_node.jstree',
            function handleActiveNodeJsTree(e, data) {
              const href = data.node.a_attr.href;
              if (Drupal.schemaDotOrgOpenDialog) {
                Drupal.schemaDotOrgOpenDialog(href);
              } else {
                window.location.href = href;
              }
              return false;
            },
          );

          // Create toggle button.
          const collapseLabel = Drupal.t('Collapse all');
          const expandLabel = Drupal.t('Expand all');

          const button = document.createElement('button');
          button.setAttribute('type', 'button');
          // eslint-disable-next-line
          button.setAttribute('class', 'schemadotorg-jstree-toggle link action-link');
          button.innerText = expandLabel;

          button.addEventListener('click', () => {
            const toggle = $jstree.data('toggle') || false;
            if (!toggle) {
              $jstree.jstree('open_all');
            } else {
              $jstree.jstree('close_all');
            }
            button.innerText = toggle ? expandLabel : collapseLabel;
            $jstree.data('toggle', !toggle);
          });

          const div = document.createElement('div');
          div.setAttribute('class', 'schemadotorg-jstree-toggle-wrapper');
          div.appendChild(button);

          // Prepend toggle button to the jstree's DOM element.
          const jstree = $jstree[0];
          jstree.parentNode.insertBefore(div, jstree);
        },
      );
    },
  };
})(jQuery, Drupal, once);
