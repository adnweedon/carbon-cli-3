<?php

namespace Carbon\Tests\Command;

use Carbon\Cli;
use Carbon\Tests\DummyMixin;
use Carbon\Tests\TestCase;

/**
 * @coversDefaultClass \Carbon\Command\Macro
 */
class MacroTest extends TestCase
{
    /**
     * @covers ::run
     * @covers \Carbon\Types\Generator::getMethods
     * @covers \Carbon\Types\Generator::getMethodsDefinitions
     * @covers \Carbon\Types\Generator::dumpParameter
     * @covers \Carbon\Types\Generator::dumpValue
     * @covers \Carbon\Types\Generator::writeHelpers
     */
    public function testRun()
    {
        $dir = sys_get_temp_dir().'/macro-test-'.mt_rand(0, 999999);
        @mkdir($dir);
        chdir($dir);
        $cli = new Cli();
        $cli->mute();
        $cli('carbon', 'macro', DummyMixin::class, '--source-path', __DIR__.'/..');

        $this->assertSame([
            '.',
            '..',
            'types',
        ], scandir($dir));
        $this->assertSame([
            '.',
            '..',
            '_ide_carbon_mixin_instantiated.php',
            '_ide_carbon_mixin_static.php',
        ], scandir("$dir/types"));
        $this->assertFileEquals(__DIR__.'/_ide_carbon_mixin_instantiated.php', "$dir/types/_ide_carbon_mixin_instantiated.php");
        $this->assertFileEquals(__DIR__.'/_ide_carbon_mixin_static.php', "$dir/types/_ide_carbon_mixin_static.php");

        $this->removeDirectory($dir);
    }

    /**
     * @covers ::run
     * @covers \Carbon\Types\Generator::getMethods
     * @covers \Carbon\Types\Generator::getMethodsDefinitions
     * @covers \Carbon\Types\Generator::dumpParameter
     * @covers \Carbon\Types\Generator::dumpValue
     * @covers \Carbon\Types\Generator::writeHelpers
     */
    public function testRunWithFile()
    {
        $dir = sys_get_temp_dir().'/macro-test-'.mt_rand(0, 999999);
        @mkdir($dir);
        chdir($dir);
        file_put_contents('test.php', '<?php \Carbon\Carbon::macro(\'foo\', function () { return 42; });');
        $cli = new Cli();
        $cli->mute();
        $cli('carbon', 'macro', 'test.php');

        $contents = file_get_contents("$dir/types/_ide_carbon_mixin_instantiated.php");
        $this->assertStringContainsString('public function foo()', $contents);

        $this->removeDirectory($dir);
    }
}