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
?>
<div class="table-responsive">
<table class="table main-table">
	<thead class="table-secondary">
		<tr>
			<th scope="col">№</th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "PROPERTY_CLIENT", "direct" => ($_REQUEST["sort"] == "PROPERTY_CLIENT" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">ФИО клиента <?= ($_REQUEST["sort"] == "PROPERTY_CLIENT" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "PROPERTY_TYPE", "direct" => ($_REQUEST["sort"] == "PROPERTY_TYPE" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">Тип договора <?= ($_REQUEST["sort"] == "PROPERTY_TYPE" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "ACTIVE_FROM", "direct" => ($_REQUEST["sort"] == "ACTIVE_FROM" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">Дата договора <?= ($_REQUEST["sort"] == "ACTIVE_FROM" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "PROPERTY_NUMBER", "direct" => ($_REQUEST["sort"] == "PROPERTY_NUMBER" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">№ договора <?= ($_REQUEST["sort"] == "PROPERTY_NUMBER" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col">Статус проверки</th>
			<th scope="col">Телефон</th>
			<th scope="col">Ответственный</th>
			<th scope="col">Заключивший договор</th>
			<th scope="col">Сумма Договора</th>
			<th scope="col">Остаток Оплаты</th>
			<th scope="col">% выполнения</th>
		</tr>
	</thead>
	<tbody>
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$arClient = CUser::GetByID($arItem["PROPERTIES"]["CLIENT"]["VALUE"])->Fetch();
			$arLawyer = CUser::GetByID($arItem["PROPERTIES"]["LAWYER"]["VALUE"])->Fetch();
			$arCreator = CUser::GetByID($arItem["PROPERTIES"]["CREATOR"]["VALUE"])->Fetch();
			$totalTasks = 0;
			$completedTasks = 0;
			$res = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"tasks", "PROPERTY_CONTRACT" => $arItem["ID"]), false, false, array("ID", "IBLOCK_ID", "PROPERTY_STATUS_TASK"));
			while($ob = $res->GetNext()) {
				$totalTasks++;
				if($ob["PROPERTY_STATUS_TASK_VALUE"] == "Выполнено")
				{
					$completedTasks++;
				}
			}
			$completionPercentage = ($totalTasks > 0) ? ($completedTasks / $totalTasks) * 100 : 0;
			$ostatok = (double)$arItem["PROPERTIES"]["CONTRACT_AMOUNT"]["VALUE"]-(double)$arItem["PROPERTIES"]["ADVANCE_PAYMENT"]["VALUE"];
			foreach ($arItem["PROPERTIES"]["PAYMENT"]["VALUE"] as $payment){
				$resPayment = CIBlockElement::GetByID($payment)->GetNextElement();
				if($resPayment)
				{
					$arPayment = array(
						"FIELDS" => $resPayment->GetFields(),
						"PROPERTIES" => $resPayment->GetProperties()
					);
				}
				if($arPayment["PROPERTIES"]["STATUS_SERVICE"]["VALUE_ENUM"] == "Оплачено") {
					$ostatok = $ostatok - (double)$arPayment["PROPERTIES"]["SUM"]["VALUE"];
				}
			}
			?>
			<tr>
				<td scope="row"><?= $arItem["ID"] ?></td>
				<td class="d-flex">
					<a class="d-flex" style="width: 37px;" href="/client/lk/contract/<?= $arItem['ID'] ?>">
						<img width="37" src="/local/templates/crm-my-prava/images/eye.png" alt="">
					</a>
					<a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?= $arClient["LAST_NAME"] . " " . $arClient["NAME"] . (!empty($arClient["SECOND_NAME"]) ? " " . $arClient["SECOND_NAME"] : "") ?></a>
				</td>
				<td><?= $arItem["PROPERTIES"]["TYPE"]["VALUE_ENUM"] ?></td>
				<td><?= $arItem["ACTIVE_FROM"] ?></td>
				<td><?= $arItem["PROPERTIES"]["NUMBER"]["VALUE"] ?></td>
				<td><?= $arItem["PROPERTIES"]["STATUS_CHECK"]["VALUE_ENUM"] ?></td>
				<td><?= $arClient["PERSONAL_PHONE"] ?></td>
				<td><?= $arLawyer["LAST_NAME"] . " " . $arLawyer["NAME"] . (!empty($arLawyer["SECOND_NAME"]) ? " " . $arLawyer["SECOND_NAME"] : "") ?></td>
				<td><?= $arCreator["LAST_NAME"] . " " . $arCreator["NAME"] . (!empty($arCreator["SECOND_NAME"]) ? " " . $arCreator["SECOND_NAME"] : "") ?></td>
				<td><?= $arItem["PROPERTIES"]["CONTRACT_AMOUNT"]["VALUE"] ?> руб.</td>
				<td><?= $ostatok ?> руб.</td>
				<td><?= $completionPercentage ?>%</td>
			</tr>
		<?endforeach;?>
	</tbody>
</table>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
