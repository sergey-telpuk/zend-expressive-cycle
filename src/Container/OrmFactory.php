<?php

declare(strict_types=1);

namespace ZendCycle\Container;

use Cycle\Annotated\{Embeddings, Entities};
use Cycle\ORM\{Factory, ORM, ORMInterface, Schema as OrmSchema};
use Cycle\Schema\{Compiler, Registry};
use Cycle\Schema\Generator\{GenerateRelations,
    GenerateTypecast,
    RenderRelations,
    RenderTables,
    ResetTables,
    SyncTables,
    ValidateEntities};
use Psr\Container\ContainerInterface;
use Spiral\Database\{DatabaseManager};
use Spiral\Database\Exception\{ConfigException};
use Spiral\Database\Config\{DatabaseConfig};
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

class CycleFactory
{
    /**
     * @param ContainerInterface $container
     * @return ORMInterface
     */
    public function __invoke(ContainerInterface $container): ORMInterface
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];

        if (!isset($config['cycle'])) {
            throw new ConfigException('Expected config databases');
        }
        $config = $config['cycle'];

        $entities = $config['entities'];

        $finder = (new Finder())->files()->in($entities);
        $cl = new ClassLocator($finder);

        $dbal = new DatabaseManager(new DatabaseConfig($config));

        $schema = (new Compiler())->compile(new Registry($dbal), [
            new Embeddings($cl),            // register embeddable entities
            new Entities($cl),              // register annotated entities
            new ResetTables(),       // re-declared table schemas (remove columns)
            new GenerateRelations(), // generate entity relations
            new ValidateEntities(),  // make sure all entity schemas are correct
            new RenderTables(),      // declare table schemas
            new RenderRelations(),   // declare relation keys and indexes
            new SyncTables(),        // sync table changes to database
            new GenerateTypecast(),  // typecast non string columns
        ]);

        $orm = new ORM(new Factory($dbal));
        $orm = $orm->withSchema(new OrmSchema($schema));

        return $orm;
    }
}
