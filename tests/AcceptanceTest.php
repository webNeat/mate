<?php
use Tarsana\Tester\CommandTestCase;
use Wn\Mate\Classes\MateCommand;

class MateTest extends CommandTestCase {
  public function test_it_generates_test_and_doc() {
    $this->assertFalse($this->fs->isFile('tests/SampleTest.php'));
    $this->assertFalse($this->fs->isFile('docs/sample.md'));

    $this->havingFile('src/sample.php', file_get_contents(__DIR__.'/examples/sample.php'));
    $this->command(new MateCommand, ['--dont-run-tests']);

    $this->assertTrue($this->fs->isFile('tests/SampleTest.php'));
    $this->assertTrue($this->fs->isFile('docs/sample.md'));

    $this->assertEquals(
      file_get_contents(__DIR__.'/examples/sample_test.php'),
      $this->fs->file('tests/SampleTest.php')->content()
    );

    $this->assertEquals(
      file_get_contents(__DIR__.'/examples/sample.md'),
      $this->fs->file('docs/sample.md')->content()
    );
  }

  public function test_it_ignores_files_without_mate_tag() {

    $this->havingFile('src/sample.php', file_get_contents(__DIR__.'/examples/sample-without-mate.php'));
    $this->command(new MateCommand, ['--dont-run-tests']);
    $this->assertFalse($this->fs->isFile('tests/SampleTest.php'));
    $this->assertFalse($this->fs->isFile('docs/sample.md'));
  }
}
