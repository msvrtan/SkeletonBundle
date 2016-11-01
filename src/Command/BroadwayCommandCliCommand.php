<?php

declare(strict_types=1);

namespace NullDev\SkeletonBundle\Command;

use NullDev\Skeleton\Definition\PHP\Types\ClassType;
use NullDev\Skeleton\Source\ImprovedClassSource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BroadwayCommandCliCommand extends BaseSkeletonGeneratorCommand
{
    protected function configure()
    {
        $this->setName('skeleton:broadway:command')
            ->setDescription('Generates Broadway command')
            ->addOption('className', null, InputOption::VALUE_REQUIRED, 'Class name');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $className = $this->handleClassNameInput();
        $fields    = $this->getConstuctorParameters();

        $classType    = ClassType::create($className);
        $classSource  = $this->getSource($classType, $fields);
        $fileResource = $this->getFileResource($classSource);

        $this->handleGeneratingFile($fileResource);

        //Generating PHPSpec.
        $createSpecQuestion = new ConfirmationQuestion('Create PHPSpec file? (default=y)', true);
        if ($this->askQuestion($createSpecQuestion)) {
            $specSource   = $this->createSpecSource($classSource);
            $specResource = $this->getFileResource($specSource);
            $this->handleGeneratingFile($specResource);
        }
    }

    private function getSource(ClassType $classType, array $fields) : ImprovedClassSource
    {
        $factory = $this->getService('null_dev.skeleton.source_factory.broadway.command');

        return $factory->create($classType, $fields);
    }

    protected function getSectionMessage()
    {
        return 'Generate Broadway command';
    }

    protected function getIntroductionMessage()
    {
        return [
            '',
            'This command helps you generate Broadway commands.',
            '',
            'First, you need to give the class name you want to generate.',
        ];
    }
}
