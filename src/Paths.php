<?php
namespace NullDev\SkeletonBundle;

use Exception;
use NullDev\Skeleton\Path\Psr0Path;
use NullDev\Skeleton\Path\Psr4Path;
use NullDev\Skeleton\Path\SpecPsr0Path;
use NullDev\Skeleton\Path\SpecPsr4Path;
use NullDev\Skeleton\Path\TestPsr0Path;
use NullDev\Skeleton\Path\TestPsr4Path;

class Paths
{
    private $list = [];

    public function __construct(array $data)
    {
        if ($data['phpspec']['enabled'] === true) {
            $this->processPhpSpec($data['phpspec']);
        }

        if ($data['phpunit']['enabled'] === true) {
            $this->processPhpUnit($data['phpspec']);
        }
        $this->processCode($data['code']);
    }

    private function processCode(array $data)
    {
        if ($data['autoload_type'] === 'psr4') {
            $this->list[] = new Psr4Path($data['path'], $data['prefix']);
        } elseif ($data['autoload_type'] === 'psr0') {
            $this->list[] = new Psr0Path($data['path'], $data['prefix']);
        } else {
            throw new Exception('Err 091212912: Unknown autoload type:'.$data['autoload_type']);
        }
    }

    private function processPhpSpec(array $data)
    {
        if ($data['autoload_type'] === 'psr4') {
            $this->list[] = new SpecPsr4Path($data['path'], $data['prefix']);
        } elseif ($data['autoload_type'] === 'psr0') {
            $this->list[] = new SpecPsr0Path($data['path'], $data['prefix']);
        } else {
            throw new Exception('Err 091214324: Unknown autoload type:'.$data['autoload_type']);
        }
    }

    private function processPhpUnit(array $data)
    {
        if ($data['autoload_type'] === 'psr4') {
            $this->list[] = new TestPsr4Path($data['path'], $data['prefix']);
        } elseif ($data['autoload_type'] === 'psr0') {
            $this->list[] = new TestPsr0Path($data['path'], $data['prefix']);
        } else {
            throw new Exception('Err 0912156757: Unknown autoload type:'.$data['autoload_type']);
        }
    }

    public function getList()
    {
        return $this->list;
    }
}
