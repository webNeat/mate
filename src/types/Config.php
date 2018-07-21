<?php
namespace Wn\Mate;

/**
 * @mate
 * @type Config
 * @field string $srcDir
 * @field string $docsDir
 * @field string $testsDir
 *
 */
class Config {
  public $srcDir;
  public $docsDir;
  public $testsDir;
  public $testCaseClass;

  public static function defaults(): array {
    return [
      'srcDir' => realpath('src'),
      'docsDir' => realpath('docs'),
      'testsDir' => realpath('tests'),
      'testCaseClass' => '\Wn\Mate\Classes\TestCase',
    ];
  }

  public static function of(array $data): Config {
    $config = new Config;
    $data = (object) array_merge(Config::defaults(), $data);
    $config->srcDir = realpath($data->srcDir);
    $config->docsDir = realpath($data->docsDir);
    $config->testsDir = realpath($data->testsDir);
    $config->testCaseClass = $data->testCaseClass;
    return $config;
  }
}
