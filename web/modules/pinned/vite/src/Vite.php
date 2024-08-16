<?php

declare(strict_types=1);

namespace Drupal\vite;

use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Rewrites libraries to work with vite.
 */
class Vite {

  /**
   * Logger.
   */
  private LoggerInterface $logger;

  /**
   * Constructs the Vite service object.
   */
  public function __construct(
    protected MessengerInterface $messenger,
    LoggerChannelFactoryInterface $logger_factory,
    protected ThemeExtensionList $themes,
    protected ModuleExtensionList $modules,
    protected ClientInterface $httpClient,
    protected TranslationInterface $stringTranslation,
    protected string $appRoot,
  ) {
    $this->logger = $logger_factory->get('vite');
  }

  /**
   * Process libraries declared to use vite.
   */
  public function processLibraries(array &$libraries, string $extension): void {
    foreach ($libraries as $libraryId => $library) {
      $libraryExtension = $extension;
      $isSdc = FALSE;
      $prefix = 'components.';
      $componentId = '';

      if (str_starts_with($libraryId, $prefix)) {
        if (substr($libraryId, 0, strlen($prefix)) == $prefix) {
          $componentId = substr($libraryId, strlen($prefix));
          $libraryExtension = explode('--', $componentId)[0];
          $isSdc = TRUE;
        }
        else {
          continue;
        }
      }

      $assetLibrary = new AssetLibrary(
        $componentId ?: $libraryId,
        $library,
        $libraryExtension,
        $this->messenger,
        $this->logger,
        $this->themes,
        $this->modules,
        $this->httpClient,
        $this->stringTranslation,
        $this->appRoot,
        $isSdc,
      );
      if (!$assetLibrary->shouldBeManagedByVite()) {
        continue;
      }
      $libraries[$libraryId] = $this->rewriteLibrary($assetLibrary);
    }
  }

  /**
   * Rewrite library for dev or dist.
   */
  private function rewriteLibrary(AssetLibrary $assetLibrary): array {
    if ($assetLibrary->shouldUseDevServer()) {
      return $this->rewriteLibraryForDev($assetLibrary);
    }
    return $this->rewriteLibraryForDist($assetLibrary);
  }

  /**
   * Rewrite library using dist output.
   */
  private function rewriteLibraryForDist(AssetLibrary $assetLibrary): array {
    $manifest = $assetLibrary->getViteManifest();
    $library = $assetLibrary->getDefinition();

    if ($manifest === NULL) {
      return $library;
    }

    if (isset($library['css'])) {
      foreach ($library['css'] as $type => $paths) {
        foreach ($paths as $path => $options) {
          $originalPath = $path;
          if (
            $assetLibrary->isSdc()
            && $this->shouldAssetBeManagedByVite($path, $options)
          ) {
            $path = strstr($path, 'components/');
          }
          $newPath = $manifest->getChunk($path);
          if ($newPath === NULL) {
            // Don't rewrite assets not present in the manifest.
            continue;
          }
          unset($library['css'][$type][$originalPath]);
          $library['css'][$type][$newPath] = $options;

        }
      }
    }

    if (isset($library['js'])) {
      foreach ($library['js'] as $path => $options) {
        $originalPath = $path;
        if (
          $assetLibrary->isSdc()
          && $this->shouldAssetBeManagedByVite($path, $options)
        ) {
          $path = strstr($path, 'components/');
        }
        $newPath = $manifest->getChunk($path);
        if ($newPath === NULL) {
          // Don't rewrite assets not present in the manifest.
          continue;
        }
        unset($library['js'][$originalPath]);

        $attributes = $options['attributes'] ?? [];
        $attributes['type'] = 'module';
        $options['attributes'] = $attributes;
        $library['js'][$newPath] = $options;

        $imports = $manifest->getImports($path);
        foreach ($imports as $importPath) {
          $library['js'][$importPath] = ['attributes' => ['rel' => 'modulepreload', 'type' => 'module']];
        }

        $styles = $manifest->getStyles($path);
        foreach ($styles as $stylePath) {
          $library['css']['component'][$stylePath] = [];
        }
      }
    }
    return $library;
  }

  /**
   * Rewrite library to use vite dev server.
   */
  private function rewriteLibraryForDev(AssetLibrary $assetLibrary): array {
    $library = $assetLibrary->getDefinition();

    if ($assetLibrary->getExtension() !== 'vite') {
      $library['dependencies'][] = 'vite/vite-dev-client';
    }

    $devServerBaseUrl = $assetLibrary->getDevServerBaseUrl();
    if (isset($library['css'])) {
      foreach ($library['css'] as $type => $paths) {
        foreach ($paths as $path => $options) {
          if (!$this->shouldAssetBeManagedByVite($path, $options)) {
            continue;
          }
          $originalPath = $path;
          if ($assetLibrary->isSdc()) {
            $path = strstr($path, 'components/');
          }
          unset($library['css'][$type][$originalPath]);
          $options['type'] = 'external';
          $attributes = $options['attributes'] ?? [];
          $attributes['type'] = 'module';
          $options['attributes'] = $attributes;
          $newPath = $devServerBaseUrl . '/' . $path;
          $library['js'][$newPath] = $options;

        }
      }
    }

    if (isset($library['js'])) {
      foreach ($library['js'] as $path => $options) {
        if (!$this->shouldAssetBeManagedByVite($path, $options)) {
          continue;
        }
        $originalPath = $path;
        if ($assetLibrary->isSdc()) {
          $path = strstr($path, 'components/');
        }
        unset($library['js'][$originalPath]);
        $options['type'] = 'external';
        $attributes = $options['attributes'] ?? [];
        $attributes['type'] = 'module';
        $options['attributes'] = $attributes;
        $newPath = $devServerBaseUrl . '/' . $path;
        $library['js'][$newPath] = $options;
      }
    }
    return $library;
  }

  /**
   * Tries to determine if asset should be managed by vite.
   */
  private function shouldAssetBeManagedByVite(string $path, array $options): bool {
    return $path[0] !== DIRECTORY_SEPARATOR
      && strpos($path, 'http') !== 0
      && (!isset($options['type']) || $options['type'] !== 'external')
      && (!isset($options['vite']) || $options['vite'] !== FALSE)
      && (!isset($options['vite']['enabled']) || $options['vite']['enabled'] !== FALSE);
  }

}
