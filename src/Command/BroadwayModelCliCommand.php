<?php

declare (strict_types = 1);
namespace NullDev\SkeletonBundle\Command;

use NullDev\Skeleton\Definition\PHP\Parameter;
use NullDev\Skeleton\Definition\PHP\Types\ClassType;
use NullDev\Skeleton\Source\ImprovedClassSource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class BroadwayModelCliCommand extends BaseSkeletonGeneratorCommand
{
    protected function configure()
    {
        $this->setName('skeleton:broadway:model')
            ->setDescription('Generates Broadway model')
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

        $this->handle();
    }

    protected function handle()
    {
        $className = $this->handleClassNameInput();

        //Id
        $modelIdClassType    = ClassType::create($className.'Id');
        $modelIdClassSource  = $this->getModelIdSource($modelIdClassType);
        $modelIdFileResource = $this->getFileResource($modelIdClassSource);

        $this->output->writeln('Generating Id file');
        $this->handleGeneratingFile($modelIdFileResource);

        //Entity
        $modelClassType    = ClassType::create($className.'Model');
        $modelClassSource  = $this->getModelSource($modelClassType, $modelIdClassType);
        $modelFileResource = $this->getFileResource($modelClassSource);

        $this->output->writeln('Generating Model file');
        $this->handleGeneratingFile($modelFileResource);

        //Repository
        $repositoryClassType    = ClassType::create($className.'Repository');
        $repositoryClassSource  = $this->getModelRepositorySource($repositoryClassType, $modelClassType);
        $repositoryFileResource = $this->getFileResource($repositoryClassSource);

        $this->output->writeln('Generating Repository file');
        $this->handleGeneratingFile($repositoryFileResource);
    }

    private function getModelIdSource(ClassType $modelIdClassType) : ImprovedClassSource
    {
        $factory = $this->getService('null_dev.skeleton.source_factory.uuid_identity');

        return $factory->create($modelIdClassType);
    }

    private function getModelSource(ClassType $modelClassType, ClassType $modelIdClassType) : ImprovedClassSource
    {
        $factory   = $this->getService('null_dev.skeleton.source_factory.broadway.event_sourced_aggregate_root');
        $parameter = new Parameter(lcfirst($modelIdClassType->getName()), $modelIdClassType);

        return $factory->create($modelClassType, $parameter);
    }

    private function getModelRepositorySource(
        ClassType $repositoryClassType,
        ClassType $modelClassType
    ) : ImprovedClassSource {
        $factory = $this->getService('null_dev.skeleton.source_factory.broadway.event_sourcing_repository');

        return $factory->create($repositoryClassType, $modelClassType);
    }

    protected function getSectionMessage()
    {
        return 'Generate Broadway model';
    }

    protected function getIntroductionMessage()
    {
        return [
            '',
            'This command helps you generate Broadway model.',
            '',
            'First, you need to give the class name you want to generate.',
            'IMPORTANT!: Dont add model suffix!',
        ];
    }
}
