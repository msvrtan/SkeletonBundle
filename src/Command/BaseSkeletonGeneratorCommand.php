<?php

declare (strict_types = 1);
namespace NullDev\SkeletonBundle\Command;

use NullDev\Skeleton\CodeGenerator\PhpParserGeneratorFactory;
use NullDev\Skeleton\Definition\PHP\Parameter;
use NullDev\Skeleton\Definition\PHP\Types\Type;
use NullDev\Skeleton\Definition\PHP\Types\TypeFactory;
use NullDev\Skeleton\File\FileFactory;
use NullDev\Skeleton\File\FileGenerator;
use NullDev\Skeleton\File\FileResource;
use NullDev\Skeleton\Source\ImprovedClassSource;
use PhpSpec\Exception\Example\PendingException;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class BaseSkeletonGeneratorCommand extends GeneratorCommand
{
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;

    private $paths;
    private $existingNamespaces;
    private $existingClasses;

    protected function getConstuctorParameters() : array
    {
        $fields = [];

        while (true) {
            $parameterClassName = $this->askForParameterClassName();

            if (true === empty($parameterClassName)) {
                break;
            }
            $parameterClassType = $this->createClassFromParameterClassName($parameterClassName);
            $parameterName      = $this->askForParameterName(lcfirst($parameterClassType->getName()));

            $fields[] = new Parameter($parameterName, $parameterClassType);
        }

        return $fields;
    }

    protected function handleClassNameInput()
    {
        if (false === empty($this->input->getOption('className'))) {
            return str_replace('/', '\\', $this->input->getOption('className'));
        }
        $this->getQuestionHelper()->writeSection($this->output, $this->getSectionMessage());

        $this->output->writeln($this->getIntroductionMessage());

        while (true) {
            $className = $this->askForClassName();

            if (true === empty($className)) {
                $this->output->writeln('No class name, please enter it');
            } else {
                break;
            }
        }

        return str_replace('/', '\\', $className);
    }

    protected function handleGeneratingFile(FileResource $fileResource)
    {
        if ($this->fileNotExistsOrShouldBeOwerwritten($fileResource)) {
            $this->createFile($fileResource);
            $this->output->writeln('File "'.$fileResource->getFileName().'" created.');
        } else {
            $this->output->writeln('No file created.');
        }
    }

    protected function fileNotExistsOrShouldBeOwerwritten(FileResource $fileResource) : bool
    {
        if (false === file_exists($fileResource->getFileName())) {
            return true;
        }

        return $this->askOverwriteConfirmationQuestion();
    }

    protected function askForClassName()
    {
        $question = new Question($this->getQuestionHelper()->getQuestion('Enter class name', ''));
        $question->setAutocompleterValues($this->getExistingNamespaces());

        return $this->askQuestion($question);
    }

    protected function askForParameterClassName()
    {
        $question = new Question($this->getQuestionHelper()->getQuestion('Enter parameter class name', ''));
        $question->setAutocompleterValues($this->getExistingClasses());

        return $this->askQuestion($question);
    }

    protected function askForParameterName(string $suggestedName)
    {
        $questionName = new Question(
            $this->getQuestionHelper()->getQuestion('Enter parameter name', $suggestedName),
            $suggestedName
        );

        return $this->askQuestion($questionName);
    }

    protected function askOverwriteConfirmationQuestion() : bool
    {
        $question = new ConfirmationQuestion('File exists, overwrite?', false);

        return $this->askQuestion($question);
    }

    protected function askQuestion(Question $question)
    {
        return $this->getQuestionHelper()->ask($this->input, $this->output, $question);
    }

    protected function getFileResource(ImprovedClassSource $classSource) : FileResource
    {
        $factory = new FileFactory($this->getPaths());

        return $factory->create($classSource);
    }

    protected function createFile(FileResource $fileResource)
    {
        $fileGenerator = new FileGenerator(new Filesystem(), PhpParserGeneratorFactory::create());

        $fileGenerator->create($fileResource);
    }

    protected function createClassFromParameterClassName(string $name) : Type
    {
        return TypeFactory::createFromName($name);
    }

    protected function getExistingNamespaces() : array
    {
        if (null === $this->existingNamespaces) {
            $sourceCodePathReader = $this->getService('null_dev.skeleton.path.reader.source_code');

            $this->existingNamespaces = $sourceCodePathReader->getExistingPaths($this->getPaths());
        }

        return $this->existingNamespaces;
    }

    protected function getExistingClasses() : array
    {
        if (null === $this->existingClasses) {
            $sourceCodePathReader = $this->getService('null_dev.skeleton.path.reader.source_code');

            $this->existingClasses = $sourceCodePathReader->getExistingClasses($this->getPaths());
        }

        return $this->existingClasses;
    }

    protected function getPaths()
    {
        if (null === $this->paths) {
            $this->paths = $this->getService('nulldev_skeleton.paths')->getList();
        }

        return $this->paths;
    }

    protected function createGenerator()
    {
        throw new PendingException();
    }

    protected function getService(string $serviceName)
    {
        return $this->getContainer()->get($serviceName);
    }
}
