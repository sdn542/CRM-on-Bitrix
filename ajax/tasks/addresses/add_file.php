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
	if(!empty(sanitizeInput($_POST["addressed_id"])) && !empty(sanitizeInput($_POST["property_code"])) && !empty($_FILES['file'])) {
		$iblock = IblockTable::getList([
			'select' => ['ID'],
			'filter' => ['CODE' => "departures"],
		])->fetch();
		if ($iblock) {
			$VALUES = array();
			$requestFiles = $_FILES['file'];
			$arFile = array(
				"name" => $requestFiles["name"] ,
				"full_path" => $requestFiles["full_path"],
				"type" => $requestFiles["type"],
				"tmp_name" => $requestFiles["tmp_name"],
				"error" => $requestFiles["error"],
				"size" => $requestFiles["size"]
			);
			$VALUES[sanitizeInput($_POST["property_code"])][] = array(
				"VALUE" => $arFile,
				"DESCRIPTION" => date('d.m.Y')
			);
			$resAddressed = CIBlockElement::GetByID($_POST["addressed_id"])->GetNextElement();
			$arAddressed = array(
				"PROPERTIES" => $resAddressed->GetProperties()
			);
			$resCreditor = CIBlockElement::GetByID($arAddressed["PROPERTIES"]["CREDITOR"]["VALUE"])->GetNextElement();
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
					"TASK" => $_POST["task_id"],
					"CHANGED" => ($_POST["property_code"] == "DOC" ? "Документ" : "Ответ") . " адресата " . $arCreditor["FIELDS"]["NAME"],
					"BEFORE" => "Ничего",
					"AFTER" => $arFile["name"]
				)
			);
			$elHistory->Add($arHistory);
			CIBlockElement::SetPropertyValuesEx(sanitizeInput($_POST["addressed_id"]), $iblock["ID"], $VALUES);
			$response = array(
				"success" => true,
				"message" => "Файлы успешно загружены",
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
			"message" => "Не переданы обязательные данные",
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

