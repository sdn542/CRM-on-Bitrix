<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");
?>
<div class="d-flex flex-column align-items-center">
	<span class="not-found">Упс...такой страницы не существует</span>
	<span class="not-found-404">404</span>
	<a style="width: 257px" class="btn btn-primary" href="/">Вернутся на главную страницу</a>
</div>
<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>