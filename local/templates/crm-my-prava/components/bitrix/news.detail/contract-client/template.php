<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
use Bitrix\Iblock\IblockTable;
?>
<main class="main">
	<div class="container">
		<a href="/" class="mb-3 d-flex align-items-center">
			<svg class="me-2" width="33" height="8" viewBox="0 0 33 8" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.646446 3.64645C0.451183 3.84171 0.451183 4.15829 0.646446 4.35355L3.82843 7.53553C4.02369 7.7308 4.34027 7.7308 4.53553 7.53553C4.7308 7.34027 4.7308 7.02369 4.53553 6.82843L1.70711 4L4.53553 1.17157C4.7308 0.976311 4.7308 0.659728 4.53553 0.464466C4.34027 0.269204 4.02369 0.269204 3.82843 0.464466L0.646446 3.64645ZM33 3.5L1 3.5V4.5L33 4.5V3.5Z" fill="black" fill-opacity="0.62"/>
			</svg>
			Назад
		</a>
		<div class="d-flex align-items-center mb-2">
			<h1 class="title mb-0 me-4">Договор <?= $arResult["PROPERTIES"]["NUMBER"]["VALUE"] ?></h1>
			<span><?= $arResult["PROPERTIES"]["TYPE"]["VALUE_ENUM"]?></span>
		</div>
		<p class="mb-2">от <?= $arResult["DISPLAY_ACTIVE_FROM"] ?></p>
<?
$arLawyer = CUser::GetByID($arResult["PROPERTIES"]["LAWYER"]["VALUE"] )->Fetch();

