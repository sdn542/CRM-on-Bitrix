<?php

namespace Sprint\Migration;


class Version20231106084045 extends Version
{
	protected $description = "Создание пользовательских свойств";

	protected $moduleVersion = "4.2.4";

	/**
	 * @return bool|void
	 * @throws Exceptions\HelperException
	 */
	public function up()
	{
		$helper = $this->getHelperManager();
		$helper->UserTypeEntity()->saveUserTypeEntity(array(
			'ENTITY_ID' => 'USER',
			'FIELD_NAME' => 'UF_AUTHORIZATION_NUMBER',
			'USER_TYPE_ID' => 'string',
			'XML_ID' => '',
			'SORT' => '100',
			'MULTIPLE' => 'N',
			'MANDATORY' => 'N',
			'SHOW_FILTER' => 'N',
			'SHOW_IN_LIST' => 'Y',
			'EDIT_IN_LIST' => 'Y',
			'IS_SEARCHABLE' => 'N',
			'SETTINGS' =>
				array(
					'SIZE' => 20,
					'ROWS' => 1,
					'REGEXP' => '',
					'MIN_LENGTH' => 0,
					'MAX_LENGTH' => 0,
					'DEFAULT_VALUE' => '',
				),
			'EDIT_FORM_LABEL' =>
				array(
					'en' => 'Authorization number',
					'ru' => '№ доверенности',
				),
			'LIST_COLUMN_LABEL' =>
				array(
					'en' => 'Authorization number',
					'ru' => '№ доверенности',
				),
			'LIST_FILTER_LABEL' =>
				array(
					'en' => '',
					'ru' => '',
				),
			'ERROR_MESSAGE' =>
				array(
					'en' => '',
					'ru' => '',
				),
			'HELP_MESSAGE' =>
				array(
					'en' => '',
					'ru' => '',
				),
		));
		$helper->UserTypeEntity()->saveUserTypeEntity(array(
			'ENTITY_ID' => 'USER',
			'FIELD_NAME' => 'UF_AUTHORIZATION_DATE',
			'USER_TYPE_ID' => 'date',
			'XML_ID' => '',
			'SORT' => '100',
			'MULTIPLE' => 'N',
			'MANDATORY' => 'N',
			'SHOW_FILTER' => 'N',
			'SHOW_IN_LIST' => 'Y',
			'EDIT_IN_LIST' => 'Y',
			'IS_SEARCHABLE' => 'N',
			'SETTINGS' =>
				array(
					'DEFAULT_VALUE' =>
						array(
							'TYPE' => 'NONE',
							'VALUE' => '',
						),
				),
			'EDIT_FORM_LABEL' =>
				array(
					'en' => 'Authorization date',
					'ru' => 'Дата доверенности',
				),
			'LIST_COLUMN_LABEL' =>
				array(
					'en' => 'Authorization date',
					'ru' => 'Дата доверенности',
				),
			'LIST_FILTER_LABEL' =>
				array(
					'en' => '',
					'ru' => '',
				),
			'ERROR_MESSAGE' =>
				array(
					'en' => '',
					'ru' => '',
				),
			'HELP_MESSAGE' =>
				array(
					'en' => '',
					'ru' => '',
				),
		));
	}

	public function down()
	{
		$helper = $this->getHelperManager();
		$helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_AUTHORIZATION_NUMBER');
		$helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_AUTHORIZATION_DATE');
	}
}
