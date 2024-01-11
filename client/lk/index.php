<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
IncludeTemplateLangFile(__FILE__);
$APPLICATION->SetTitle('Личный кабинет');
$APPLICATION->SetPageProperty("description", 'Личный кабинет');
?>
<main class="client-main">
	<div class="container">
		<h1 class="title"><?= GetMessage("PERSONAL_PROFILE") ?></h1>
		<div class="row">
			<div class="col-4">
				<div class="gray-block">
					<h3 class="title-secondary mb-2"><?= GetMessage("PERSONAL_INFO") ?></h3>
					<div>
						<div class="personal-data mb-2">
							<p><?= GetMessage("PERSONAL_FIO") ?></p>
							<p><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></p>
						</div>
						<div class="personal-data mb-2">
							<p><?= GetMessage("PERSONAL_PHONE") ?></p>
							<p><?= $arUser["PERSONAL_PHONE"] ?></p>
						</div>
						<div class="personal-data">
							<p><?= GetMessage("PERSONAL_EMAIL") ?></p>
							<p><?= $arUser["EMAIL"] ?></p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<h3 class="title-secondary mb-3"><?= GetMessage("GO_TO_PAYMENT") ?></h3>
				<a href="<?= $headerData["LINK_PAYMENT"]["VALUE"] ?>"
					 target="_blank"><?= GetMessage("TITLE_PAYMENT_BUTTON") ?></a>
			</div>
		</div>
		<div class="row mb-5">
			<div class="col-2">
				<a data-bs-toggle="modal" data-bs-target="#changePassword" class="btn btn-primary mt-3" href="#"><?= GetMessage("CHANGE_PASSWD_BUTTON") ?></a>
			</div>
		</div>
		<div>
			<?
			global $arFilter;
			$arFilter = array(
				"=PROPERTY_CLIENT" => $arUser["ID"],
				"=PROPERTY_STATUS_CHECK" => 11
			);
			$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"client-contracts",
				Array(
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"ADD_SECTIONS_CHAIN" => "N",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"AJAX_OPTION_HISTORY" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"CACHE_TIME" => "36000000",
					"CACHE_TYPE" => "A",
					"CHECK_DATES" => "N",
					"DETAIL_URL" => "",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"DISPLAY_DATE" => "N",
					"DISPLAY_NAME" => "N",
					"DISPLAY_PICTURE" => "N",
					"DISPLAY_PREVIEW_TEXT" => "N",
					"DISPLAY_TOP_PAGER" => "N",
					"FIELD_CODE" => array("", ""),
					"FILTER_NAME" => "arFilter",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"IBLOCK_ID" => "contracts",
					"IBLOCK_TYPE" => "content",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"INCLUDE_SUBSECTIONS" => "N",
					"MESSAGE_404" => "",
					"NEWS_COUNT" => "20",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => ".default",
					"PAGER_TITLE" => "Новости",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"PREVIEW_TRUNCATE_LEN" => "",
					"PROPERTY_CODE" => array("CLIENT", "NUMBER", "GRACE_PERIOD", "ADVANCE_PAYMENT", "CONTRACT_TERM", "STATUS", "CONTRACT_AMOUNT", "TYPE", "LAWYER"),
					"SET_BROWSER_TITLE" => "N",
					"SET_LAST_MODIFIED" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_STATUS_404" => "N",
					"SET_TITLE" => "N",
					"SHOW_404" => "N",
					"SORT_BY1" => "ACTIVE_FROM",
					"SORT_BY2" => "SORT",
					"SORT_ORDER1" => "DESC",
					"SORT_ORDER2" => "ASC",
					"STRICT_SECTION_CHECK" => "N"
				)
			);?>
		</div>
	</div>
</main>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>