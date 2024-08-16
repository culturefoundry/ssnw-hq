# ssnw Theme

## Important

To install dependencies, use --legacy-peer-deps.

## Status: Dev

## Production build:

1. npm install
2. npm run build
3. drush cr

## CSS overview

This uses Freelock's current theme standards, using Vite to build CSS using UnoCSS.

We define several layers, as follows:

- Base: Overall color scheme, typography.
- Components: Individual elements or groups of elements on a page
- Variants: Modifiers to change existing components
- Utilities: General purpose styles that may be applied in the User Interface.

Each layer gets its styles from different places.

### Base styles

Base colors and fonts should be defined in the unocss.config.js and the src/main.css files.

For colors, we define several layers of colors, too:

- Color hex value.
- Semantic colors (base (text color), pagebg, highlight1, highlight2, accent1, accent2, primary, etc). These may be overridden for different themes (e.g. Dark theme, high-contrast).
- Functional colors (text, link, header) -- each section can assign a different semantic color to a functional color to make it easy to switch themes

With these set up properly, and with color variants added to the uno config, you can use semantic colors in Tailwind-style classes on a component (e.g. "text-highlight2 bg-accent1").

### Component styles

Components come from a huge range of sources. The CSS should live as close to the component as possible.

If a component uses a template, we generally use Tailwind-style classes in the component template's markup. UnoCSS provides custom  "variants" that can be extremely useful, which can be specified here.

Generally when styling a component, here is our preference for where to put the CSS:

- SDC components, Vue components -- put in the component directory, either as TW classes on HTML or in the attached CSS
- Page elements -- use TW classes in twig templates
- src/main.css -- if we want to be able to target specific HTML with component styling, go ahead and add here
- css/components/* -- these are components inherited from the base theme, modify/use/delete as appropriate

### Variant styles

"Variant" has several different meanings:

- SDC components -- variants can be defined explicitly as options that may be selected when the SDC component is used
- UnoCSS defines "custom variants" which can be automatically converted to specific style modifiers
- Generally a variant class is one that modifies the style of some sort of base component or element.

Some examples:

- "desk" Uno variant - custom variant that limits the next style to only be active when the mobile menu is active, generally combined with lg. This is used in our header with the olivero menu.
- "layoutbuilder" Uno variant -- allows changing the color in a layout when it is shown in layout builder
- bleed-container -- class that makes an element go full width through margin/padding, for background strips
- white-text -- inverts the text colors to show white text on dark backgrounds
- red-header -- makes header font colors use an accent color within a component

UnoCSS variants are defined in unocss.config.js. Component variants are defined in the component yaml. Most other variants should be in src/main.css.

### Utility styles

Utility styles are classes that may be used in a variety of places, and are often included in the ckeditor5.css stylesheet so that authors can select them as needed.

Note that we maintain a separate ckeditor5.css stylesheet in addition to utility classes

We do not load the ckeditor5.css file on most pages -- we find it useful to keep the editor implementation free of colors (mostly) so define these items twice -- once for the editor, and then add the final style to the src/main.css.

## Build for development

This project is set up to use Vite's hot module reload functionality with local development.

At the moment, this does not work on Freelock dev servers -- the PHP Docker container cannot see the host's ports to know whether the vite server is running.

This is currently configured to work inside ddev.

To actively develop with this project:

1. ddev start (the vite port is configured in .ddev/config.yml)
2. Create a web/sites/default/settings.local.php file with the line:
```
$settings['vite']['devServerUrl'] = 'https://ssnw.ddev.site:5243';
```
3. Run the npm build inside the ddev web container:
```
ddev ssh
cd web/themes/midnight_theme
npm run dev
```
4. Clear the Drupal cache in another terminal (or in the website):
```
ddev drush cr
```
When you reload the page, you should see `[vite] connected.` in the console.log.

If so, congrats! You're using a live build. Any changes you make in main.css should appear immediately. Any changes you make in a twig template should make your browser auto reload.

To avoid having to repeatedly cache rebuild, add these lines to your settings.local.php:

```
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['rebuild_access'] = TRUE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
```

## Generator
midnight_theme theme, generated from starterkit_theme. Additional information on generating themes can be found in the [Starterkit documentation](https://www.drupal.org/docs/core-modules-and-themes/core-themes/starterkit-theme).
