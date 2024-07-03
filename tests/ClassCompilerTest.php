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

    public function setUp() : void
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
}
