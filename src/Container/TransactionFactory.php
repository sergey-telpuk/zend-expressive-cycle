<?php
declare(strict_types=1);

namespace ZendCycle\Container;

use Cycle\ORM\Transaction;
use Cycle\ORM\TransactionInterface;
use Psr\Container\ContainerInterface;

class TransactionFactory
{
    /**
     * @param ContainerInterface $container
     * @return TransactionInterface
     */
    public function __invoke(ContainerInterface $container): TransactionInterface
    {
        return new Transaction($container->get(CycleFactory::class));
    }
}