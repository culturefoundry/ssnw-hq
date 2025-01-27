/* eslint-disable no-undef */

/**
 * @file
 * Schema.org mermaid behaviors.
 */

((Drupal, mermaid, once) => {
  /**
   * Schema.org mermaid behaviors.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.schemaDotOrgMermaid = {
    attach: function attach(context) {
      const mermaids = once('mermaid', '.mermaid, .language-mermaid', context);
      if (!mermaids.length) {
        return;
      }

      let closedDetails = [];
      mermaids.forEach((element) => {
        // Track closed details and open them to after diagram is rendered.
        let parentElement = element.parentNode;
        while (parentElement) {
          // eslint-disable-next-line
          if (parentElement.tagName === 'DETAILS' && !parentElement.getAttribute('open')) {
            parentElement.setAttribute('open', 'open');
            closedDetails.push(parentElement);
          }
          parentElement = parentElement.parentNode;
        }
      });

      // Via post render close opened details and svg-pan-zoom
      mermaid.run({
        querySelector: '.mermaid, .language-mermaid',
        postRenderCallback: () => {
          // Use set timeout to delay closing details until all diagrams are rendered.
          window.setTimeout(function closeDetails() {
            if (closedDetails) {
              closedDetails.forEach((element) =>
                element.removeAttribute('open'),
              );
            }
            // Set closed details to null to only trigger closing details once.
            closedDetails = null;
          });

          // @see https://github.com/ariutta/svg-pan-zoom
          if (window.svgPanZoom) {
            svgPanZoom('.mermaid svg, .language-mermaid svg', {
              controlIconsEnabled: true,
            });
          }
        },
      });
    },
  };
})(Drupal, mermaid, once);
