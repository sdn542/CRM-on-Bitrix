<?php

namespace Sprint\Migration;


class Version20231120050404 extends Version
{
	protected $description = "Создание свойства Шаблон в инфоблоке Кредиторы";

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
			'NAME' => 'Шаблон',
			'ACTIVE' => 'Y',
			'SORT' => '500',
			'CODE' => 'TEMPLATE',
			'DEFAULT_VALUE' => '',
			'PROPERTY_TYPE' => 'F',
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
		$helper->Iblock()->deletePropertyIfExists($iblockId, "TEMPLATE");
	}
}
