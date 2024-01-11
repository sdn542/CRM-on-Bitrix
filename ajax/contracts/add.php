<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;

CModule::IncludeModule("main");

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

function generateRandomPassword($length = 10)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$password = '';

	$password .= $characters[random_int(0, 9)];

	for ($i = 1; $i < $length; $i++) {
		$password .= $characters[random_int(0, strlen($characters) - 1)];
	}

	$password = str_shuffle($password);

	return $password;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$password = generateRandomPassword();

	// Создание пользователя
	$newUserID = 0; // Идентификатор созданного пользователя

	$newUser = new CUser;
	$arClient = array(
		"PASSWORD" => $password,
		"CONFIRM_PASSWORD" => $password,
		"NAME" => sanitizeInput($_POST["first_name"]),
		"LAST_NAME" => sanitizeInput($_POST["last_name"]),
		"SECOND_NAME" => sanitizeInput($_POST["second_name"]),
		"EMAIL" => sanitizeInput($_POST["email"]),
		"LOGIN" => sanitizeInput($_POST["email"]),
		"GROUP_ID" => array(5),
		"PERSONAL_BIRTHDAY" => FormatDate("d.m.Y", MakeTimeStamp(sanitizeInput($_POST["date_birth"]))),
		"PERSONAL_PHONE" => sanitizeInput($_POST["phone"]),
		"UF_PLACE_BIRTH" => sanitizeInput($_POST["place_birth"]),
		"UF_EMPLOYMENT_INCOME" => sanitizeInput($_POST["employment_and_income"]),
		"UF_OWNERSHIP" => sanitizeInput($_POST["property_presence"]),
		"UF_THREE_YEAR_TRANSACTIONS" => sanitizeInput($_POST["transactions_three_year_period"]),
		"UF_CUSTOMER_EXPECTED_OUTCOME" => sanitizeInput($_POST["expected_client_result"]),
		"UF_SERIES" => sanitizeInput($_POST["series"]),
		"UF_DATE_ISSUED" => FormatDate("d.m.Y", MakeTimeStamp(sanitizeInput($_POST["date_issued"]))),
		"UF_ISSUING_AUTHORITY" => sanitizeInput($_POST["issued_by"]),
		"UF_INN" => sanitizeInput($_POST["inn"]),
		"UF_SNILS" => sanitizeInput($_POST["snils"]),
		"UF_RESIDENCE_ADDRESS" => sanitizeInput($_POST["residence_address"]),
		"UF_NUMBER" => sanitizeInput($_POST["number"]),
		"UF_COSIGNERS_COBORROWERS" => sanitizeInput($_POST["guarantors_cobligors"]),
		"UF_PREEXISTING_DELINQUENCIES" => sanitizeInput($_POST["delinquencies_at_contract"]),
		"UF_MFOS_DEBTS" => sanitizeInput($_POST["overdue_mfo"]),
		"UF_UTILITY_ARREARS" => sanitizeInput($_POST["overdue_housing_services"]),
		"UF_CREDITOR_COUNT" => sanitizeInput($_POST["creditors_number"]),
		"UF_JUDGMENT_DECREES" => sanitizeInput($_POST["judicial_acts"]),
		"UF_COLLATERAL_VALUE" => sanitizeInput($_POST["total_encumbrance_amount"]),
		"UF_EXECUTION_PROCEEDINGS" => sanitizeInput($_POST["enforcement_proceedings"]),
		"UF_TAX_OBLIGATIONS" => sanitizeInput($_POST["tax_liabilities"]),
		"UF_NOTES" => sanitizeInput($_POST["notes"]),
		"UF_CHANGE_HISTORY" => sanitizeInput($_POST["change_history"]),
		"UF_WORK_PLAN" => sanitizeInput($_POST["work_plan"]),
	);

	$newUserID = $newUser->Add($arClient);

	if ($newUserID > 0) {
		$arEventFields = array(
			"EMAIL" => $arClient["EMAIL"],
			"PASSWORD" => $arClient["PASSWORD"],
		);
		CEvent::SendImmediate("USER_PASS", "s1", $arEventFields, "N", "", "", true);
		if ($_POST["payment"]) {
			$iblockPayment = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'payments'], true)->Fetch();
			$iblockPaymentId = $iblockPayment['ID'];
			$arPayments = array();
			foreach ($_POST["payment"] as $payment) {
				$el = new CIBlockElement;
				$arPayment = array(
					"IBLOCK_ID" => $iblockPaymentId,
					"NAME" => "Платеж",
					"ACTIVE" => "Y",
					"PROPERTY_VALUES" => array(
						"DATE" => $payment["date"],
						"SUM" => $payment["sum-payment"],
						"POSTAGE" => $payment["sum-pochta"]
					)
				);
				$arPayments[] = $el->Add($arPayment);
			}
		}
		// Пользователь успешно создан
		$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'contracts'], true)->Fetch();
		if ($iblock) {
			$iblockId = $iblock['ID'];
			$el = new CIBlockElement;
			$property_enums = CIBlockPropertyEnum::GetList(
				Array("ID"=>"ASC", "SORT"=>"ASC"),
				Array("IBLOCK_ID"=>$iblockId, "CODE"=>"TYPE", "ID" => sanitizeInput($_POST["type_contract"]))
			);
			if ($enum_fields = $property_enums->GetNext()) {
				$xml_id_type = $enum_fields["XML_ID"];
			}
			$filter = [
				'IBLOCK_ID' => $iblockId,
				'ACTIVE' => "Y",
				'PROPERTY_TYPE' => sanitizeInput($_POST["type_contract"]),
			];
			$select = ['ID', 'NAME', 'PROPERTY_NUMBER'];
			$order = ['PROPERTY_NUMBER' => 'DESC'];
			$contractIterator = CIBlockElement::GetList($order, $filter, false, false, $select);
			if ($lastContract = $contractIterator->Fetch()) {
				$lastNumber = explode('-', $lastContract['PROPERTY_NUMBER_VALUE'])[0];
				$lastNumber = (int)$lastNumber + 1;
				$newNumber = (string)$lastNumber . "-" . $xml_id_type . "-" . date("Y");
			} else {
				$newNumber = "1000-" . $xml_id_type . "-" . date("Y");
			}
			$arContract = array(
				"MODIFIED_BY" => $USER->GetID(),
				"IBLOCK_ID" => $iblockId,
				"NAME" => $newNumber,
				"ACTIVE" => "Y",
				"ACTIVE_FROM" => FormatDate("d.m.Y", strtotime(sanitizeInput($_POST["date_contract"]))),
				"PROPERTY_VALUES" => array(
					"TYPE" => sanitizeInput($_POST["type_contract"]),
					"CONTRACT_AMOUNT" => sanitizeInput($_POST["contract_amount"]),
					"CONTRACT_TERM" => sanitizeInput($_POST["contract_term"]),
					"GRACE_PERIOD" => sanitizeInput($_POST["deferment"]),
					"ADVANCE_PAYMENT" => sanitizeInput($_POST["prepayment"]),
					"NUMBER" => $newNumber,
					"CLIENT" => $newUserID,
					"CREATOR" => sanitizeInput($_POST["contract_signer"]),
					"LAWYER" => sanitizeInput($_POST["responsible"]),
					"PAYMENT" => $arPayments
				)
			);
			$propertyEnumsStatus = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblock["ID"], "CODE" => "STATUS_CHECK", "XML_ID" => "PENDING_APPROVAL"));
			if($enumFieldsStatus = $propertyEnumsStatus->GetNext())
			{
				$arContract["PROPERTY_VALUES"]["STATUS_CHECK"] = $enumFieldsStatus["ID"];
			}
			if ($contractID = $el->Add($arContract)) {
				if ($_POST["creditor"]) {
					$iblockCreditor = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'contract_creditors'], true)->Fetch();
					$iblockCreditorId = $iblockCreditor['ID'];
					$arCreditors = array();
					foreach ($_POST["creditor"] as $creditor) {
						$el = new CIBlockElement;
						$arCreditor = array(
							"IBLOCK_ID" => $iblockCreditorId,
							"NAME" => "Кредитор",
							"ACTIVE" => "Y",
							"PROPERTY_VALUES" => array(
								"DATE" => $creditor["date"],
								"NUMBER" => $creditor["number"],
								"CREDITOR" => $creditor["id"],
								"CONTRACT" => $contractID
							)
						);
						if($creditorID = $el->Add($arCreditor)){
							$arCreditors[] = $creditor["id"];
						}
					}
					$propertyValues = array(
						"CREDITOR" => $arCreditors
					);
					CIBlockElement::SetPropertyValuesEx($contractID, $iblockId, $propertyValues);
				}
				$resContract = CIBlockElement::GetByID($contractID)->GetNextElement();
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
				if (extension_loaded('zip')) {
					$iblockTemplates = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'templates-docs'], true)->Fetch();
					$property_enums = CIBlockPropertyEnum::GetList(
						array("ID" => "ASC", "SORT" => "ASC"),
						array("IBLOCK_ID" => $iblockTemplates["ID"], "CODE" => "TYPE_CONTRACT", "XML_ID" => $arContract["PROPERTIES"]["TYPE"]["VALUE_XML_ID"])
					);
					if ($enum_fields = $property_enums->GetNext()) {
						$arSelect = array("ID", "IBLOCK_ID");
						$arFilter = array("IBLOCK_ID" => $iblockTemplates["ID"], "PROPERTY_TYPE_CONTRACT" => $enum_fields["ID"]);
						$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
						while ($ob = $res->GetNextElement()) {
							$arTemplate = array(
								"FIELDS" => $ob->GetFields(),
								"PROPERTIES" => $ob->GetProperties()
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

							$tempFilePath = (tempnam(sys_get_temp_dir(), 'contract_') . '.docx');
							$phpWord->saveAs($tempFilePath);

							$arFile = CFile::MakeFileArray($tempFilePath);
							$arFile["MODULE_ID"] = "iblock";
							CIBlockElement::SetPropertyValueCode($contractID, "DOCS_BY_CLIENT", array("VALUE" => $arFile, "DESCRIPTION" => date('d.m.Y')));

							unlink($tempFilePath);
						}
					}
				}
				$groupCode = "chief_lawyers";
				$rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", array("STRING_ID" => $groupCode));
				$arGroup = $rsGroup->Fetch();
				$groupId = $arGroup["ID"];
				if ($groupId) {
					$userFilter = array("GROUPS_ID" => array($groupId));
					$userParams = array("SELECT" => array("ID", "NAME", "EMAIL"));
					$rsUsers = CUser::GetList($by = "ID", $order = "asc", $userFilter, $userParams);
					$arUser = $rsUsers->Fetch();
				}
				$iblockTasks = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'tasks'], true)->Fetch();
				$iblockTasksId = $iblockTasks['ID'];
				$elTasks = new CIBlockElement;
				$arTask = array(
					"IBLOCK_ID" => $iblockTasksId,
					"NAME" => "Задача",
					"ACTIVE" => "Y",
					"ACTIVE_FROM" => FormatDate("d.m.Y", MakeTimeStamp($arContract["ACTIVE_FROM"])),
					"PROPERTY_VALUES" => array(
						"CONTRACT" => $contractID,
						"LAWYER" => $arUser["ID"],
						"TYPE_TASK" => "Проверка договора",
					)
				);
				$taskID = $elTasks->Add($arTask);
				// Элемент успешно создан
				$response = array(
					"success" => true,
					"message" => "Пользователь и договор успешно созданы",
				);
			} else {
				// Ошибка создания элемента
				$response = array(
					"success" => false,
					"message" => "Ошибка при создании договора: " . $el->LAST_ERROR,
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
		// Ошибка создания пользователя
		$response = array(
			"success" => false,
			"message" => "Ошибка при создании пользователя: " . $newUser->LAST_ERROR,
		);
	}
} else {
	$response = array(
		"success" => false,
		"message" => "Недопустимый метод запроса",
	);
}

//echo Json::encode($response, JSON_UNESCAPED_UNICODE);
?>
<script>
	window.location.href = '/lawyer/contract/<?= $contractID ?>'
</script>
