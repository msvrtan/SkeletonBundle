<?php

declare (strict_types = 1);
namespace NullDev\SkeletonBundle\Command;

use NullDev\Skeleton\Definition\PHP\Types\ClassType;
use NullDev\Skeleton\Source\ImprovedClassSource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UuidIdentityCommand extends BaseSkeletonGeneratorCommand
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this->setName('skeleton:value-object:uuid-identity')
            ->setDescription('Generates UUID identity value object')
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

        $classType    = ClassType::create($className);
        $classSource  = $this->getSource($classType);
        $fileResource = $this->getFileResource($classSource);

        $this->handleGeneratingFile($fileResource);
    }

    protected function getSource(ClassType $classType) : ImprovedClassSource
    {
        $factory = $this->getService('null_dev.skeleton.source_factory.uuid_identity');

        return $factory->create($classType);
    }

    protected function getSectionMessage()
    {
        return 'Generate UUID identity value object';
    }

    protected function getIntroductionMessage()
    {
        return [
            '',
            'This command helps you generate UUID identity value objects.',
            '',
            'First, you need to give the class name you want to generate.',
        ];
    }
}
