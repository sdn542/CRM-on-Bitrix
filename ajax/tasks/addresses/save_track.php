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
	if(!empty(sanitizeInput($_POST["track"])) && !empty(sanitizeInput($_POST["addressed_id"]))) {
		$iblock = IblockTable::getList([
			'select' => ['ID'],
			'filter' => ['CODE' => "departures"],
		])->fetch();
		if ($iblock) {
			$VALUES = array(
				"TRACK_NUMBER" => sanitizeInput($_POST["track"]),
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
					"CHANGED" => "Трек-номер адресата " . $arCreditor["FIELDS"]["NAME"],
					"BEFORE" => !empty($arAddressed["PROPERTIES"]["TRACK_NUMBER"]["VALUE"]) ? $arAddressed["PROPERTIES"]["TRACK_NUMBER"]["VALUE"] : "Ничего",
					"AFTER" => $_POST["track"]
				)
			);
			$elHistory->Add($arHistory);
			CIBlockElement::SetPropertyValuesEx(sanitizeInput($_POST["addressed_id"]), $iblock["ID"], $VALUES);
			$response = array(
				"success" => true,
				"message" => "Трек-номер обновлен",
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

