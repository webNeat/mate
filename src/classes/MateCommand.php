<?php
namespace Wn\Mate\Classes;

use Tarsana\Command\Command;
use Tarsana\IO\Interfaces\Filesystem\Directory;
use Wn\Mate as M;

class MateCommand extends Command {

  protected $firstRun;
  protected $cache;
  protected $paths;

  protected function init() {
    $this
      ->name('Mate')
      ->version('1.0.0-alpha')
      ->description('Your functional programming helper.')
      ->syntax('configPath: (string:mate.json)')
      ->options(['--dont-run-tests', '--watch', '--no-cache'])
      ->describe('configPath', 'Path to the config file.')
      ->describe('--dont-run-tests', "Don't run phpunit after the build.")
      ->describe('--watch', "Watch source files for changes.")
      ->describe('--no-cache', "Don't use cache. Please don't combine this option with --watch.")
      ->configPaths([__DIR__ . '/../../mate.json', 'mate.json']);
  }

  protected function initConfig() {
    $data = $this->config();
    $this->fs->dir($data['testsDir'], true);
    $this->fs->dir($data['docsDir'], true);
    $this->config = M\Config::of($data);
    return $this;
  }

  protected function loadCache() {
    $cachePath = $this->config->cachePath;
    $this->cache = $this->fs->isFile($cachePath)
      ? json_decode($this->fs->file($cachePath)->content(), true)
      : ['files' => [], 'modules' => []];
    return $this;
  }

  protected function saveCache() {
    $cacheFile = $this->fs->file($this->config->cachePath, true);
    $cacheFile->content(json_encode($this->cache));
    return $this;
  }

  protected function loadPaths() {
    $srcDir = $this->fs->dir($this->config->srcDir);
    $this->paths = [];
    $this->addPathsFrom($srcDir);
    return $this;
  }

  protected function addPathsFrom(Directory $dir) {
    $this->paths = array_merge(
      $this->paths,
      $dir->fs()->find('*.php')->paths()
    );

    $subDirs = $dir->fs()->find('*')->dirs()->asArray();
    foreach ($subDirs as $subDir)
      $this->addPathsFrom($subDir);
  }

  protected function changedFiles() {
    $changedFiles = [];
    foreach ($this->paths as $path) {
      $content = $this->fs->file($path)->content();
      if ($this->firstRun || $this->option('--no-cache')) {
        $changedFiles[] = M\File::of($path, $content);
        continue;
      }
      $hash = hash('adler32', $content);
      if (empty($this->cache['files'][$path]) || $hash != $this->cache['files'][$path]) {
        $this->cache['files'][$path] = $hash;
        $changedFiles[] = M\File::of($path, $content);
      }
    }
    return $changedFiles;
  }

  protected function execute() {
    try {
      $this
        ->initConfig()
        ->loadCache()
        ->loadPaths();
      $this->firstRun = true;
      if ($this->option('--watch'))
        while (true) {
          $this->generate();
          sleep(1);
        }
      $this->generate();
    } catch(\Exception $e) {
      $this->console->line($e->getMessage());
      $this->console->line($e->getTraceAsString());
    }
  }

  protected function generate() {
    $types = [];
    $runTests = false;
    foreach ($this->changedFiles() as $file) {
      $runTests = true;
      $module = M\make_module($file);
      if ($module) {
        $types = array_merge($types, $module->types);
        if ($this->option('--no-cache'))
          $this->build($module);
        else {
          $hash = $this->hashModule($module);
          $missing = empty($this->cache['modules'][$module->path]);
          if ($missing || $this->cache['modules'][$module->path] != $hash) {
            $this->cache['modules'][$module->path] = $hash;
            $this->build($module);
          }
        }
      }
    }
    $this->writeTypes($types);
    if (! $this->option('--no-cache'))
      $this->saveCache();
    if (! $this->option('--dont-run-tests') && $runTests)
      $this->runTests();
  }

  protected function runTests() {
    $this->console
      ->line('Executing Tests ...')
      ->line(shell_exec('./vendor/bin/phpunit') ?: 'Errored!');
  }

  protected function hashModule(M\Module $module) {
    return hash('adler32', serialize($module));
  }

  protected function build(M\Module $module) {
    if (!empty($module->functions)) {
      $test = M\make_test($this->config, $module);
      $this->fs->file($test->path, true)->content(M\render_test($test));
    }

    if (!empty($module->functions)) {
      $doc = M\make_doc($this->config, $module);
      $this->fs->file($doc->path, true)->content(M\render_doc($doc));
    }
  }

  protected function writeTypes(array $types) {
    if (empty($types)) return;
    $path = $this->config->srcDir . '/_types.php';
    $module = M\Module::of($path, '', '', [], $types, []);
    $hash = $this->hashModule($module);
    if (empty($this->cache['modules'][$path]) || $this->cache['modules'][$path] != $hash) {
      $this->cache['modules'][$path] = $hash;
      $doc = M\make_doc($this->config, $module, true);
      $this->fs->file($doc->path, true)->content(M\render_doc($doc));
    }
  }

}
