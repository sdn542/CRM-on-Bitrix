<?php

namespace Sprint\Migration;


class Version20231106084604 extends Version
{
	protected $description = "Создание свойств инфоблока Основное";

	protected $moduleVersion = "4.2.4";

	/**
	 * @return bool|void
	 * @throws Exceptions\HelperException
	 */
	public function up()
	{
		$helper = $this->getHelperManager();
		$iblockId = $helper->Iblock()->getIblockIdIfExists('main-content', 'content');
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Шаблоны конверта',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'TEMPLATES_ENVELOPE',
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
			'WITH_DESCRIPTION' => 'Y',
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
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Шаблоны типов договоров',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'TEMPLATES_TYPES_CONTRACTS',
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
			'WITH_DESCRIPTION' => 'Y',
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
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Шаблоны документов',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'TEMPLATES_DOCS',
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
			'WITH_DESCRIPTION' => 'Y',
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
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Переменные для шаблонов',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'VARIABLES_TEMPLATES',
			'DEFAULT_VALUE' =>
				array(
					'TEXT' => '',
					'TYPE' => 'HTML',
				),
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
			'USER_TYPE' => 'HTML',
			'USER_TYPE_SETTINGS' =>
				array(
					'height' => 200,
				),
			'HINT' => '',
		));
	}

	public function down()
	{
		$helper = $this->getHelperManager();
		$iblockId = $helper->Iblock()->getIblockIdIfExists('main-content', 'content');
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TEMPLATES_ENVELOPE");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TEMPLATES_TYPES_CONTRACTS");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TEMPLATES_DOCS");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "VARIABLES_TEMPLATES");
	}
}
