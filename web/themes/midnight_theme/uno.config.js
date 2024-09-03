// uno.config.js
import {
  defineConfig, presetWebFonts, presetUno,
  presetTypography, transformerDirectives,
  transformerVariantGroup,
} from 'unocss';
import presetIcons from '@unocss/preset-icons';
// import presetGridAreas from 'unocss-preset-grid-areas';

export default defineConfig({
  presets: [
    presetUno({
      preflight: true
    }),
    presetWebFonts({
      provider: 'google',
      fonts: {
        sans: 'Hind:300,400,500,600,700',
        serif: 'EB Garamond',
        secondary: 'Roboto',
        alfaslab: 'Alfa Slab One:100,200,300,400,500,600,700',
      },
    }),
    // presetGridAreas({
    //   gridTemplateAreas: {
    //     header: [
    //       'logo menu burger',
    //     ],
    //     headerm: [
    //       'logo burger',
    //     ],
    //     flip: [
    //       'user',
    //       'nav',
    //     ],
    //   },
    // }),
    // presetTypography({
    //   fonts: {
        
    //   }
    // }),
    presetIcons({
      collections: {
        'mdi': () => import('@iconify-json/mdi/icons.json').then(i => i.default),
        'gg': () => import('@iconify-json/gg/icons.json').then(i => i.default),
        'bi': () => import('@iconify-json/bi/icons.json').then(i => i.default),
        'dashicons': () => import('@iconify-json/dashicons/icons.json').then(i => i.default),
        'ssnw': () => import('./images/icons/icons.json').then(i => i.default),
      },
    }),
  ],
  shortcuts: [
    { 'type-base': 'font-sans font-light text-md leading-tight color-main' },
    { 'type-formatted': 'font-sans font-light text-md leading-normal color-main' },
    { 'type-sm': 'font-sans font-light text-sm leading-tight color-main' },
  ],
  safelist: [
    'i-mdi-login',
    'i-mdi-logout',
    'i-mdi-arrow-right-thick',
    'i-mdi-search',
  ],
  theme: {
    container: {
      center: true,
      padding: {
        'DEFAULT': '2em',
      },
      width: {
        'DEFAULT': 'calc(100% - 4em)',
      },
    },
    colors: {
      /** Color palette */
      primary: '#000', // Black text, header background
      secondary: '#F8C417', // Yellow highlight
      tertiary: '#333333', // Dark

      /** Semantic colors */
      // body text
      main: 'var(--main-color)',
      pagebg: 'var(--color-pagebg)',
      background: 'var(--background-color)',
      footerbg: 'var(--color-footerbg)',
      // Links, Headers
      highlight1: 'var(--color-highlight1)',
      highlight2: 'var(--color-highlight2)',
      accent1: 'var(--color-accent1)',
      accent2: 'var(--color-accent2)',
      linkcolor: 'var(--link-color)',
      hovercolor: 'var(--hover-color)',
      theme: 'var(--color-theme)',
      // other colors
      dark1: '#202020',
      dark2: '#333333',
      themeblue: '#00549F'
    },
    spacing: {
      bleed: 'calc(50% - 50vw)', // container padding
      unbleed: 'calc(50vw - 50%)',
    },
    fontSize: {
      'xs': 'var(--step--2)',
      'sm': 'var(--step--1)',
      'md': 'var(--step-0)',
      'lg': 'var(--step-1)',
      'xl': 'var(--step-2)',
      '2xl': 'var(--step-3)',
      '3xl': 'var(--step-4)',
      '4xl': 'var(--step-5)',
    },
    backgroundImage: {
      'footerimg': 'url(/themes/midnight_theme/images/footerbg.webp) no-repeat center center / cover',
      'wavesbg': 'url(/themes/midnight_theme/images/wavesbg.webp) no-repeat center center / contain',
    },
    breakpoints: {
      'sm': '640px',
      'md': '768px',
      'lg': '1024px',
      'desk': '1200px',
      'xl': '1280px',
      '2xl': '1536px',
    },
  },
  variants: [
    // Layout Builder
    (matcher) => {
      if (!matcher.startsWith('layoutbuilder:')) {
        return matcher;
      }
      // slice `layoutbuilder:` prefix and passed to the next variants and rules
      return {
        matcher: matcher.slice(14),
        selector: s => `.layout-builder ${s}`,
      };
    },
    // desk - when menu collapses
    /*(matcher) => {
      if (!matcher.startsWith('desk:')) {
        return matcher;
      }
      return {
        matcher: matcher.slice(5),
        selector: s => `body:not(.is-always-mobile-nav) ${s}`,
      };
    },*/
    // feature flag - fcontrol
    (matcher) => {
      if (!matcher.startsWith('fcontrols:')) {
        return matcher;
      }
      return {
        matcher: matcher.slice(10),
        selector: s => `html.feature-controls ${s}`,
      };
    },
  ],

  content: {
    filesystem: [
      'templates/**/*.twig',
      'components/**/*.twig',
      'layouts/**/*.twig',
    ],
  },
  transformers: [
    transformerDirectives(),
    transformerVariantGroup(),
  ],
});
