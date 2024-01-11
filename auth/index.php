<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");
if ($USER->IsAuthorized()) {
	LocalRedirect("/");
}
?>
	<div class="row" style="height: 100vh">
		<div class="col-5 d-flex align-items-center justify-content-center" style="background-image: url('<?= CFile::GetPath($headerData["BACKGROUND_AUTH"]["VALUE"]) ?>')">
			<? if (!empty($headerData["LOGO"]["VALUE"])): ?>
				<img src="<?= CFile::GetPath($headerData["LOGO"]["VALUE"]) ?>">
			<? endif; ?>
		</div>
		<div class="col-6">
			<? $APPLICATION->IncludeComponent(
				"bitrix:system.auth.form",
				"auth",
				array(
					"FORGOT_PASSWORD_URL" => "",
					"PROFILE_URL" => "",
					"REGISTER_URL" => "",
					"SHOW_ERRORS" => "Y"
				)
			); ?>
		</div>
	</div>
	<style>
		header {
			display: none;
		}
	</style>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>