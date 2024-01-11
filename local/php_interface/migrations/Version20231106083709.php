<?php

namespace Sprint\Migration;


class Version20231106083709 extends Version
{
	protected $description = "Создание свойств в инфоблоке Задачи";

	protected $moduleVersion = "4.2.4";

	/**
	 * @return bool|void
	 * @throws Exceptions\HelperException
	 */
	public function up()
	{
		$helper = $this->getHelperManager();
		$iblockId = $helper->Iblock()->getIblockIdIfExists('tasks', 'content');
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TYPE");
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Тип',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'TYPE_TASK',
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
			'USER_TYPE_SETTINGS' => 'a:0:{}',
			'HINT' => '',
			'VALUES' =>
				array(
					0 =>
						array(
							'VALUE' => 'Отказ от взаимодействия с третьими лицами',
							'DEF' => 'N',
							'SORT' => '500',
							'XML_ID' => '59e5245b84e996951adda5cf14816e04',
						),
				),
			'FEATURES' =>
				array(
					0 =>
						array(
							'MODULE_ID' => 'iblock',
							'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
							'IS_ENABLED' => 'N',
						),
					1 =>
						array(
							'MODULE_ID' => 'iblock',
							'FEATURE_ID' => 'LIST_PAGE_SHOW',
							'IS_ENABLED' => 'N',
						),
				),
		));
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Нестандартная задача',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'NON_STANDART',
			'DEFAULT_VALUE' => '',
			'PROPERTY_TYPE' => 'L',
			'ROW_COUNT' => '1',
			'COL_COUNT' => '30',
			'LIST_TYPE' => 'C',
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
			'USER_TYPE_SETTINGS' => 'a:0:{}',
			'HINT' => '',
			'VALUES' =>
				array(
					0 =>
						array(
							'VALUE' => 'Y',
							'DEF' => 'N',
							'SORT' => '500',
							'XML_ID' => '095714e6fe2ab3cd6d34281e78b05af5',
						),
				),
			'FEATURES' =>
				array(
					0 =>
						array(
							'MODULE_ID' => 'iblock',
							'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
							'IS_ENABLED' => 'N',
						),
					1 =>
						array(
							'MODULE_ID' => 'iblock',
							'FEATURE_ID' => 'LIST_PAGE_SHOW',
							'IS_ENABLED' => 'N',
						),
				),
		));
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Адресаты',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'ADDRESSEES',
			'DEFAULT_VALUE' => '',
			'PROPERTY_TYPE' => 'E',
			'ROW_COUNT' => '1',
			'COL_COUNT' => '30',
			'LIST_TYPE' => 'L',
			'MULTIPLE' => 'Y',
			'XML_ID' => NULL,
			'FILE_TYPE' => '',
			'MULTIPLE_CNT' => '5',
			'LINK_IBLOCK_ID' => 'content:creditors',
			'WITH_DESCRIPTION' => 'N',
			'SEARCHABLE' => 'N',
			'FILTRABLE' => 'N',
			'IS_REQUIRED' => 'N',
			'VERSION' => '1',
			'USER_TYPE' => NULL,
			'USER_TYPE_SETTINGS' => 'a:0:{}',
			'HINT' => '',
			'FEATURES' =>
				array(
					0 =>
						array(
							'MODULE_ID' => 'iblock',
							'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
							'IS_ENABLED' => 'N',
						),
					1 =>
						array(
							'MODULE_ID' => 'iblock',
							'FEATURE_ID' => 'LIST_PAGE_SHOW',
							'IS_ENABLED' => 'N',
						),
				),
		));
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Крайний срок',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'DEADLINE',
			'DEFAULT_VALUE' => NULL,
			'PROPERTY_TYPE' => 'S',
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
			'USER_TYPE' => 'Date',
			'USER_TYPE_SETTINGS' => NULL,
			'HINT' => '',
		));
	}

	public function down()
	{
		$helper = $this->getHelperManager();
		$iblockId = $helper->Iblock()->getIblockIdIfExists('tasks', 'content');
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TYPE_TASK");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "NON_STANDART");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "ADDRESSEES");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "DEADLINE");
	}
}
