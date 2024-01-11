<?php

namespace Sprint\Migration;


class Version20231113100526 extends Version
{
	protected $description = "Создание свойства Политика конфиденциальности в инфоблоке Основное";

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
			'NAME' => 'Политика конфиденциальности',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'PRIVACY_POLICY',
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
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TEMPLATES_ENVELOPE");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TEMPLATES_TYPES_CONTRACTS");
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TEMPLATES_DOCS");
	}

	public function down()
	{
		$helper = $this->getHelperManager();
		$iblockId = $helper->Iblock()->getIblockIdIfExists('main-content', 'content');
		$helper->Iblock()->deletePropertyIfExists($iblockId, "PRIVACY_POLICY");
	}
}
