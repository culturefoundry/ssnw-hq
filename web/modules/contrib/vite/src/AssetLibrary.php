<?php

declare(strict_types=1);

namespace Drupal\vite;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\vite\Exception\ManifestCouldNotBeLoadedException;
use Drupal\vite\Exception\ManifestNotFoundException;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Drupal library vite config.
 */
class AssetLibrary {

  use StringTranslationTrait;

  const DEFAULT_VITE_DEV_SERVER_URL = 'http://localhost:5173';

  const DEFAULT_MANIFEST_LOOKUP_PATHS = [
    'dist/manifest.json',
    'dist/.vite/manifest.json',
  ];

  /**
   * Constructs AssetLibrary object.
   */
  public function __construct(
    protected string $libraryId,
    protected array $library,
    protected string $extension,
    protected MessengerInterface $messenger,
    protected LoggerInterface $logger,
    protected ThemeExtensionList $themes,
    protected ModuleExtensionList $modules,
    protected ClientInterface $httpClient,
    TranslationInterface $stringTranslation,
    protected string $appRoot,
    protected bool $isSdc = FALSE,
  ) {
    $this->stringTranslation = $stringTranslation;
  }

  /**
   * Returns drupal asset library definition.
   */
  public function getDefinition(): array {
    return $this->library;
  }

  /**
   * Returns vite manifest.
   */
  public function getViteManifest(): ?Manifest {

    // Skip loading manifest for internal dev mode only libraries.
    if ($this->extension === 'vite') {
      return NULL;
    }

    $manifestPath = $this->getManifestPath();
    $baseUrl = $this->getBaseUrl();

    try {
      return new Manifest($manifestPath, $baseUrl);
    }
    catch (ManifestNotFoundException | ManifestCouldNotBeLoadedException $e) {
      $args = [
        '@extension' => $this->extension,
        '@library' => $this->libraryId,
        '@path_to_manifest' => $this->getManifestRelativePath(),
      ];
      $this->messenger->addError($this->t("Could not load vite manifest for the library `@extension/@library` (@path_to_manifest). Perhaps you forgot to build frontend assets, try running `vite build` in the `@extension` theme/module. Also ensure that vite is configured to output manifest to `dist/manifest.json` in the theme/module main directory or that a different path is correctly set in `@extension.libraries.yml` in `@library.vite.manifest`.", $args));
      $this->logger->error("Could not load vite manifest for the library `@extension/@library` (@path_to_manifest). Perhaps you forgot to build frontend assets, try running `vite build` in the `@extension` theme/module. Also ensure that vite is configured to output manifest to `dist/manifest.json` in the theme/module main directory or that a different path is correctly set in `@extension.libraries.yml` in `@library.vite.manifest`.", $args);
    }
    return NULL;
  }

  /**
   * Returns base url used in rewriting library for dist.
   */
  private function getBaseUrl(): string {
    $baseUrl = $this->getViteSetting('baseUrl');
    if (empty($baseUrl) || !is_string($baseUrl)) {
      $baseUrl = $this->getViteSettingFromLibraryConfig('baseUrl');
    }
    if (empty($baseUrl) || !is_string($baseUrl)) {
      $baseUrl = '/' . $this->getExtensionBasePath() . '/dist/';
    }
    return $baseUrl;
  }

  /**
   * Returns absolute vite manifest path.
   */
  private function getManifestPath(): string {
    $manifestPath = $this->getManifestRelativePath();
    $basePath = $this->getExtensionBasePath();
    return $this->appRoot . '/' . $basePath . '/' . $manifestPath;
  }

  /**
   * Returns relative vite manifest path.
   */
  private function getManifestRelativePath(): string {
    // Check if manifest path is overridden in settings.
    $manifestPath = $this->getViteSetting('manifest');
    if (is_string($manifestPath)) {
      return $manifestPath;
    }

    // Check if manifest path is explicitly provided in library definition.
    $manifestPath = $this->getViteSettingFromLibraryConfig('manifest');
    if (is_string($manifestPath)) {
      return $manifestPath;
    }

    // Check if manifest is present in one of the default locations.
    foreach (self::DEFAULT_MANIFEST_LOOKUP_PATHS as $path) {
      if (file_exists($this->appRoot . '/' . $this->getExtensionBasePath() . '/' . $path)) {
        return $path;
      }
    }

    // Return last default manifest lookup path.
    return self::DEFAULT_MANIFEST_LOOKUP_PATHS[array_key_last(self::DEFAULT_MANIFEST_LOOKUP_PATHS)];
  }

