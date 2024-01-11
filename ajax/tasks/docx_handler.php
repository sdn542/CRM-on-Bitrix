<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
CModule::IncludeModule("iblock");

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$taskId = sanitizeInput($_POST["task_id"]);
	if (isset($taskId)) {
		$resTask = CIBlockElement::GetByID($taskId)->GetNextElement();
		$arTask = array(
			"FIELDS" => $resTask->GetFields(),
			"PROPERTIES" => $resTask->GetProperties()
		);
		if(!empty($arTask["PROPERTIES"]["TEMPLATE"]["VALUE"])) {
			$templatePath = ($_SERVER["DOCUMENT_ROOT"] . CFile::GetPath($arTask["PROPERTIES"]["TEMPLATE"]["VALUE"]));
		} else {
			$iblockTemplates = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'templates-docs'], true)->Fetch();
			$arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_TEMPLATE_DOC");
			$arFilter = Array("IBLOCK_ID"=>$iblockTemplates["ID"], "PROPERTY_TYPE_TASK_VALUE" => $arTask["PROPERTIES"]["TYPE_TASK"]["VALUE"]);
			$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
			if($ob = $res->GetNext()) {
				$templatePath = ($_SERVER["DOCUMENT_ROOT"] . CFile::GetPath($ob["PROPERTY_TEMPLATE_DOC_VALUE"]));
			}
		}
		if(!empty($templatePath)) {
			$arLawyer = CUser::GetByID($arTask["PROPERTIES"]["LAWYER"]["VALUE"])->Fetch();
			$arClient = CUser::GetByID($arTask["PROPERTIES"]["CLIENT"]["VALUE"])->Fetch();
			$resContract = CIBlockElement::GetByID($arTask["PROPERTIES"]["CONTRACT"]["VALUE"])->GetNextElement();
			$arContract = array(
				"FIELDS" => $resContract->GetFields(),
				"PROPERTIES" => $resContract->GetProperties()
			);
			if(!empty($arContract["PROPERTIES"]["PAYMENT"]["VALUE"]))
			{
				$table = new Table();
				$table->addRow();
				$table->addCell()->addText("Дата");
				$table->addCell()->addText("Сумма платежа");
				$table->addCell()->addText("Статус оплаты услуги");
				$table->addCell()->addText("Почтовые расходы");
				$table->addCell()->addText("Статус оплаты почты");
				foreach ($arContract["PROPERTIES"]["PAYMENT"]["VALUE"] as $payment) {
					$resPayment = CIBlockElement::GetByID($payment)->GetNextElement();
					$arPayment = array(
						"FIELDS" => $resPayment->GetFields(),
						"PROPERTIES" => $resPayment->GetProperties()
					);
					$table->addRow();
					$table->addCell()->addText($arPayment["PROPERTIES"]["DATE"]["VALUE"]);
					$table->addCell()->addText($arPayment["PROPERTIES"]["SUM"]["VALUE"]);
					$table->addCell()->addText($arPayment["PROPERTIES"]["STATUS_SERVICE"]["VALUE"]);
					$table->addCell()->addText($arPayment["PROPERTIES"]["POSTAGE"]["VALUE"]);
					$table->addCell()->addText($arPayment["PROPERTIES"]["STATUS_POST"]["VALUE"]);
				}
			}
			$phpWord = new TemplateProcessor($templatePath);
			$arVariables = array(
				"\${tasktype}" => $arTask["PROPERTIES"]["TYPE_TASK"]["VALUE"],
				"\${number}" => $arContract["PROPERTIES"]["NUMBER"]["VALUE"],
				"\${lawyer}" => $arLawyer["LAST_NAME"] . " " . $arLawyer["NAME"] . (!empty($arLawyer["SECOND_NAME"]) ? " " . $arLawyer["SECOND_NAME"] : ""),
				"\${client}" => $arClient["LAST_NAME"] . " " . $arClient["NAME"] . (!empty($arClient["SECOND_NAME"]) ? " " . $arClient["SECOND_NAME"] : ""),
				"\${date}" => $arContract["FIELDS"]["ACTIVE_FROM"],
				"\${amount}" => $arContract["PROPERTIES"]["CONTRACT_AMOUNT"]["VALUE"],
				"\${address}" => $arClient["UF_RESIDENCE_ADDRESS"],
				"\${clientaddress}" => $arClient["UF_RESIDENCE_ADDRESS"],
				"\${passportserires}" => $arClient["UF_SERIES"],
				"\${passportnumber}" => $arClient["UF_NUMBER"],
				"\${police}" => $arClient["UF_ISSUING_AUTHORITY"],
				"\${passportdate}" => $arClient["UF_DATE_ISSUED"],
			);

			foreach ($arVariables as $variable => $value) {
				$phpWord->setValue($variable, $value);
			}

			if(!empty($table))
			{
				$phpWord->setComplexBlock("\${schedule}", $table);
			}

			$tempFilePath = (tempnam(sys_get_temp_dir(), 'task_').'.docx');
			$phpWord->saveAs($tempFilePath);

			$arFile = CFile::MakeFileArray($tempFilePath);
			$arFile["MODULE_ID"] = "iblock";
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
					"CHANGED" => "Документ",
					"BEFORE" => "Ничего",
					"AFTER" => $arFile["NAME"]
				)
			);
			$elHistory->Add($arHistory);
			CIBlockElement::SetPropertyValueCode($taskId, "DOC", Array("VALUE"=>$arFile, "DESCRIPTION" => date('d.m.Y')));

			unlink($tempFilePath);
			$response = array(
				"success" => true,
				"message" => "Документы успешно сформированы",
			);
		} else {
			$response = array(
				"success" => false,
				"message" => "Отсутствует шаблон",
			);
		}
		if(!empty($arTask["PROPERTIES"]["ADDRESSEES"]["VALUE"]))
		{
			foreach ($arTask["PROPERTIES"]["ADDRESSEES"]["VALUE"] as $addressed) {
				$resAddressed = CIBlockElement::GetByID($addressed)->GetNextElement();
				$arAddressed = array(
					"FIELDS" => $resAddressed->GetFields(),
					"PROPERTIES" => $resAddressed->GetProperties()
				);
				if(!empty($arAddressed["PROPERTIES"]["CREDITOR"]["VALUE"]))
				{
					$resCreditor = CIBlockElement::GetByID($arAddressed["PROPERTIES"]["CREDITOR"]["VALUE"])->GetNextElement();
					$arCreditor = array(
						"FIELDS" => $resCreditor->GetFields(),
						"PROPERTIES" => $resCreditor->GetProperties()
					);
					if(!empty($arCreditor["PROPERTIES"]["TEMPLATE"]["VALUE"]))
					{
						$templatePath = ($_SERVER["DOCUMENT_ROOT"] . CFile::GetPath($arCreditor["PROPERTIES"]["TEMPLATE"]["VALUE"]));
						$phpWord = new TemplateProcessor($templatePath);
						$arVariables = array(
							"\${address}" => $arCreditor["PROPERTIES"]["ADDRESS"]["VALUE"],
							"\${name}" => $arCreditor["FIELDS"]["NAME"],
//							"\${number}" => "",
//							"\${date}" => ""
						);

						foreach ($arVariables as $variable => $value) {
							$phpWord->setValue($variable, $value);
						}

						$tempFilePath = (tempnam(sys_get_temp_dir(), 'creditor_').'.docx');
						$phpWord->saveAs($tempFilePath);

						$arFile = CFile::MakeFileArray($tempFilePath);
						$arFile["MODULE_ID"] = "iblock";
						CIBlockElement::SetPropertyValueCode($arAddressed["FIELDS"]["ID"], "DOC", Array("VALUE"=>$arFile, "DESCRIPTION" => date('d.m.Y')));

						unlink($tempFilePath);
					}
				}
			}
		}
	} else {
		$response = array(
			"success" => false,
			"message" => "Отсутствуют необходимые параметры",
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