if ($arLawyer) {?>
			<h2 class="title-secondary">Юрист</h2>
			<div class="row mb-3">
				<div class="col-5">
					<div class="gray-block d-flex" style="padding: 7px 17px; background-color: #e2e3e5">
						<span style="color: black" class="me-5"><?= $arLawyer["LAST_NAME"] . " " . $arLawyer["NAME"] . (!empty($arLawyer["SECOND_NAME"]) ? " " . $arLawyer["SECOND_NAME"] : "") ?></span>
						<span style="color: black" class="me-5"><?= $arLawyer["PERSONAL_PHONE"] ?></span>
						<span style="color: black" class="me-5"><?= $arLawyer["EMAIL"] ?></span>
					</div>
				</div>
			</div>
<?} else {
	echo "Пользователь не найден";
}
?>
		<h3 class="title">График платежей</h3>
		<div class="row">
			<div class="col-5">
				<div class="table-responsive">
					<table class="table main-table">
						<tr class="table-secondary">
							<th scope="col">Дата платежа</th>
							<th scope="col">Услуги</th>
							<th scope="col">Чек</th>
							<th scope="col">Статус</th>
							<th scope="col">Почта</th>
							<th scope="col">Чек</th>
							<th scope="col">Статус</th>
						</tr>
					<?
					$iblockPayments = IblockTable::getList([
						'select' => ['ID'],
						'filter' => ['CODE' => "payments"],
					])->fetch();
					$arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_*");
					$arFilter = Array("IBLOCK_ID" => $iblockPayments["ID"], "ID" => $arResult["PROPERTIES"]["PAYMENT"]["VALUE"]);
					$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
					while($ob = $res->GetNextElement()){
						$arPaymentFields = $ob->GetFields();
						$arPayment = $ob->GetProperties();
						?>
						<tr>
							<td><?= $arPayment["DATE"]["VALUE"] ?></td>
							<td><?= $arPayment["SUM"]["VALUE"] ?></td>
							<td>
								<div>
									<? foreach ($arPayment["CHECK_SERVICE"]["VALUE"] as $check): ?>
										<?= CFile::GetFileArray($check)["ORIGINAL_NAME"] ?><a href="<?= CFile::GetPath($check) ?>" download>Скачать</a>
									<? endforeach; ?>
								</div>
							</td>
							<td><?= !empty($arPayment["STATUS_SERVICE"]["VALUE"]) ? $arPayment["STATUS_SERVICE"]["VALUE"] : "Не оплачено" ?></td>
							<td><?= $arPayment["POSTAGE"]["VALUE"] ?></td>
							<td>
								<div>
									<? foreach ($arPayment["CHECK_POST"]["VALUE"] as $check): ?>
										<?= CFile::GetFileArray($check)["ORIGINAL_NAME"] ?><a href="<?= CFile::GetPath($check) ?>" download>Скачать</a>
									<? endforeach; ?>
								</div>
							</td>
							<td><?= !empty($arPayment["STATUS_POST"]["VALUE"]) ? $arPayment["STATUS_POST"]["VALUE"] : "Не оплачено" ?></td>
						</tr>
						<?
					}
					?>
					</table>
				</div>
			</div>
			<div class="col-3 offset-1">
				<table class="table main-table">
					<thead class="table-secondary">
						<tr>
							<th>Документ</th>
						</tr>
					</thead>
					<tbody>
						<? foreach ($arResult["PROPERTIES"]["DOCS_BY_CLIENT"]["VALUE"] as $index => $doc): ?>
							<tr>
								<td>
									<?= CFile::GetFileArray($doc)["ORIGINAL_NAME"] ?>
								</td>
							</tr>
						<? endforeach; ?>
					</tbody>
				</table>
				<button class="btn btn-primary" data-bs-target="#addDocs" data-bs-toggle="modal">Добавить</button>
				<div class="modal fade" id="addDocs" aria-hidden="true" aria-labelledby="resetPasswordLabel" tabindex="-1">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<h3 class="title" id="docsForClientLabel">Загрузить документ</h3>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
								<form enctype="multipart/form-data" method="post" action="/ajax/contracts/add_file.php">
									<input type="text" name="contract_id" value="<?= $arResult["ID"] ?>" hidden>
									<input type="text" name="property_code" value="DOCS_BY_CLIENT" hidden>
									<input class="form-control mb-3" type="file" name="files[]" multiple>
									<input class="btn btn-primary" type="submit">
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-3">
				<table class="table main-table">
					<thead class="table-secondary"> 
						<tr>
							<th>Скачать документы</th>
						</tr>
					</thead>
					<tbody>
						<? foreach ($arResult["PROPERTIES"]["DOCS_FOR_CLIENT"]["VALUE"] as $index => $doc): ?>
							<tr>
								<td>
									<a href="<?= CFile::GetPath($doc) ?>" download><?= CFile::GetFileArray($doc)["ORIGINAL_NAME"] ?></a>
								</td>
							</tr>
						<? endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row mt-5">
			<div class="col-5">
				<?
				global $arFilter;
				$arFilter = array(
					"=PROPERTY_CONTRACT" => $arResult["ID"],
					"!PROPERTY_SHOW_IN_LK" => false
				);
				$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"client-tasks",
					Array(
						"ACTIVE_DATE_FORMAT" => "d.m.Y",
						"ADD_SECTIONS_CHAIN" => "Y",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_ADDITIONAL" => "",
						"AJAX_OPTION_HISTORY" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "Y",
						"CACHE_FILTER" => "N",
						"CACHE_GROUPS" => "Y",
						"CACHE_TIME" => "36000000",
						"CACHE_TYPE" => "A",
						"CHECK_DATES" => "Y",
						"DETAIL_URL" => "",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"DISPLAY_DATE" => "Y",
						"DISPLAY_NAME" => "Y",
						"DISPLAY_PICTURE" => "Y",
						"DISPLAY_PREVIEW_TEXT" => "Y",
						"DISPLAY_TOP_PAGER" => "N",
						"FIELD_CODE" => array("", ""),
						"FILTER_NAME" => "arFilter",
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",
						"IBLOCK_ID" => "tasks",
						"IBLOCK_TYPE" => "content",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
						"INCLUDE_SUBSECTIONS" => "Y",
						"MESSAGE_404" => "",
						"NEWS_COUNT" => "14",
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
						"PROPERTY_CODE" => array("CLIENT", "NUMBER", "GRACE_PERIOD", "ADVANCE_PAYMENT", "CONTRACT_TERM", "STATUS", "CONTRACT_AMOUNT", "TYPE", "LAWYER", ""),
						"SET_BROWSER_TITLE" => "Y",
						"SET_LAST_MODIFIED" => "N",
						"SET_META_DESCRIPTION" => "Y",
						"SET_META_KEYWORDS" => "Y",
						"SET_STATUS_404" => "N",
						"SET_TITLE" => "Y",
						"SHOW_404" => "N",
						"SORT_BY1" => "ACTIVE_FROM",
						"SORT_BY2" => "SORT",
						"SORT_ORDER1" => "DESC",
						"SORT_ORDER2" => "ASC",
						"STRICT_SECTION_CHECK" => "N",
						"USER_DATA" => $arUser
					)
				);?>
			</div>
		</div>
	</div>
</main>