  /**
   * Returns library extension (module/theme) base path.
   */
  private function getExtensionBasePath(): string {
    if ($this->themes->exists($this->extension)) {
      return $this->themes->getPath($this->extension);
    }
    elseif ($this->modules->exists($this->extension)) {
      return $this->modules->getPath($this->extension);
    }
    throw new \Exception('Could not find library extension (module/theme) base path.');
  }

  /**
   * Checks if library should be managed by vite.
   */
  public function shouldBeManagedByVite(): bool {
    $enabledInSettings = $this->getViteSetting('enabled');
    if (!is_bool($enabledInSettings)) {
      $enabledInSettings = FALSE;
    }
    $enabledInLibraryDefinition = isset($this->library['vite'])
      && $this->library['vite'] !== FALSE
      && (!isset($this->library['vite']['enabled']) || $this->library['vite']['enabled'] === TRUE);
    return $enabledInSettings || $enabledInLibraryDefinition;
  }

  /**
   * Determines if vite dev server or dist build should serve library assets.
   */
  public function shouldUseDevServer(): bool {
    $useDevServer = $this->getViteSetting('useDevServer');
    if ($useDevServer === NULL || $useDevServer === 'auto') {
      try {
        $acceptableStatuses = [
          'vite_4' => 404,
          'vite_5' => 200,
        ];
        $statusCode = $this->httpClient->request('GET', $this->getDevServerBaseUrl(), ['http_errors' => FALSE])->getStatusCode();
        return in_array($statusCode, $acceptableStatuses, TRUE);
      }
      catch (\Exception $e) {
        return FALSE;
      }
    }
    if (is_bool($useDevServer)) {
      return $useDevServer;
    }
    return FALSE;
  }

  /**
   * Returns base url of vite dev server for the library.
   */
  public function getDevServerBaseUrl(): string {
    $baseUrl = $this->getViteSetting('devServerUrl');
    if (!is_string($baseUrl) || !UrlHelper::isValid($baseUrl)) {
      $baseUrl = $this->getViteSettingFromLibraryConfig('devServerUrl');
    }
    if (!is_string($baseUrl) || !UrlHelper::isValid($baseUrl)) {
      $baseUrl = self::DEFAULT_VITE_DEV_SERVER_URL;
    }
    return $baseUrl;
  }

  /**
   * Returns library extension (theme/module) id.
   */
  public function getExtension(): string {
    return $this->extension;
  }

  /**
   * Returns vite setting for the library or NULL.
   */
  private function getViteSetting(string $setting): mixed {
    $settings = Settings::get('vite', []);
    if (!is_array($settings)) {
      return NULL;
    }

    $value = NULL;

    // Global settings.
    if (isset($settings[$setting])) {
      $value = $settings[$setting];
    }

    // Extension specific settings.
    if (isset($settings['overrides'][$this->extension][$setting])) {
      $value = $settings['overrides'][$this->extension][$setting];
    }

    // Library specific settings.
    if (isset($settings['overrides'][$this->extension . '/' . $this->libraryId][$setting])) {
      $value = $settings['overrides'][$this->extension . '/' . $this->libraryId][$setting];
    }

    return $value;
  }

  /**
   * Returns vite library config for the library or NULL.
   */
  private function getViteSettingFromLibraryConfig(string $setting): mixed {
    if (!isset($this->library['vite']) || !is_array($this->library['vite'])) {
      return NULL;
    }
    $value = NULL;
    if (!empty($this->library['vite'][$setting])) {
      $value = $this->library['vite'][$setting];
    }
    return $value;
  }

  /**
   * Checks if library is SDC component library.
   */
  public function isSdc(): bool {
    return $this->isSdc;
  }

}
