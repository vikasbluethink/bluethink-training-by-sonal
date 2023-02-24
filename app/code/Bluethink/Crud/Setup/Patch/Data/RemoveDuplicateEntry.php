<?php
declare(strict_types=1);

/**
 * RemoveExtraTable Patch Class
 *
 * Setup Patch data class to remove
 * table image_storage_tmp
 */

namespace Bluethink\Crud\Setup\Patch\Data;

use Laminas\Db\Sql\Predicate\IsNull;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class RemoveExtraData
 */
class RemoveDuplicateEntry implements DataPatchInterface
{
    /**
     * table entity value
     */
    const TABLE_NAME = 'bluethink_testimonial';

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * {@inheritDoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * This method used to Remove older extra table
     *
     * @return void
     */
    public function apply(): void
    {

        $installer = $this->schemaSetup;
        $installer->startSetup();

        if ($installer->tableExists(self::TABLE_NAME)) {

             $installer->getConnection()->delete($installer->getTable(self::TABLE_NAME), "name is NULL");
        }

        $installer->endSetup();
    }
}
