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
$totalTasks = count($arResult["ITEMS"]);
$completedTasks = 0;

foreach ($arResult["ITEMS"] as $arItem) {
	if ($arItem["PROPERTIES"]["STATUS_TASK"]["VALUE"] == "Выполнено") {
		$completedTasks++;
	}
}

$completionPercentage = ($totalTasks > 0) ? ($completedTasks / $totalTasks) * 100 : 0;
$completionPercentage = number_format($completionPercentage, 2);
?>
<div class="d-flex align-items-center">
	<div style="white-space: nowrap" class="me-3">
		<?= $completionPercentage."%" ?>
	</div>
	<div class="progress-wrapper">
		<div class="progress" style="width: <?= $completionPercentage."%" ?>"></div>
	</div>
</div>
<table>
	<tbody>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<tr>
		<td><?= $arItem["PROPERTIES"]["STATUS_TASK"]["VALUE"] == "Выполнено" ? "V" : "" ?></td>
		<td><?= $arItem["PROPERTIES"]["TYPE_TASK"]["VALUE"] ?></td>
		<td>
			<?= match ($arItem["PROPERTIES"]["STATUS_TASK"]["VALUE"]) {
				'Новая' => '',
				'Выполняется' => 'Ждёт оплаты',
				'На отправку' => 'Выполнено',
				default => $arItem["PROPERTIES"]["STATUS_TASK"]["VALUE"]
			};
			?>
		</td>
	</tr>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
	</tbody>
</table>
