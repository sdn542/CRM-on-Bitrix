<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
IncludeTemplateLangFile(__FILE__);
CModule::IncludeModule("iblock");
$db_props = CIBlockElement::GetList(array("sort" => "asc"), array("IBLOCK_CODE" => "main-content", "CODE" => "main-content-element"));
if ($obj = $db_props->GetNextElement()) {
	$headerData = $obj->GetProperties();
}

$userIsAuthorized = $USER->IsAuthorized();
if ($userIsAuthorized) {
	$currentUser = CUser::GetByID($USER->GetParam('USER_ID'));
	$arUser = $currentUser->Fetch();
} else {
	if($_SERVER['REQUEST_URI'] != '/auth/index.php') {
		LocalRedirect("/auth/index.php");
	}
}
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
	<head>
		<?$APPLICATION->ShowHead();?>
		<title><?$APPLICATION->ShowTitle(false);?></title>
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" /> 	
		<?
			$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/normalize.css');
			$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap.min.css');
			$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/styles.css');
			$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.min.js');
			$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/bootstrap.min.js');
			$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/inputmask.min.js');
			$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/main.js');
		?>
	</head>
	<body>
		<div id="panel">
			<?$APPLICATION->ShowPanel();?>
		</div>
		<header class="header">
			<div class="container d-flex align-items-center justify-content-between">
				<div class="logo">
					<? if ($APPLICATION->GetCurPage(false) != '/'): ?>
						<a href="/" class="logo-wrap">
							<img width="112" height="42" src="<?= CFile::GetPath($headerData["LOGO"]["VALUE"]) ?>" alt="logo">
						</a>
					<? else: ?>
						<div class="logo-wrap">
							<img src="<?= CFile::GetPath($headerData["LOGO"]["VALUE"]) ?>" alt="logo">
						</div>
					<? endif; ?>
				</div>
				<div>
					<? if($userIsAuthorized): ?>
						<div class="d-flex align-items-center position-relative js-user" style="cursor: pointer;">
							<img class="mx-2" src="<?= !empty($arUser["PERSONAL_PHOTO"]) ? CFile::GetPath($arUser["PERSONAL_PHOTO"]) : (SITE_TEMPLATE_PATH . "/images/user/empty-avatar.png") ?>" alt="avatar">
							<p><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></p>
							<a class="logout" href="/?logout=yes&<?= bitrix_sessid_get() ?>"><?= GetMessage("USER_LOGOUT") ?></a>
						</div>
					<? else: ?>
	
					<? endif; ?>
				</div>
			</div>
		</header>
