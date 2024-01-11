<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
CModule::IncludeModule("iblock");

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$taskId = sanitizeInput($_POST["task_id"]);
	$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'departures'], true)->Fetch();
	$iblockId = $iblock['ID'];
	$el = new CIBlockElement;
	$arAddresat = array(
		"IBLOCK_ID" => $iblockId,
		"NAME" => "Отправление",
		"ACTIVE" => "Y",
		"ACTIVE_FROM" => FormatDate("d.m.Y", time()),
		"PROPERTY_VALUES" => array(
			"CREDITOR" => sanitizeInput($_POST["creditor"])
		)
	);
	$resCreditor = CIBlockElement::GetByID($_POST["creditor"])->GetNextElement();
	$arCreditor = array(
		"FIELDS" => $resCreditor->GetFields()
	);
	$iblockHistory = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'history'], true)->Fetch();
	$iblockHistoryId = $iblockHistory['ID'];
	$elHistory = new CIBlockElement;
	$arHistory = array(
		"IBLOCK_ID" => $iblockHistoryId,
		"NAME" => "Изменение задачи " . $_POST["task_id"],
		"ACTIVE" => "Y",
		"ACTIVE_FROM" => FormatDate("d.m.Y", time()),
		"PROPERTY_VALUES" => array(
			"DATETIME" => FormatDate("d.m.Y H:m:i", time()),
			"USER" => $USER->GetID(),
			"TASK" => $taskId,
			"CHANGED" => "Адресат",
			"BEFORE" => "Ничего",
			"AFTER" => $arCreditor["FIELDS"]["NAME"]
		)
	);
	$elHistory->Add($arHistory);

	if ($addresatID = $el->Add($arAddresat)) {
		$taskId = sanitizeInput($_POST["task_id"]);
		$iblockTask = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'tasks'], true)->Fetch();
        $resTask = CIBlockElement::GetByID($taskId)->GetNextElement();
        $arTask = array(
            "FIELDS" => $resTask->GetFields(),
            "PROPERTIES" => $resTask->GetProperties()
        );
        $arTask["PROPERTIES"]["ADDRESSEES"]["VALUE"][] = $addresatID;
		$propertyValues = array(
			"ADDRESSEES" => $arTask["PROPERTIES"]["ADDRESSEES"]["VALUE"]
		);
		CIBlockElement::SetPropertyValuesEx($taskId, $iblockTask["ID"], $propertyValues);
		// Элемент успешно создан
		$response = array(
			"success" => true,
			"message" => "Адресат успешно создан",
		);
	} else {
		// Ошибка создания элемента
		$response = array(
			"success" => false,
			"message" => "Ошибка при создании адресата: " . $el->LAST_ERROR,
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
