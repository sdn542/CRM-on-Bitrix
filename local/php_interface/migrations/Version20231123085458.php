<?php

namespace Sprint\Migration;


class Version20231123085458 extends Version
{
    protected $description = "Изменение свойств в инфоблоке Задачи";

    protected $moduleVersion = "4.2.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('tasks', 'content');
        $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Статус задачи',
  'ACTIVE' => 'Y',
  'SORT' => '500',
  'CODE' => 'STATUS_TASK',
  'DEFAULT_VALUE' => '',
  'PROPERTY_TYPE' => 'L',
  'ROW_COUNT' => '1',
  'COL_COUNT' => '30',
  'LIST_TYPE' => 'L',
  'MULTIPLE' => 'N',
  'XML_ID' => NULL,
  'FILE_TYPE' => '',
  'MULTIPLE_CNT' => '5',
  'LINK_IBLOCK_ID' => '0',
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'N',
  'IS_REQUIRED' => 'N',
  'VERSION' => '1',
  'USER_TYPE' => NULL,
  'USER_TYPE_SETTINGS' => NULL,
  'HINT' => '',
  'VALUES' => 
  array (
    0 => 
    array (
      'VALUE' => 'Выполнено',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => '2f2e2a394b812ba2da77d95b8f109d6b',
    ),
    1 => 
    array (
      'VALUE' => 'Выполняется',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'f7c8862c87fa86a35905a48d0ab1f39f',
    ),
    2 => 
    array (
      'VALUE' => 'Ждёт оплаты',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'f797a91ff0001a7ea0b0c7ef21b67b9a',
    ),
    3 => 
    array (
      'VALUE' => 'На отправку',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => '98f9bcb882e4cbf59f86855ddd1e1c94',
    ),
    4 => 
    array (
      'VALUE' => 'Новая',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'ae0ddecb7ed45b7e2e8c3b5e9bbdfa6d',
    ),
    5 => 
    array (
      'VALUE' => 'Оплачено',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'fb031773b1694140394f2b751c18dbec',
    ),
  ),
  'FEATURES' => 
  array (
    0 => 
    array (
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
    1 => 
    array (
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'LIST_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
  ),
));
    
    }

    public function down()
    {
        //your code ...
    }
}
