<?php

namespace Sprint\Migration;


class Version20231106084429 extends Version
{
	protected $description = "Создание свойств инфоблока Кредиторы";

	protected $moduleVersion = "4.2.4";

	/**
	 * @return bool|void
	 * @throws Exceptions\HelperException
	 */
	public function up()
	{
		$helper = $this->getHelperManager();
		$iblockId = $helper->Iblock()->getIblockIdIfExists('creditors', 'content');
		$helper->Iblock()->saveProperty($iblockId, array(
			'NAME' => 'Адрес',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'ADDRESS',
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
			'USER_TYPE' => NULL,
			'USER_TYPE_SETTINGS' => 'a:0:{}',
			'HINT' => '',
		));

	}

	public function down()
	{
		$helper = $this->getHelperManager();
		$iblockId = $helper->Iblock()->getIblockIdIfExists('creditors', 'content');
		$helper->Iblock()->deletePropertyIfExists($iblockId, "ADDRESS");
	}
}
