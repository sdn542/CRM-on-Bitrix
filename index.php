<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

if($USER->IsAuthorized())
{
	if (!empty($arUser)) {
		$redirectUrls = array(
			5 => "/client/lk/",
			6 => "/lawyer/",
			7 => "/lawyer/",
			9 => "/lawyer/",
			10 => "/lawyer/"
		);

		$arUserGroups = CUser::GetUserGroup($arUser['ID']);

		$redirectGroupId = null;
		foreach ($arUserGroups as $userGroupId) {
			if (isset($redirectUrls[$userGroupId])) {
				$redirectGroupId = $userGroupId;
				break;
			}
		}

		if ($redirectGroupId !== null) {
			$redirectUrl = $redirectUrls[$redirectGroupId];
			LocalRedirect($redirectUrl);
		}
	}
} else {
	LocalRedirect("/auth/");
}


require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>
