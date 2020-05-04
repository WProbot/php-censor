<?php

use Phinx\Migration\AbstractMigration;

class AddedProjectPeriodicalConfig extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')
            ->addColumn('periodical_config', 'text', ['null' => true])
            ->save();
    }

    public function down()
    {
        $this
            ->table('project')
            ->removeColumn('periodical_config')
            ->save();
    }
}
