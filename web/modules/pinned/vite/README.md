# Vite

Vite [backend integration](https://vitejs.dev/guide/backend-integration) for Drupal asset libraries.

* To submit bug reports and feature suggestions, or track changes:
   <https://www.drupal.org/project/issues/vite>

## Requirements

This module requires no modules outside of Drupal core.

It's designed to work with [Vite](https://vitejs.dev) 3 or newer.

## Installation

* Install as you would normally install a contributed Drupal module. Visit
   <https://www.drupal.org/node/1897420> for further information.

## Usage

* Install the module.
* Configure vite to:
  * generate `manifest.json`
     <https://vitejs.dev/config/build-options.html#build-manifest>
  * use right right assets base path for production
     <https://vitejs.dev/config/shared-options.html#base>,
     usually it will be something like `/themes/custom/my_theme/dist/`
     or `/modules/custom/my_module/dist/`
  * depending on your setup you may also need to configure dev server host
     and/or port <https://vitejs.dev/config/server-options.html#server-options>

```diff
 import { defineConfig } from "vite"

 export default defineConfig({
   build: {
+    manifest: true,
     rollupOptions: {
       input: [
         [...]
       ],
     },
   },
   [...]
 })
```

* Enable vite support for asset libraries you would like to use it with:
  * To enable for all libraries/components of the theme/module, in its
    `.info.yml` file, add `vite:` section with one or both of:
    * `enableInAllLibraries: true` to enable vite support for all libraries,
    * `enableInAllComponents: true` to enable vite support for all components.
  * To enable for single library, in the `<theme|module>.libraries.yml`, for
    the library you would like to use assets build by vite, add property
    `vite: true`.
* Replace paths to assets in library definition with paths to assets sources:

```diff
 # theme.info.yml
 name: My theme
 type: theme
 [...]
+ vite:
+   enableInAllLibraries: true
```

```diff
 # theme.libaries.yml
 global-styling:
   js:
-    dist/script.js: {}
+    src/script.ts: {}
   css:
     component:
-      dist/style.css: {}
+      src/scss/style.scss: {}
   dependencies:
     - core/drupalSettings
```

* The module will dynamically rewrite assets paths to dist and include
   their dependencies defined in manifest.json.

* To use hot module reload during development, just start vite dev server.
   If the server is accessible on the localhost under default port (5173)
   the module will automatically start using it instead of dist assets
   from manifest.json as soon as you clear the cache (library definitions
   are cached by default).

### SDC integration

To use Vite for processing [SDC](https://www.drupal.org/docs/develop/theming-drupal/using-single-directory-components)
asset libraries:

* add source assets in the components folders to entrypoints in the vite config of the theme/module components are in
* override component library to set `vite: true` and include paths to source assets

For example:

* in `<theme>/vite.config.ts`:

```diff
 import { defineConfig } from "vite"
 import multiInput from "rollup-plugin-multi-input"

 export default defineConfig({
   plugins: [multiInput.default()],
   build: {
     manifest: true,
     rollupOptions: {
       input: [
         [...]
+        "components/**/*.pcss.css",
+        "components/**/*.ts",
       ],
     },
   },
   [...]
 })
```

* in `<theme>/components/button/button.component.yml`:

```diff
name: Button
+libraryOverrides:
+  vite: true
+  css:
+    component:
+      button.pcss.css: {}
+  js:
+    button.ts: {}
props:
  [...]
```

## Configuration

In library definition instead of only enabling vite support by setting
`vite: true` theres also an option to provide some custom configurations.

```yaml
vite:
  # By default true, if not set explicitly to false assumed true.
  enabled: true
  # Path to manifest.json, by default `dist/manifest.json`.
  manifest: vue_app/dist/manifest.json
  # By default `<path_to_module|theme>/dist/`.
  baseUrl: '/themes/custom/my_theme/dist/'
  # Vite dev server url, by default http://localhost:5173.
  devServerUrl: 'http://localhost:9999`

```

These settings can also be overwritten in site settings.php:

```php
$settings['vite'] = [
  // By default ('auto') the module will automatically check if vite dev server
  // is running and if so, use it. Settings this to false will make sure that
  // vite dev server will not be used, which is recommended setting for
  // production environments.
  'useDevServer' => 'auto',
  // Global overrides.
  /* Make note that these are global so they will take effect for all drupal
   * asset libraries, so setting enabled => TRUE here is not really recommended.
   * Probably the only useful to set here would be devServerUrl to globally
   * override the default one.
   */
  'enabled' => TRUE,
  'manifest' => 'vue_app/dist/manifest.json',
  'baseUrl' => '/some/custom/base/url/used/in/production/for/dist/assets/',
  'devServerUrl' => 'http://localhost:9999',
  'overrides' => [
    // Per module/theme overrides.
    '<module|theme>' => [
      // ... settings like the global ones
    ]
    // Per library overrides.>
    '<module|theme>/<library>' => [
      // ... settings like the global ones
    ]
  ],

]

```

In `<theme/module>.libraries.yml` there is also an option to disable rewriting
of specific asset, to do that you need to set `vite: false` for specific asset,
for example:

```diff
 global-styling:
   vite: true
   js:
     src/script.ts: {}
-    some/static/script.js: {}
+    some/static/script.js: {vite: false}
   css:
     component:
       src/scss/style.scss: {}
     dependencies:
       - core/drupalSettings
```
