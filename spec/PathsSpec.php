<?php

declare(strict_types=1);

namespace spec\NullDev\SkeletonBundle;

use PhpSpec\ObjectBehavior;

class PathsSpec extends ObjectBehavior
{
    public function let()
    {
        $data = [
            'code' => [
                'autoload_type' => 'psr0',
                'path'          => '/path/',
                'prefix'        => 'prefix\\',
            ],
            'phpspec' => [
                'autoload_type' => 'psr0',
                'path'          => '/path/',
                'prefix'        => 'prefix\\',
                'enabled'       => true,
            ],
            'phpunit' => [
                'autoload_type' => 'psr0',
                'path'          => '/path/',
                'prefix'        => 'prefix\\',
                'enabled'       => true,
            ],
        ];
        $this->beConstructedWith($data);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('NullDev\SkeletonBundle\Paths');
    }
}
