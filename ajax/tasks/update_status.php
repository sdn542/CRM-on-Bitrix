<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Bitrix\Iblock\IblockTable;

CModule::IncludeModule("main");
CModule::IncludeModule('iblock');

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$taskId = sanitizeInput($_POST["task_id"]);
	$iblock = IblockTable::getList([
		'select' => ['ID'],
		'filter' => ['CODE' => "tasks"],
	])->fetch();
	if ($iblock && $taskId) {
		$VALUES = array();
		foreach ($_POST as $key => $value){
			$VALUES[$key] = sanitizeInput($value);
		}
		if (!empty($VALUES["STATUS_TASK"]))
		{
			$resTask = CIBlockElement::GetByID($taskId)->GetNextElement();
			$arTask = array(
				"FIELDS" => $resTask->GetFields(),
				"PROPERTIES" => $resTask->GetProperties()
			);
			$propertyIdStatusTask = CIBlockProperty::GetList(
				array(),
				array('IBLOCK_ID' => $iblock["ID"], 'CODE' => "STATUS_TASK")
			)->Fetch()['ID'];
			$enumFinalStatusTask = CIBlockPropertyEnum::GetList(
				array(),
				array('PROPERTY_ID' => $propertyIdStatusTask, 'VALUE' => "Выполнено")
			);
			if ($enumStatusTask = $enumFinalStatusTask->Fetch()) {
				$enumIdFinalStatusTask = $enumStatusTask['ID'];
			}
			if($VALUES["STATUS_TASK"] == $enumIdFinalStatusTask && $arTask["PROPERTIES"]["STATUS_TASK"]["VALUE_ENUM_ID"] != $enumIdFinalStatusTask)
			{
				$VALUES["DATE_SENDING"] = FormatDate("d.m.Y", time());
			}

			if(!empty($arTask["PROPERTIES"]["DATE_SENDING"]["VALUE"])) {
				if($VALUES["STATUS_TASK"] != $enumIdFinalStatusTask)
				{
					$VALUES["DATE_SENDING"] = "";
				}
			}
		}

		CIBlockElement::SetPropertyValuesEx($taskId, $iblock["ID"], $VALUES);
		$response = array(
			"success" => true,
			"message" => "Статус обновлен",
		);
	} else {
		$response = array(
			"success" => false,
			"message" => "Инфоблок не найден",
		);
	}
} else {
	$response = array(
		"success" => false,
		"message" => "Недопустимый метод запроса",
	);
}

// echo Json::encode($response, JSON_UNESCAPED_UNICODE);

?>
<script>
	history.back()
</script>
