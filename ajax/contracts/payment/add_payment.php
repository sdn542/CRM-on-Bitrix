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
	if(!empty(sanitizeInput($_POST["date"])) && !empty(sanitizeInput($_POST["sum"])) && !empty(sanitizeInput($_POST["postage"])) && !empty(sanitizeInput($_POST["status-service"])) && !empty(sanitizeInput($_POST["status-post"])) && !empty(sanitizeInput($_POST["contract_id"]))) {
		$contractId = sanitizeInput($_POST["contract_id"]);
		$iblock = IblockTable::getList([
			'select' => ['ID'],
			'filter' => ['CODE' => "payments"],
		])->fetch();
		if ($iblock) {
			$iblockPayment = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'payments'], true)->Fetch();
			$iblockPaymentId = $iblockPayment['ID'];
			$el = new CIBlockElement;
			$arPayment = array(
				"IBLOCK_ID" => $iblockPaymentId,
				"NAME" => "Платеж",
				"ACTIVE" => "Y",
				"PROPERTY_VALUES" => array(
					"DATE" => sanitizeInput($_POST["date"]),
					"SUM" => sanitizeInput($_POST["sum"]),
					"POSTAGE" => sanitizeInput($_POST["postage"]),
					"STATUS_SERVICE" => sanitizeInput($_POST["status-service"]),
					"STATUS_POST" => sanitizeInput($_POST["status-post"]),
				)
			);
			if ($paymentID = $el->Add($arPayment)) {
				$iblockContract = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'contracts'], true)->Fetch();
				$resContract = CIBlockElement::GetByID($contractId)->GetNextElement();
				$arContract = array(
					"FIELDS" => $resContract->GetFields(),
					"PROPERTIES" => $resContract->GetProperties()
				);
				$arContract["PROPERTIES"]["PAYMENT"]["VALUE"][] = $paymentID;
				$propertyValues = array(
					"PAYMENT" => $arContract["PROPERTIES"]["PAYMENT"]["VALUE"]
				);
				CIBlockElement::SetPropertyValuesEx($contractId, $iblockContract["ID"], $propertyValues);
				// Элемент успешно создан
				$response = array(
					"success" => true,
					"message" => "Платеж успешно добавлен",
				);
			} else {
				// Ошибка создания элемента
				$response = array(
					"success" => false,
					"message" => "Ошибка при добавлении платежа",
				);
			}
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
