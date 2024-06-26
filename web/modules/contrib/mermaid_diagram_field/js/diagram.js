/**
* @file
* Support mermaid rendering.
*/

(function (Drupal, mermaid, $) {

  "use strict";

  Drupal.behaviors.diagramDisplay = {
    attach: function (context) {
      // Display mermaid containers after they're rendered.
      const mermaids = context.querySelectorAll('.mermaid');
      $(mermaids).once('diagram-processed').each(function () {
        // Initialize mermaid only if mermaids exist.
        if (mermaids.length > 0) {
          mermaid.initialize(
            {
              startOnLoad: true,
              mermaid: {
                callback: (function() {
                  return function(id) {
                    for (let index = 0; index < mermaids.length; index++) {
                      mermaids[index].style.opacity = '100%';
                    }
                  }
                })()
              }
            });
        }
      });
      }
    };

  })(Drupal, mermaid, jQuery);
