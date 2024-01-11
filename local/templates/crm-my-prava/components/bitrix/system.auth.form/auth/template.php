<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init();
?>

<div class="bx-system-auth-form">
	<h1 class="auth-title">
		<?=GetMessage("AUTH_TITLE")?>
	</h1>
	<h2 class="auth-subtitle">
		<?=GetMessage("AUTH_MESSAGE")?>
	</h2>
<?
if ($arResult['SHOW_ERRORS'] === 'Y' && $arResult['ERROR'] && !empty($arResult['ERROR_MESSAGE']))
{
	ShowMessage($arResult['ERROR_MESSAGE']);
}
?>

<?if($arResult["FORM_TYPE"] == "login"):?>

<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" class="row g-3">
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
	<div class="col-12">
		<label class="form-label" for="USER_LOGIN"><?=GetMessage("AUTH_LOGIN")?>:</label>
		<input class="form-control" type="text" name="USER_LOGIN" maxlength="50" value="" size="17" />
		<script>
			BX.ready(function() {
				var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
				if (loginCookie) {
					var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
					var loginInput = form.elements["USER_LOGIN"];
					loginInput.value = loginCookie;
				}
			});
		</script>
	</div>
	<div class="col-12">
		<label class="form-label" for="USER_LOGIN"><?=GetMessage("AUTH_PASSWORD")?>:</label>
		<input class="form-control" type="password" name="USER_PASSWORD" maxlength="255" size="17" autocomplete="off" />
		<p style="color: #64748B; font-weight: 600;"><?=GetMessage("AUTH_PASSWORD_HINT")?></p>
	</div>
	<div class="col-12 d-flex justify-content-between">
		<div class="form-check">
			<input class="form-check-input" type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" />
			<label class="form-check-label" for="USER_REMEMBER_frm" title="<?=GetMessage("AUTH_REMEMBER_ME")?>"><?echo GetMessage("AUTH_REMEMBER_SHORT")?></label>
		</div>
		<noindex><a data-bs-toggle="modal" data-bs-target="#resetPassword" href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex>
	</div>
	<div class="col-12">
		<input class="btn btn-primary" type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" />
	</div>
	<div class="col-12">
		<div class="form-check">
			<input class="form-check-input" type="checkbox" id="USER_AGREEMENT" name="USER_AGREEMENT" required/>
			<label class="form-check-label" for="USER_AGREEMENT" title="<?=GetMessage("AUTH_AGREEMENT")?>">
				<?=GetMessage("AUTH_AGREEMENT")?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
						"AREA_FILE_SHOW" => "file",
						"AREA_FILE_SUFFIX" => "inc",
						"EDIT_TEMPLATE" => "",
						"PATH" => "/include/agreement-link.php"
					)
				);?>
			</label>
		</div>
	</div>
</form>

<?endif?>
</div>
