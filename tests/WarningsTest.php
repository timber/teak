<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Teak\Compiler\Warnings;
use Teak\Console\ClassReferenceGenerator;
use Teak\Console\ClassReferenceHandler;

class WarningsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Warnings::getInstance()->flush();
    }

    public function testWarningsAreFlushedWithContextAndDeduplicated()
    {
        $warnings = Warnings::getInstance();

        $warnings->add('Without context');
        $warnings->setContext('Foo::bar()');
        $warnings->add('Something is off');
        $warnings->add('Something is off');

        $this->assertSame(['Without context', 'Foo::bar(): Something is off'], $warnings->flush());
        $this->assertSame([], $warnings->flush(), 'Flushing should reset the list.');

        $warnings->add('After flush');

        $this->assertSame(['After flush'], $warnings->flush(), 'Flushing should reset the context.');
    }

    public function testInvalidParamTagsAreCompiledAndReported()
    {
        $classReferenceHandler = new ClassReferenceHandler([
            new \phpDocumentor\Reflection\File\LocalFile(ABSPATH . '/testclasses/TestClass.php'),
        ]);
        $contents = '';

        foreach ($classReferenceHandler->getClassList() as $class) {
            if ($class->getName() === 'TestClass') {
                $contents = $classReferenceHandler->compileClass($class);
            }
        }

        $this->assertStringContainsString('invalid_param_tags()', $contents);
        $this->assertStringContainsString('| [Invalid Tag] |', $contents, 'Invalid tag should be shown in the table.');
        $this->assertStringContainsString('<li>**[Invalid Tag]**<br>Error:', $contents, 'Invalid nested tag should be shown in the array.');
        $this->assertStringContainsString('**$valid**<br>`string`', $contents, 'Valid nested tags should still be compiled.');

        $warnings = Warnings::getInstance()->flush();

        $this->assertCount(2, $warnings);

        $prefix = 'Tests\TestClasses\TestClass::invalid_param_tags(): Invalid @param tag';
        $this->assertCount(1, array_filter($warnings, fn($w) => str_starts_with($w, $prefix . ' - ')));
        $this->assertCount(1, array_filter($warnings, fn($w) => str_starts_with($w, $prefix . ' in array description - ')));
    }

    public function testCommandPrintsWarningsToErrorOutput()
    {
        $application = new Application('Teak Documentation Test');
        $application->add(new ClassReferenceGenerator());

        $fs = new Filesystem();
        $outputFolder = sys_get_temp_dir() . '/teak-test-' . uniqid();
        $relativeOutput = rtrim($fs->makePathRelative($outputFolder, getcwd()), '/');

        $commandTester = new CommandTester($application->find('generate:class-reference'));

        try {
            $exitCode = $commandTester->execute(
                [
                    'files'    => ABSPATH . '/testclasses/TestClass.php',
                    '--output' => $relativeOutput,
                ],
                ['capture_stderr_separately' => true]
            );
        } finally {
            $fs->remove($outputFolder);
        }

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString(
            'Warning: Tests\TestClasses\TestClass::invalid_param_tags(): Invalid @param tag',
            $commandTester->getErrorOutput()
        );
        $this->assertStringNotContainsString('Warning:', $commandTester->getDisplay());
    }
}
