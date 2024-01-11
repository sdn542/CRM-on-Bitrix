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
	$taskId = sanitizeInput($_POST["task_id"]);
	$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'tasks'], true)->Fetch();
	if ($iblock && isset($taskId)) {
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

		$enumFinalStatusTask = CIBlockPropertyEnum::GetList(
			array(),
			array('PROPERTY_ID' => $propertyIdStatusTask, 'VALUE' => "Выполнено")
		);
		if ($enumStatusTask = $enumFinalStatusTask->Fetch()) {
			$enumIdFinalStatusTask = $enumStatusTask['ID'];
		}

		$arTaskChange = array(
			"MODIFIED_BY" => $USER->GetID(),
			"IBLOCK_ID" => $iblockId,
			"PREVIEW_TEXT" => sanitizeInput($_POST["description"])
		);

		$propertyValues = array(
			"INTERIM_DATE" => sanitizeInput($_POST["interim_period"]),
			"TYPE_POST_ITEM" => sanitizeInput($_POST["type_post_item"]),
			"STATUS_TASK" => sanitizeInput($_POST["status_task"]),
			"STATUS_POST" => sanitizeInput($_POST["status_post"]),
			"LAWYER" => sanitizeInput($_POST["lawyer"]),
			"SHOW_IN_LK" => sanitizeInput($_POST["show_in_lk"]) == "Y" ? $enumIdShowInLk : false,
		);

        if(!empty($_POST["type"]))
        {
            $propertyValues["TYPE_TASK"] = sanitizeInput($_POST["type"]);
        }

		if(!empty(sanitizeInput($_POST["deadline"])))
		{
			$propertyValues["DEADLINE"] = sanitizeInput($_POST["deadline"]);
		}

		$resTask = CIBlockElement::GetByID($taskId)->GetNextElement();
		$arTask = array(
			"FIELDS" => $resTask->GetFields(),
			"PROPERTIES" => $resTask->GetProperties()
		);

		if(sanitizeInput($_POST["status_task"]) == $enumIdFinalStatusTask && $arTask["PROPERTIES"]["STATUS_TASK"]["VALUE_ENUM_ID"] != $enumIdFinalStatusTask)
		{
			$propertyValues["DATE_SENDING"] = FormatDate("d.m.Y", time());
		}

		if(!empty($arTask["PROPERTIES"]["DATE_SENDING"]["VALUE"])) {
			if(sanitizeInput($_POST["status_task"]) != $enumIdFinalStatusTask)
			{
				$propertyValues["DATE_SENDING"] = "";
			}
		}

		$iblockHistory = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'history'], true)->Fetch();
		$iblockHistoryId = $iblockHistory['ID'];

		foreach ($propertyValues as $key => $value)
		{
			$changed = false;
			$afterValue = $value;
			if($arTask["PROPERTIES"][$key]["PROPERTY_TYPE"] == "S")
			{
				if($key == "LAWYER")
				{
					if($afterValue != $arTask["PROPERTIES"][$key]["VALUE"])
					{
						$arBeforeLawyer = CUser::GetByID($arTask["PROPERTIES"][$key]["VALUE"])->Fetch();
						$arAfterLawyer = CUser::GetByID($afterValue)->Fetch();
						$changed = true;
						$beforeValue = $arBeforeLawyer["LAST_NAME"] . " " . $arBeforeLawyer["NAME"] . (!empty($arBeforeLawyer["SECOND_NAME"]) ? " " . $arBeforeLawyer["SECOND_NAME"] : "");
						$afterValue = $arAfterLawyer["LAST_NAME"] . " " . $arAfterLawyer["NAME"] . (!empty($arAfterLawyer["SECOND_NAME"]) ? " " . $arAfterLawyer["SECOND_NAME"] : "");
					}
				} else {
					$afterValue = strtotime($value);
					if($afterValue != strtotime($arTask["PROPERTIES"][$key]["VALUE"]))
					{
						$changed = true;
						$afterValue = FormatDate("d.m.Y", $afterValue);
					}
				}
			} elseif($arTask["PROPERTIES"][$key]["PROPERTY_TYPE"] == "L")
			{
				if($afterValue != $arTask["PROPERTIES"][$key]["VALUE_ENUM_ID"] && $afterValue != "Выбрать")
				{
					$changed = true;
					$enumValue = CIBlockPropertyEnum::GetList(
						array(),
						array('PROPERTY_ID' => $arTask["PROPERTIES"][$key]["ID"], 'ID' => $afterValue)
					)->Fetch();
					$afterValue = $enumValue["VALUE"];
				}
			}
			elseif($value != $arTask["PROPERTIES"][$key]["VALUE"]) {
				$changed = true;
			}
			if($changed)
			{
				$elHistory = new CIBlockElement;
				$arHistory = array(
					"IBLOCK_ID" => $iblockHistoryId,
					"NAME" => "Изменение задачи $taskId",
					"ACTIVE" => "Y",
					"ACTIVE_FROM" => FormatDate("d.m.Y", time()),
					"PROPERTY_VALUES" => array(
						"DATETIME" => FormatDate("d.m.Y H:m:i", time()),
						"USER" => $USER->GetID(),
						"TASK" => $taskId,
						"CHANGED" => $arTask["PROPERTIES"][$key]["NAME"],
						"BEFORE" => !empty($beforeValue) ? $beforeValue : $arTask["PROPERTIES"][$key]["VALUE"],
						"AFTER" => $afterValue
					)
				);
				$elHistory->Add($arHistory);
			}
		}

		if($arTask["FIELDS"]["PREVIEW_TEXT"] != $arTaskChange["PREVIEW_TEXT"])
		{
			$elHistory = new CIBlockElement;
			$arHistory = array(
				"IBLOCK_ID" => $iblockHistoryId,
				"NAME" => "Изменение задачи $taskId",
				"ACTIVE" => "Y",
				"ACTIVE_FROM" => FormatDate("d.m.Y", time()),
				"PROPERTY_VALUES" => array(
					"DATETIME" => FormatDate("d.m.Y H:m:i", time()),
					"USER" => $USER->GetID(),
					"TASK" => $taskId,
					"CHANGED" => "Описание",
					"BEFORE" => $arTask["FIELDS"]["PREVIEW_TEXT"],
					"AFTER" => $arTaskChange["PREVIEW_TEXT"]
				)
			);
			$elHistory->Add($arHistory);
		}

		CIBlockElement::SetPropertyValuesEx($taskId, $iblockId, $propertyValues);
		if ($taskID = $el->Update($taskId, $arTaskChange)) {
			// Элемент успешно создан
			$response = array(
				"success" => true,
				"message" => "Задача успешно сохранена",
			);
		} else {
			// Ошибка создания элемента
			$response = array(
				"success" => false,
				"message" => "Ошибка при сохранении задачи: " . $el->LAST_ERROR,
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
	history.back()
</script>

