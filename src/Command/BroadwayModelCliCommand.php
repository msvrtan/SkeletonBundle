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
use Symfony\Component\Console\Question\ConfirmationQuestion;

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

        //Generating PHPSpec for Id.
        $createSpecQuestion = new ConfirmationQuestion('Create PHPSpec file for id? (default=y)', true);
        if ($this->askQuestion($createSpecQuestion)) {
            $modelIdSpecSource   = $this->createSpecSource($modelIdClassSource);
            $modelIdSpecResource = $this->getFileResource($modelIdSpecSource);
            $this->handleGeneratingFile($modelIdSpecResource);
        }

        //Entity
        $modelClassType    = ClassType::create($className.'Model');
        $modelClassSource  = $this->getModelSource($modelClassType, $modelIdClassType);
        $modelFileResource = $this->getFileResource($modelClassSource);

        $this->output->writeln('Generating Model file');
        $this->handleGeneratingFile($modelFileResource);

        //Generating PHPSpec for Entity.
        $createSpecQuestion = new ConfirmationQuestion('Create PHPSpec file for entity? (default=y)', true);
        if ($this->askQuestion($createSpecQuestion)) {
            $modelSpecSource   = $this->createSpecSource($modelClassSource);
            $modelSpecResource = $this->getFileResource($modelSpecSource);
            $this->handleGeneratingFile($modelSpecResource);
        }

        //Repository
        $repositoryClassType    = ClassType::create($className.'Repository');
        $repositoryClassSource  = $this->getModelRepositorySource($repositoryClassType, $modelClassType);
        $repositoryFileResource = $this->getFileResource($repositoryClassSource);

        $this->output->writeln('Generating Repository file');
        $this->handleGeneratingFile($repositoryFileResource);

        //Generating PHPSpec for Repository.
        $createSpecQuestion = new ConfirmationQuestion('Create PHPSpec file for repository? (default=y)', true);
        if ($this->askQuestion($createSpecQuestion)) {
            $readRepositorySpecSource   = $this->createSpecSource($repositoryClassSource);
            $readRepositorySpecResource = $this->getFileResource($readRepositorySpecSource);
            $this->handleGeneratingFile($readRepositorySpecResource);
        }
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
