<?php

namespace Sprint\Migration;


class Version20231120050451 extends Version
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
			'LINK_IBLOCK_ID' => 'content:departures',
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
			'NAME' => 'Постановщик',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'CREATOR',
			'DEFAULT_VALUE' => '',
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
			'USER_TYPE' => 'UserID',
			'USER_TYPE_SETTINGS' => NULL,
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
			'NAME' => 'Чеки',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'CHECK_FILES',
			'DEFAULT_VALUE' => '',
			'PROPERTY_TYPE' => 'F',
			'ROW_COUNT' => '1',
			'COL_COUNT' => '30',
			'LIST_TYPE' => 'L',
			'MULTIPLE' => 'Y',
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

	}

	public function down()
	{
		$helper = $this->getHelperManager();
		$iblockId = $helper->Iblock()->getIblockIdIfExists('tasks', 'content');
		$helper->Iblock()->deletePropertyIfExists($iblockId, "ADDRESSEES");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "CREATOR");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "CHECK_FILES");
	}
}
