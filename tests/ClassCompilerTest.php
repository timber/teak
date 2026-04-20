<?php

use PHPUnit\Framework\TestCase;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Teak\Console\ClassReferenceGenerator;
use Teak\Console\ClassReferenceHandler;
use Teak\Console\FunctionReferenceGenerator;
use Teak\Console\HookReferenceGenerator;

/**
 * Class ClassCompilerTest
 *
 * @link https://symfony.com/doc/current/console.html#testing-commands
 */
class ClassCompilerTest extends TestCase
{
    /**
     * @var Application
     */
    public $application;

    public function setUp(): void
    {
        parent::setUp();

        $this->application = new Application('Teak Documentation Test');
        $this->application->setAutoExit(false);

        $this->application->add(new ClassReferenceGenerator());
        $this->application->add(new FunctionReferenceGenerator());
        $this->application->add(new HookReferenceGenerator());
    }

    public function testDefault()
    {
        $command = $this->application->find('generate:class-reference');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'files'    => ABSPATH . '/testclasses/TestSeeTag.php',
            '--output' => './../temp',
        ]);

        $files = $command->getFiles($commandTester->getInput());

        $classReferenceHandler = new ClassReferenceHandler($files);

        foreach ($classReferenceHandler->getClassList() as $class) {
            $contents = $classReferenceHandler->compileClass($class);
        }

        /*$fs = new FileSystem();
        $fs->dumpFile(__DIR__ . '/output/TestClass.md', $output);*/
        // $this->assertContains('Username: Wouter', $output);
    }

    public function testTraitDocumentation()
    {
        $command = $this->application->find('generate:class-reference');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'files'    => ABSPATH . '/testclasses/TestTrait.php',
            '--output' => './../temp',
        ]);

        $files = $command->getFiles($commandTester->getInput());

        $classReferenceHandler = new ClassReferenceHandler($files);

        $classList = $classReferenceHandler->getClassList();

        // Assert that traits are being documented
        $this->assertNotEmpty($classList, 'Traits should be included in the class list');

        foreach ($classList as $class) {
            $contents = $classReferenceHandler->compileClass($class);

            // Verify the compiled output contains trait documentation
            $this->assertNotEmpty($contents, 'Trait documentation should not be empty');
            $this->assertStringContainsString('TestTrait', $contents, 'Trait name should appear in documentation');
        }
    }

    public function testClassWithTraitShowsTraitMethods()
    {
        $command = $this->application->find('generate:class-reference');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'files'    => ABSPATH . '/testclasses/',
            '--output' => './../temp',
        ]);

        $files = $command->getFiles($commandTester->getInput());

        $classReferenceHandler = new ClassReferenceHandler($files);

        $classList = $classReferenceHandler->getClassList();

        $this->assertNotEmpty($classList, 'Class list should not be empty');

        foreach ($classList as $class) {
            if ($class->getName() === 'TestClass') {
                $contents = $classReferenceHandler->compileClass($class);

                // Verify the class shows it uses the trait
                $this->assertStringContainsString('This class uses the trait', $contents, 'Class should show it uses a trait');
                $this->assertStringContainsString('TestTrait', $contents, 'Trait name should appear in trait usage statement');

                // Verify the class documentation contains trait methods
                $this->assertStringContainsString('trait_method', $contents, 'Trait method should appear in class documentation');
                $this->assertStringContainsString('Trait method summary', $contents, 'Trait method summary should appear in class documentation');

                // Verify trait properties are included
                $this->assertStringContainsString('trait_property', $contents, 'Trait property should appear in class documentation');
            }
        }
    }
}
