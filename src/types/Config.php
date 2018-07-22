<?php
namespace Wn\Mate;

/**
 * @mate
 * @type Config
 * @field string $srcDir
 * @field string $docsDir
 * @field string $testsDir
 * @field string $cachePath
 *
 */
class Config {
  public $srcDir;
  public $docsDir;
  public $testsDir;
  public $testCaseClass;
  public $cachePath;

  public static function defaults() {
    return [
      'srcDir' => "src",
      'testsDir' => "tests",
      'docsDir' => "docs",
      'testCaseClass' => "\\Wn\\Mate\\Classes\\TestCase",
      'cachePath' => "mate.lock",
    ];
  }

  public static function of(array $data): Config {
    $config = new Config;
    $data = (object) array_merge(Config::defaults(), $data);
    $config->srcDir = realpath($data->srcDir ?: '');
    $config->docsDir = realpath($data->docsDir ?: '');
    $config->testsDir = realpath($data->testsDir ?: '');
    $config->cachePath = $data->cachePath ?: '';
    $config->testCaseClass = $data->testCaseClass ?: '';
    return $config;
  }
}
