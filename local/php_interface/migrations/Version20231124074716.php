<?php

namespace Sprint\Migration;


class Version20231124074716 extends Version
{
    protected $description = "Создание групп пользователей";

    protected $moduleVersion = "4.2.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->UserGroup()->saveGroup('director',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '1',
  'ANONYMOUS' => 'N',
  'NAME' => 'Руководитель',
  'DESCRIPTION' => ''
));
        $helper->UserGroup()->saveGroup('chief_lawyers',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '2',
  'ANONYMOUS' => 'N',
  'NAME' => 'Главный юрист',
  'DESCRIPTION' => ''
));
        $helper->UserGroup()->saveGroup('senior_lawyers',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '3',
  'ANONYMOUS' => 'N',
  'NAME' => 'Старшие юристы',
  'DESCRIPTION' => ''
));
        $helper->UserGroup()->saveGroup('junior_lawyers',array (
  'ACTIVE' => 'Y',
  'C_SORT' => '4',
  'ANONYMOUS' => 'N',
  'NAME' => 'Младшие юристы',
  'DESCRIPTION' => ''
));
    }

    public function down()
    {
        //your code ...
    }
}
