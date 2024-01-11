<?php
$groupIDs = array();
$rsGroups = CGroup::GetList($by = 'ID', $order = 'ASC', array('STRING_ID' => 'junior_lawyers|chief_lawyers'));
while ($group = $rsGroups->Fetch()) {
	$groupIDs[] = $group['ID'];
}
$lawyersArr = array();
$rsUsers = CUser::GetList($by = 'ID', $order = 'ASC', ['GROUPS_ID' => $groupIDs]);
while ($user = $rsUsers->GetNext()) {
	$lawyersArr[$user['ID']] = $user["LAST_NAME"] . " " . $user["NAME"] . (!empty($user["SECOND_NAME"]) ? " " . $user["SECOND_NAME"] : "");
}
foreach($arResult["ITEMS"] as $arItem)
{
	$arItem["PROPERTIES"]["LAWYER"]["VALUE"] = $lawyersArr[$arItem["PROPERTIES"]["LAWYER"]["VALUE"]];
}