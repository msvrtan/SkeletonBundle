<?php
namespace NullDev\SkeletonBundle;

use NullDev\SkeletonBundle\DependencyInjection\NullDevSkeletonExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NullDevSkeletonBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new NullDevSkeletonExtension();
        }

        return $this->extension;
    }
}
