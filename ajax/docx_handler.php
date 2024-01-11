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
	$contractId = sanitizeInput($_POST["contract_id"]);
	$templateId = sanitizeInput($_POST["template_id"]);
	if (isset($contractId) && isset($templateId)) {
		$resContract = CIBlockElement::GetByID($contractId)->GetNextElement();
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
		$arUser = CUser::GetByID($arContract["PROPERTIES"]["LAWYER"]["VALUE"])->Fetch();
		$arClient = CUser::GetByID($arContract["PROPERTIES"]["CLIENT"]["VALUE"])->Fetch();
		$resTemplate = CIBlockElement::GetByID($templateId)->GetNextElement();
		$arTemplate = array(
			"FIELDS" => $resTemplate->GetFields(),
			"PROPERTIES" => $resTemplate->GetProperties()
		);

		$phpWord = new TemplateProcessor($_SERVER["DOCUMENT_ROOT"] . CFile::GetPath($arTemplate["PROPERTIES"]["TEMPLATE_DOC"]["VALUE"]));
		$arVariables = array(
			"\${number}" => $arContract["PROPERTIES"]["NUMBER"]["VALUE"],
			"\${lawyer}" => $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : ""),
			"\${client}" => $arClient["LAST_NAME"] . " " . $arClient["NAME"] . (!empty($arClient["SECOND_NAME"]) ? " " . $arClient["SECOND_NAME"] : ""),
			"\${date}" => $arContract["ACTIVE_FROM"],
			"\${amount}" => $arContract["PROPERTIES"]["CONTRACT_AMOUNT"]["VALUE"],
			"\${address}" => $arClient["UF_RESIDENCE_ADDRESS"],
			"\${passportserires}" => $arClient["UF_SERIES"],
			"\${passportnumber}" => $arClient["UF_NUMBER"],
		);

		foreach ($arVariables as $variable => $value) {
			$phpWord->setValue($variable, $value);
		}

		$phpWord->setComplexBlock("\${schedule}", $table);

		$tempFilePath = tempnam(sys_get_temp_dir(), 'contract_');
		$phpWord->saveAs($tempFilePath);

		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment; filename="contract.docx"');
		header('Content-Length: ' . filesize($tempFilePath));

		readfile($tempFilePath);

		unlink($tempFilePath);

		exit();
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

echo Json::encode($response, JSON_UNESCAPED_UNICODE);
