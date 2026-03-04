((Drupal, once) => {
  Drupal.behaviors.dropdown_menu_hover = {
    attach: (context) => {
      const getLgBreakpoint = () => {
        const lgBreakpoint = getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-lg') || '992px';
        return lgBreakpoint.trim();
      };

      const createMediaQuery = () => {
        const breakpoint = getLgBreakpoint();
        return window.matchMedia(`(min-width: ${breakpoint})`);
      };

      const applyHoverBehavior = (details) => {
        let content = details.querySelector('ul');

        content.onmouseover = () => {
          details.setAttribute('open', 'true');
        };

        content.onmouseleave = () => {
          details.removeAttribute('open');
        }

        details.onmouseover = () => {
          details.setAttribute('open', 'true');
        };

        details.onmouseleave = () => {
          details.removeAttribute('open');
        }
      };

      const removeHoverBehavior = (details) => {
        let content = details.querySelector('ul');
        content.onmouseover = null;
        content.onmouseleave = null;
        details.onmouseover = null;
        details.onmouseleave = null;
      };

      once('dropdown_menu_hover', 'details[data-component-id="nsc_theme:dropdown-menu"]', context).forEach((details) => {
        const mediaQuery = createMediaQuery();
        if (mediaQuery.matches) {
          applyHoverBehavior(details);
        }
        mediaQuery.addEventListener('change', (event) => {
          if (event.matches) {
            applyHoverBehavior(details);
          } else {
            removeHoverBehavior(details);
          }
        });
      });
    },
  };
})(Drupal, once);
