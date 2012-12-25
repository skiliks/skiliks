<?php

class m121221_165626_window extends CDbMigration
{
    public function up()
    {
        $this->createTable('window', array(
            'id' => 'pk',
            'type' => 'string',
            'subtype' => 'string'
        ));
        $this->createIndex('type_subtype_unique', 'window', 'type, subtype', true);

        $subScreens = array(
            1 => 'main screen',
            3 => 'plan',
            11 => 'mail main',
            12 => 'mail preview',
            13 => 'mail new',
            14 => 'mail plan',
            21 => 'phone main',
            23 => 'phone talk',
            24 => 'phone call',
            31 => 'visitor entrance',
            32 => 'visitor talk',
            41 => 'documents main',
            42 => 'documents files'
        );

        foreach (array_keys($subScreens) as $subScreen) {
            $window = new Window();
            $window->id = $subScreen;
            $window->subtype = $subScreens[$subScreen];
            $subtype_parts = explode(' ', $window->subtype);
            $window->type = $window->subtype === 'main screen' ? $window->subtype : $subtype_parts[0];
            $window->save();
        }
        return true;

    }

    public function down()
    {
        $this->dropTable('window');
        return true;
    }

}