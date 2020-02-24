<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 14/01/2020
 * Time: 17:10
 */

namespace SaltId\SeoSerpBundle;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;

class Installer extends MigrationInstaller
{
    const TABLE_SEO = 'bundle_seoserp_seo';

    const TABLE_SEO_RULE = 'bundle_seoserp_seo_rule';

    /**
     * Executes install migration. Used during installation for initial creation of database tables and other data
     * structures (e.g. pimcore classes). The version object is the version object which can be used to add raw SQL
     * queries via `addSql`.
     *
     * If possible, use the Schema object to manipulate DB state (see Doctrine Migrations)
     *
     * @param Schema $schema
     * @param Version $version
     */
    public function migrateInstall(Schema $schema, Version $version)
    {
        $this->installSeoTable($schema, $version);
        $this->installSeoRuleTable($schema, $version);
    }

    /**
     * Opposite of migrateInstall called on uninstallation of a bundle.
     *
     * @param Schema $schema
     * @param Version $version
     */
    public function migrateUninstall(Schema $schema, Version $version)
    {
        $this->uninstallSeoTable($schema, $version);
        $this->uninstallSeoRuleTable($schema, $version);
    }

    private function installSeoTable(Schema $schema, Version $version)
    {
        $table = $schema->createTable(self::TABLE_SEO);
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);
        $table->addColumn('objectId', 'integer', []);
        $table->addUniqueIndex(['objectId']);

        $table->addColumn('data', 'text');
        $table->setPrimaryKey(['id']);
    }

    private function uninstallSeoTable(Schema $schema, Version $version)
    {
        $schema->dropTable(self::TABLE_SEO);
    }

    private function installSeoRuleTable(Schema $schema, Version $version)
    {
        $table = $schema->createTable(self::TABLE_SEO_RULE);

        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);

        $table->addColumn('name', 'string', [
            'default' => ''
        ]);

        $table->addColumn('routeName', 'string', [
            'notnull' => false
        ]);
        $table->addColumn('routeVariable', 'string', [
            'notnull' => false
        ]);
        $table->addColumn('className', 'string', [
            'notnull' => false
        ]);
        $table->addColumn('classField', 'string', [
            'notnull' => false
        ]);

        $table->addColumn('active', 'smallint', [
            'default' => 1,
            'notnull' => false
        ]);
        $table->addColumn('metadata', 'text', [
            'notnull' => false
        ]);

        $table->addUniqueIndex(['routeName', 'className']);
        $table->setPrimaryKey(['id']);
    }

    private function uninstallSeoRuleTable(Schema $schema, Version $version)
    {
        $schema->dropTable(self::TABLE_SEO_RULE);
    }

}