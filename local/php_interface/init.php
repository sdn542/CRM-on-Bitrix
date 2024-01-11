<?
require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/wsrubi.smtp/classes/general/wsrubismtp.php");

function checkAccessElement($elementId)
{
	global $USER;

	$user = $USER->GetID();
	$currentGroup = CUser::GetUserGroup($user);

	$resElement = CIBlockElement::GetByID($elementId)->GetNextElement();
	$arElement = array(
		"FIELDS" => $resElement->GetFields(),
		"PROPERTIES" => $resElement->GetProperties()
	);
	$responsibleGroup = CUser::GetUserGroup($arElement["PROPERTIES"]["LAWYER"]["VALUE"]);

	$arrGroups = array();
	$groupCode = "director|chief_lawyers|senior_lawyers|junior_lawyers";
	$rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", array("STRING_ID" => $groupCode));
	while ($arGroups = $rsGroup->Fetch()) {
		$arrGroups[] = $arGroups["ID"];
	}

	$userIsExecutor = ($user == $arElement["PROPERTIES"]["LAWYER"]["VALUE"]);

	$userGroupIndex = false;
	foreach ($arrGroups as $index => $value) {
		if (in_array($value, $currentGroup)) {
			$userGroupIndex = $index;
			break;
		}
	}

	$executorGroupIndex = false;
	foreach ($arrGroups as $index => $value) {
		if (in_array($value, $responsibleGroup)) {
			$executorGroupIndex = $index;
			break;
		}
	}

	if ($userIsExecutor || ($userGroupIndex !== false && $executorGroupIndex !== false && $userGroupIndex < $executorGroupIndex)) {
		return true;
	} else {
		return false;
	}
}

function checkAccess($minGroupIndex)
{
	global $USER;

	$user = $USER->GetID();
	$currentGroup = CUser::GetUserGroup($user);

	$arrGroups = array();
	$groupCode = "director|chief_lawyers|senior_lawyers|junior_lawyers";
	$rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", array("STRING_ID" => $groupCode));
	while ($arGroups = $rsGroup->Fetch()) {
		$arrGroups[] = $arGroups["ID"];
	}
	$userGroupIndex = false;
	foreach ($arrGroups as $index => $value) {
		if (in_array($value, $currentGroup)) {
			$userGroupIndex = $index;
			break;
		}
	}
	if ($userGroupIndex !== false && $minGroupIndex !== false && $userGroupIndex <= $minGroupIndex) {
		return true;
	} else {
		return false;
	}
}
