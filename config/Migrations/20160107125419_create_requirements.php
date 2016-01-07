<?php
use Migrations\AbstractMigration;

class CreateRequirements extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('requirements');
        $table->addColumn('code', 'string', [
            'default' => null,
            'limit' => 16,
            'null' => false,
        ]);
        $table->addColumn('status', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addIndex([
            'code',
        ], [
            'name' => 'UNIQUE_CODE',
            'unique' => true,
        ]);
        $table->create();
    }
}
