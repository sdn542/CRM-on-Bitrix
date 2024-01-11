<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;

CModule::IncludeModule("main");
CModule::IncludeModule('iblock');

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'tasks'], true)->Fetch();
		if ($iblock) {
			$iblockId = $iblock['ID'];
			$el = new CIBlockElement;

			$propertyIdStatusTask = CIBlockProperty::GetList(
				array(),
				array('IBLOCK_ID' => $iblockId, 'CODE' => "STATUS_TASK")
			)->Fetch()['ID'];

			$enumIdStatusTask = false;
			$enumResultStatusTask = CIBlockPropertyEnum::GetList(
				array(),
				array('PROPERTY_ID' => $propertyIdStatusTask, 'VALUE' => "Новая")
			);
			if ($enumStatusTask = $enumResultStatusTask->Fetch()) {
				$enumIdStatusTask = $enumStatusTask['ID'];
			}

			$propertyIdNonStandart = CIBlockProperty::GetList(
				array(),
				array('IBLOCK_ID' => $iblockId, 'CODE' => "NON_STANDART")
			)->Fetch()['ID'];

			$enumIdNonStandart = false;
			$enumResultNonStandart = CIBlockPropertyEnum::GetList(
				array(),
				array('PROPERTY_ID' => $propertyIdNonStandart, 'VALUE' => "Y")
			);
			if ($enumNonStandart = $enumResultNonStandart->Fetch()) {
				$enumIdNonStandart = $enumNonStandart['ID'];
			}

			$propertyIdShowInLk = CIBlockProperty::GetList(
				array(),
				array('IBLOCK_ID' => $iblockId, 'CODE' => "SHOW_IN_LK")
			)->Fetch()['ID'];

			$enumIdShowInLk = false;
			$enumResultShowInLk = CIBlockPropertyEnum::GetList(
				array(),
				array('PROPERTY_ID' => $propertyIdShowInLk, 'VALUE' => "Y")
			);
			if ($enumShowInLk = $enumResultShowInLk->Fetch()) {
				$enumIdShowInLk = $enumShowInLk['ID'];
			}

			if(!empty($_POST["creditor"]))
			{
				$arCreditors = $_POST["creditor"];
				$iblockDepartures = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'departures'], true)->Fetch();
				$iblockDeparturesId = $iblockDepartures['ID'];
				$arAddresats = array();
				$elDepartures = new CIBlockElement;
				foreach ($arCreditors as $index => $creditor)
				{
					$arAddresat = array(
						"IBLOCK_ID" => $iblockDeparturesId,
						"NAME" => "Отправление",
						"ACTIVE" => "Y",
						"ACTIVE_FROM" => FormatDate("d.m.Y", time()),
						"PROPERTY_VALUES" => array(
							"CREDITOR" => $creditor["id"]
						)
					);

					if(!empty($creditor["track"]))
					{
						$arAddresat["PROPERTY_VALUES"]["TRACK_NUMBER"] = $creditor["track"];
					}

					if(!empty($_FILES["creditor"]["name"][$index]["doc"])){
						$arDoc = array(
							"name" => $_FILES["creditor"]["name"][$index]["doc"],
							"full_path" => $_FILES["creditor"]["full_path"][$index]["doc"],
							"type" => $_FILES["creditor"]["type"][$index]["doc"],
							"tmp_name" => $_FILES["creditor"]["tmp_name"][$index]["doc"],
							"error" => $_FILES["creditor"]["error"][$index]["doc"],
							"size" => $_FILES["creditor"]["size"][$index]["doc"]
						);
						$arAddresat["PROPERTY_VALUES"]["DOC"] = array("VALUE" => $arDoc);
					}

					if(!empty($_FILES["creditor"]["name"][$index]["response"])){
						$arResponse = array(
							"name" => $_FILES["creditor"]["name"][$index]["response"],
							"full_path" => $_FILES["creditor"]["full_path"][$index]["response"],
							"type" => $_FILES["creditor"]["type"][$index]["response"],
							"tmp_name" => $_FILES["creditor"]["tmp_name"][$index]["response"],
							"error" => $_FILES["creditor"]["error"][$index]["response"],
							"size" => $_FILES["creditor"]["size"][$index]["response"]
						);
						$arAddresat["PROPERTY_VALUES"]["RESPONSE"] = array("VALUE" => $arResponse);
					}

					if ($addresatID = $elDepartures->Add($arAddresat)) {
						$arAddresats[] = $addresatID;
					}
				}
			}

			$arTask = array(
				"MODIFIED_BY" => $USER->GetID(),
				"IBLOCK_ID" => $iblockId,
				"NAME" => "Задача",
				"ACTIVE" => "Y",
				"ACTIVE_FROM" => FormatDate("d.m.Y", MakeTimeStamp(sanitizeInput($_POST["active_from"]))),
				"PREVIEW_TEXT" => sanitizeInput($_POST["description"]),
				"PROPERTY_VALUES" => array(
					"CONTRACT" => sanitizeInput($_POST["contract"]),
					"INTERIM_DATE" => sanitizeInput($_POST["interim_period"]),
					"TYPE_POST_ITEM" => sanitizeInput($_POST["type_post_item"]),
					"STATUS_TASK" => $enumIdStatusTask,
					"TYPE_TASK" => sanitizeInput($_POST["type"]),
					"NON_STANDART" => sanitizeInput($_POST["non_standart"]) == "Y" ? $enumIdNonStandart : false,
					"CLIENT" => sanitizeInput($_POST["client"]),
					"LAWYER" => sanitizeInput($_POST["lawyer"]),
					"CREATOR" => $USER->GetID(),
					"SHOW_IN_LK" => sanitizeInput($_POST["show_in_lk"]) == "Y" ? $enumIdShowInLk : false,
					"DEADLINE" => sanitizeInput($_POST["deadline"]),
					"ADDRESSEES" => $arAddresats
				)
			);

			if ($taskID = $el->Add($arTask)) {
				// Элемент успешно создан
				$response = array(
					"success" => true,
					"message" => "Задача успешно создана",
				);
			} else {
				// Ошибка создания элемента
				$response = array(
					"success" => false,
					"message" => "Ошибка при создании задачи: " . $el->LAST_ERROR,
				);
			}
		} else {
			// Ошибка инфоблока
			$response = array(
				"success" => false,
				"message" => "Ошибка инфоблок не найден",
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
    window.location.href = '/lawyer/tasks/<?= $taskID ?>'
</script>
