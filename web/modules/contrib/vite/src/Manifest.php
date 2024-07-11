<?php

declare(strict_types=1);

namespace Drupal\vite;

use Drupal\vite\Exception\ManifestCouldNotBeLoadedException;
use Drupal\vite\Exception\ManifestNotFoundException;

/**
 * Object representing vite manifest.
 */
class Manifest {

  /**
   * Vite manifest.
   */
  private array $manifest;

  /**
   * Constructs vite manifest object.
   *
   * @throws \InvalidArgumentException
   * @throws \Drupal\vite\Exception\ManifestNotFoundException
   * @throws \Drupal\vite\Exception\ManifestCouldNotBeLoadedException
   */
  public function __construct(
    string $manifestPath,
    protected string $baseUri,
  ) {
    $realManifestPath = realpath($manifestPath);
    if ($realManifestPath === FALSE) {
      throw new ManifestNotFoundException("Manifest file was not found under path: $manifestPath");
    }

    $manifestContent = file_get_contents($realManifestPath);
    if ($manifestContent === FALSE) {
      throw new ManifestCouldNotBeLoadedException("Failed loading manifest: $manifestPath");
    }

    $manifest = json_decode($manifestContent, TRUE);
    if ($manifest === NULL || !is_array($manifest)) {
      throw new ManifestCouldNotBeLoadedException("Failed loading manifest: $manifestPath");
    }

    $this->manifest = $manifest;

    if (!is_array(parse_url($baseUri))) {
      throw new \InvalidArgumentException("Failed to parse base uri: $baseUri");
    }
  }

  /**
   * Returns resolved path of given chunk.
   */
  public function getChunk(string $chunk, bool $prependBaseUri = TRUE): ?string {
    if (!$this->chunkExists($chunk)) {
      return NULL;
    }
    return $this->getPath($this->manifest[$chunk]['file'], $prependBaseUri);
  }

  /**
   * Returns imports paths of given chunk.
   */
  public function getImports(string $chunk, bool $prependBaseUri = TRUE): array {
    return $this->getChunkPropertyPaths('imports', $chunk, $prependBaseUri);
  }

  /**
   * Returns styles paths of given chunk.
   */
  public function getStyles(string $chunk, bool $prependBaseUri = TRUE): array {
    return $this->getChunkPropertyPaths('css', $chunk, $prependBaseUri);
  }

  /**
   * Returns assets paths of given chunk.
   */
  public function getAssets(string $chunk, bool $prependBaseUri = TRUE): array {
    return $this->getChunkPropertyPaths('assets', $chunk, $prependBaseUri);
  }

  /**
   * Checks if chunk exists in the manifest.
   */
  private function chunkExists(string $chunk): bool {
    return isset($this->manifest[$chunk]);
  }

  /**
   * Resolves asset path.
   */
  private function getPath(string $assetPath, bool $prependBaseUri = TRUE): string {
    return ($prependBaseUri ? $this->baseUri : '') . $assetPath;
  }

  /**
   * Returns resolved paths of given chunk's property.
   */
  private function getChunkPropertyPaths(string $property, string $chunk, bool $prependBaseUri = TRUE): array {
    if (
      !$this->chunkExists($chunk)
      || empty($this->manifest[$chunk][$property])
      || !is_array($this->manifest[$chunk][$property])
    ) {
      return [];
    }

    return array_filter(array_map(
      fn($import) => $this->getChunk($import, $prependBaseUri),
      $this->manifest[$chunk][$property],
    ));
  }

}
