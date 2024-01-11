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
	if(!empty(sanitizeInput($_POST["date"])) && !empty(sanitizeInput($_POST["sum"])) && !empty(sanitizeInput($_POST["postage"])) && !empty(sanitizeInput($_POST["status-service"])) && !empty(sanitizeInput($_POST["status-post"])) && !empty(sanitizeInput($_POST["payment_id"]))) {
		$iblock = IblockTable::getList([
			'select' => ['ID'],
			'filter' => ['CODE' => "payments"],
		])->fetch();
		if ($iblock) {
			$VALUES = array(
				"DATE" => sanitizeInput($_POST["date"]),
				"SUM" => sanitizeInput($_POST["sum"]),
				"STATUS_SERVICE" => sanitizeInput($_POST["status-service"]),
				"POSTAGE" => sanitizeInput($_POST["postage"]),
				"STATUS_POST" => sanitizeInput($_POST["status-post"]),
			);
			CIBlockElement::SetPropertyValuesEx(sanitizeInput($_POST["payment_id"]), $iblock["ID"], $VALUES);
			$response = array(
				"success" => true,
				"message" => "Платеж обновлен",
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

