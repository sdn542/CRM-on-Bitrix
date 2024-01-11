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
$arClient = CUser::GetByID($arResult["PROPERTIES"]["CLIENT"]["VALUE"])->Fetch();
?>
<main class="main">
	<div class="container">
		<div class="top d-flex align-items-center mb-4">
			<a href="/client/lk/contract/<?= $arResult['ID'] ?>"><img width="60" src="/local/templates/crm-my-prava/images/eye.png" alt=""></a>
			<h1 class="title mb-0">
				Договор <?= $arResult["PROPERTIES"]["NUMBER"]["VALUE"] ?> 
				<span class="contract-date">от <?= $arResult["DISPLAY_ACTIVE_FROM"] ?></span>
			</h1>
			<div class="d-flex align-items-center ms-auto">
				<form class="me-4" method="post" action="/ajax/contracts/recreate_doc.php">
					<input type="hidden" name="contract_id" value="<?= $arResult["ID"] ?>">
					<input class="btn btn-primary" type="submit" value="Пересоздать договор">
				</form>
				<? if(checkAccess(2)): ?>
				<form class="d-flex align-items-center gap-4" method="post" action="/ajax/docx_handler.php">
					<a class="btn btn-primary" href="/lawyer/contract/create/?client_id=<?= $arResult["PROPERTIES"]["CLIENT"]["VALUE"] ?>">Новый договор</a>
					<input type="hidden" name="contract_id" value="<?= $arResult["ID"] ?>">
					<select class="form-select" name="template_id">
						<?
						$iblockTemplates = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'templates-docs'], true)->Fetch();
						$arSelect = Array("ID", "IBLOCK_ID", "NAME");
						$arFilter = Array("IBLOCK_ID"=>$iblockTemplates["ID"], "PROPERTY_TYPE_CONTRACT_VALUE"=>false, "PROPERTY_TYPE_TASK"=>false);
						$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
						while($ob = $res->GetNextElement()){
							$arFields = $ob->GetFields();
						?>
							<option value="<?= $arFields["ID"] ?>"><?= $arFields["NAME"] ?></option>
						<?
						}
						?>
					</select>
					<input class="btn btn-primary" type="submit">
				</form>
				<? endif; ?>
			</div>
		</div>
		<div class="row block mb-3">
			<div class="col-5">
				<h3 class="title-secondary">Документы для клиента</h3>
				<table class="table main-table mb-0"> 
					<thead class="table-secondary">
						<tr>
							<th>Дата загрузки</th>
							<th class="text-end">Загрузить/Скачать файл</th>
						</tr>
					</thead>
				</table>
				<div class="scroll-vertical-table" style="max-height: 200px; margin-bottom: 15px;">		
					<table class="table main-table mb-0">
						<tbody>
							<? foreach ($arResult["PROPERTIES"]["DOCS_FOR_CLIENT"]["VALUE"] as $index => $doc): ?>
								<tr>
									<td>
										<?= $arResult["PROPERTIES"]["DOCS_FOR_CLIENT"]["DESCRIPTION"][$index] ?>
									</td>
									<td class="d-flex align-items-center justify-content-end">
										<a href="<?= CFile::GetPath($doc) ?>" download><?= CFile::GetFileArray($doc)["ORIGINAL_NAME"] ?></a>
										<? if(checkAccess(1)): ?>
										<form action="/ajax/contracts/delete_file.php" method="post">
											<input type="text" name="contract_id" value="<?= $arResult["ID"] ?>" hidden>
											<input type="text" name="file_id" value="<?= $doc ?>" hidden>
											<input type="text" name="property_code" value="DOCS_FOR_CLIENT" hidden>
											<input class="btn p-0 mx-2" type="submit" value="x">
										</form>
										<? endif; ?>
									</td>
								</tr>
							<? endforeach; ?>
						</tbody>
					</table>
				</div>
				<button class="btn btn-primary" data-bs-target="#docsForClient" data-bs-toggle="modal">Добавить</button>
				
				<div class="modal fade" id="docsForClient" aria-hidden="true" aria-labelledby="docsForClientLabel" tabindex="-1">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<h3 class="title" id="docsForClientLabel">Загрузить документ</h3>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
								<form enctype="multipart/form-data" method="post" action="/ajax/contracts/add_file.php">
									<input type="text" name="contract_id" value="<?= $arResult["ID"] ?>" hidden>
									<input type="text" name="property_code" value="DOCS_FOR_CLIENT" hidden>
									<input class="form-control mb-3" type="file" name="files[]" multiple>
									<input class="btn btn-primary" type="submit">
								</form>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="col-6 offset-1">
				<form class="form-reload" method="post" action="/ajax/contracts/update_status.php">
					<input type="text" name="contract_id" value="<?= $arResult["ID"] ?>" hidden>
					<div class="row mb-3">
						<div class="col-4 d-flex flex-column">
							<h2 class="title-secondary">Статус проверки договора</h2>
							<div class="border-block p-4 d-flex flex-column h-100">
								<?
								$iblock = IblockTable::getList([
									'select' => ['ID'],
									'filter' => ['CODE' => "contracts"],
								])->fetch();
								$property_enums = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblock["ID"], "CODE" => "STATUS_CHECK"));
								while($enum_fields = $property_enums->GetNext())
								{?>
									<div class="form-check px-0">
										<label class="form-check-label" for="<?= $enum_fields["ID"] ?>">
											<?= $enum_fields["VALUE"] ?>
										</label>
										<input 
											class="form-check-input" 
											type="radio" 
											name="status_check" 
											id="<?= $enum_fields["ID"] ?>" 
											value="<?= $enum_fields["ID"] ?>"
											<?= checkAccess(1) ? "" : "disabled" ?>
											<?= $arResult["PROPERTIES"]["STATUS_CHECK"]["VALUE_ENUM_ID"] == $enum_fields["ID"] ? "checked" : "" ?>
											style="float: right"
										>
									</div>
								<?} ?>
							</div>
						</div>
						<div class="col-4 d-flex flex-column ">
							<h2 class="title-secondary">Статус договора</h2>
							<div class="border-block p-4 d-flex flex-column h-100">
								<?
								$property_enums2 = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblock["ID"], "CODE" => "STATUS"));
								while($enum_fields2 = $property_enums2->GetNext())
								{?>
									<div class="form-check px-0">
										<label class="form-check-label" for="<?= $enum_fields2["ID"] ?>">
											<?= $enum_fields2["VALUE"] ?>
										</label>
										<input 
											class="form-check-input" 
											type="radio" 
											name="status" 
											id="<?= $enum_fields2["ID"] ?>" 
											value="<?= $enum_fields2["ID"] ?>"
											<?= checkAccess(0) ? "" : "disabled" ?>
											<?= $arResult["PROPERTIES"]["STATUS"]["VALUE_ENUM_ID"] == $enum_fields2["ID"] ? "checked" : "" ?>
											style="float: right"
										>
									</div>
								<?} ?>
							</div>
						</div>
					</div>
					<input class="btn btn-primary" type="submit" value="Сохранить">
				</form>
			</div>
		</div>

		<div class="d-flex align-items-start">
			<h2 class="title">Данные клиента</h2>
			<? if(checkAccess(2)): ?>
				<button class="ms-2 mt-1" data-bs-target="#editModal" data-bs-toggle="modal">
					<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<rect width="15" height="15" fill="url(#pattern0)"/>
						<defs>
						<pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
						<use xlink:href="#image0_4829_2124" transform="translate(-0.0200573) scale(0.00286533)"/>
						</pattern>
						<image id="image0_4829_2124" width="363" height="349" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAWsAAAFdCAYAAADFZUzIAAAS00lEQVR4nO3dW3RV9Z3A8V9IgiCExJSbCoEgERerLhUEHSsEuQhyFZIQLoFILbpW56XjQ8vM68y8TKEvnYfOjAOE0EVu0ALhopXWQKl1hsSVF6yxSxSqXAQDM4iQ5OTMg7NtkHByLv+9///f3t/Puzu/5cN3/daPc/bJutnVHRcAgNMG2R4AADAwYg0AChBrAFCAWAOAAsQaABQg1gCgALEGAAWINQAoQKwBQAFiDQAKEGsAUIBYA4ACxBoAFCDWAKAAsQYABYg1AChArAFAAWINAAoQawBQgFgDgALEGgAUINYAoACxBgAFiDUAKECsAUABYg0AChBrAFCAWAOAAsQaABQg1gCgALEGAAWINQAoQKwBQAFiDQAKEGsAUIBYA4ACxBoAFCDWAKAAsQYABYg1AChArAFAAWINAAoQawBQgFgDgALEGgAUINYAoACxBgAFiDUAKECsAUABYg0AChBrAFCAWAOAAsQaABQg1gCgALEGAAWINQAoQKwBQAFiDQAKEGsAUIBYA4ACxBoAFCDWAKAAsQYABYg1AChArAFAAWINAAoQawBQgFgDgALEGgAUINYAoACxBgAFcmwPAMAN3d3d0vL223L8eIu0t7fLx2fOSOfVq9Lb2yt5w/OkaEKRTJ06VZ6dNUuef36h5OXl2R45UrJudnXHbQ8BwJ5PP/2L/OvPfy67amrkypUrSf03Q4cOlRdfXCk/eu01eeyxx3yeECLEGoisrq4u2frTf5FtW7fKl19+mdYzsrKypLJyjfx061YZNXq04QnRF7EGIujMmTOyYf06OXXqlJHnjRk7Vmprd8vs0lIjz8OdiDUQMe3t7bJi+TK5cP680ecOHjxYXt++XVavrjT6XHyNf2AEIqS9vV0WL1qY9G06FV1dXbKpulq6u7plfVWV8edHHR/dAyLCz1B7YrGYvLL5B/LL3bt9+xtRRayBCAgi1B6C7Q9iDYRckKH2EGzziDUQYjZC7SHYZhFrIKRshtpDsM0h1kAIuRBqD8E2g1gDIeNSqD0EO3PEGggRF0PtIdiZIdZASLgcag/BTh+xBkJAQ6g9BDs9xBpQTlOoPQQ7dcQaUExjqD0EOzXEGlDqvffeUxtqD8FOHm/dAxRqa2uTJS8sks7OTtujZMwLtojwtr4E2KwBZcIUag8b9sCINaBIGEPtIdiJ8UsxgBIdHR1SOuvZUIa6r+zsbPn3/3idk8i3sFkDSkyaNEnmzp1newzfeRv27tpa26M4hVgDSuTk5EhNba2UlZXbHsV3sVhMXn1lM8Hug1gDihDs6CLWgDIEO5qINaAQwY4eYg0oRbCjhVgDihHs6CDWgHIEOxqINRACBDv8iDUQEl6wy8srbI/iuygGm1gDIZKTkyM7d+0i2CFErAFLWltb5W9/+EPp6ekx+tycnBzZUVMTqZNIfX2d7VF8x/usAQtaW1tl6eIXpLOzU/7n2jXZuWuXZGdnG3t+bm6u1NTWSjwel3379hp7rotisZhsfvllGTtmrJTOmWN7HN/w1j0gYH1D7amoWG082CIiPT09srGqKvTBFhEZe//9cupUq4wcNcr2KL7gDAIEqL9Qi4g0NjbIxqoqX04iu3bvjsQN+8L587JlyxbbY/iGzRoIyN1C3dfq1ZWyfedOyckxe6Hs6emR6g0bZO/eJqPPdc2gQYPk3f8+JY8++qjtUYxjswYCkEyoRUQaGuplU3W1xGIxo3/f+1jfqlVlRp/rmt7eXvnZtm22x/AFmzXgs2RD3VdZWbnU1Nb6smG/tHGjNDU1Gn2uS+699145+5dPZfjw4bZHMYrNGvBROqEWEdm7t0le3rTJlw17565dof5Y340bN+TYsbdsj2EcsQZ8km6oPfX1dfL9l17iJJKG3584YXsE44g14INMQ+3xM9hh/pTI6dOnbY9gHLEGDDMVao+fwQ7rSeTc2bO2RzCOWAMGmQ61h5NIav73+nXbIxhHrAFD/Aq1h5NI8rIHmf0mqAuINWCA36H2cBJJTkFBvu0RjCPWQIaCCrXH75NIGDbsicXFtkcwjlgDGQg61B6/N2ztwX788cdtj2AcsQbSZCvUHk4id1daGr5XpfJ1cyANtkPdV2XlGtm+c6cvr1fV+NX0MWPHykdnPjb+/8M2NmsgRS6FWuTrDdvPr6ZrO4lUV1eHLtQibNZASlwLdV9+btibqqulsbHB6HP9MGzYMHn/Tx/I6DFjbI9iHJs1kCSXQy3i7w1by286/vgnW0IZahE2ayAproe6r6hu2DNnzpRjv3tbcnNzbY/iCzZrYACaQi3i7w17R02Nkzfs+x94QH65Z09oQy1CrIGEtIXaU1e3JzL/6FhYWCgHDjbL+PFFtkfxFbEG7kJrqD1+B7uiYrXR56ajsLBQDh99I5S/ufhtxBroh/ZQe/wM9o6aGqvB9kIdxm8r9odYA98SllB7whjsqIVahFgDtwlbqD1hCnYUQy1CrIFvhDXUnjAEu7CwUA4dORq5UIsQa0BEwh9qj+Zge6F+4oknfPsbLiPWiLyohNqjMdhRD7UIsUbERS3UHk3BJtRfI9aIrKiG2qMh2IT6r4g1Iinqofa4HGxCfTtijcgh1LdzMdiE+k7EGpFCqPvnUrAJdf+INSKDUCfmQrAJ9d0Ra0QCoU6OzWAT6sSINUKPUKfGRrALCwul+fARQp0AsUaoEer0BBlsL9TTpk0z+rfCJsf2AIBfCHVm6ur2iIjIf+7YYfQnwrxgi4gcO/YWoU4Sv8GIUCLU5qxdu05e377dl990PHPmjJSUlBh9blgRa4QOoTZvzZq1xjdspIabNUKFUPvDrxs2kkesERqE2l8E2y5ijVAg1MEg2PYQa6hHqINFsO0g1lCNUNtBsINHrKFWW1sbobaIYAeLWEOltrY2WfLCIkJtGcEODrGGOoTaLQQ7GMQaqhBqNxFs/xFrqEGo3VZXt0de+7sf2R4jtIg1VCDU7issLJSXNn3f9hihRazhPELtPn44wH/EGk4j1O4j1MEg1nAWoXYfoQ4OsYaTCLX7CHWwiDWcQ6jdR6iDR6zhFELtPkJtB7GGMwi1+wi1PcQaTiDU7iPUdhFrWEeo3Ueo7SPWsIpQu49Qu4FYwxpC7T5C7Q5iDSsItfsItVuINQJHqN1XWFgozYePEGqHEGsEilC7zwv1tGnTbI+CPog1AkOo3Ueo3UWsEQhC7T5C7TZiDd8RavcRavcRa/iKULuPUOtArOEbQu0+Qq0HsYYvCLX7CLUuxBrGEWr3EWp9iDWMItTuI9Q6EWsYQ6jdR6j1ItYwglC7j1DrRqyRMULtvoKCAkKtHLFGRgi1+wh1OBBrpI1Qu88L9ZNPPml7FGSIWCMthNp9hDpciDVSRqjdR6jDh1gjJYTafYQ6nIg1kkao3Ueow4tYIymE2n2EOtyINQZEqN1HqMOPWCMhQu0+Qh0NxBp3RajdV1BQIM2HDhPqCCDW6Behdt83oZ4xw/YoCACxxh0ItfsIdfQQa9yGULuPUEcTscY3CLX7CHV0EWuICKHWgFBHG7EGoVaAUINYRxyhdh+hhgixjjRC7T5CDQ+xjihC7T5Cjb6IdQQRavcRanwbsY4YQu0+Qo3+EOsIIdTuI9S4G2IdEYTafYQaiRDrCCDU7iPUGAixDjlC7T5CjWQQ6xAj1O4j1EgWsQ4pQu0+Qo1UEOsQItTuI9RIFbEOGULtPkKNdBDrECHU7iPUSBexDglC7b78/HxCjbQR6xAg1O7Lz8+XQ4ePEGqkjVgrR6jdR6hhArFWjFC7j1DDFGKtFKF2H6GGScRaIULtPkIN04i1MoTafYQafiDWihBq9xFq+IVYK0Go3Ueo4SdirQChdh+hht+IteMItfsINYJArB1GqN1HqBEUYu0oQu0+3vWBIBFrBxFq93mhnjFzpu1REBHE2jGE2n2EGjYQa4cQavcRathCrB1BqN1HqGETsXYAoXYfoYZtxNoyQu0+Qg0XEGuLCLX7CDVcQawtIdTuI9RwSdbNru647SGi5vTp0zJ/7nPyxRdf2B4Fd8GvkMM1xDpgN2/elBnTp8uHH3bYHgV3UVBQIAebD7FRwymcQQL2b7/4BaF2WH5+PqGGk9isA/bdqVPlz3/+0PYY6AcvZYLL2KwD1NraSqgdxUuZ4DpiHaCmxgbbI6AffOoDGnAGCUg8HpeHJ0+Wc+fO2h4FfRBqaMFmHZA/vvMOoXYMoYYmxDogjZxAnEKooQ1nkAD09vZK8cQJcvHCBdujQAg1dGKzDsDxlhZC7QhCDa2IdQAaGxttjwAh1NCNM4jPuru7ZeKEIrly+bLtUSKNUEM7Nmuf/fbYMUJtGaFGGBBrn3ECsYtQIyw4g/jo1q1bUjTuQbl27ZrtUSKJUCNM2Kx99MYbRwm1JYQaYUOsfdTUwAnEBkKNMOIM4pMbN25I0bgH5fr167ZHiRRCjbBis/bJoUPNhDpghBphRqx90lBfb3uESCHUCDvOID64evWqTBg/Tm7dumV7lEgg1IgCNmsfHNi/n1AHhFAjKoi1D+rr62yPEAn8uC2ihDOIYZ9fuiTFEydIT0+P7VFCzQv1zKeesj0KEAg2a8P27t1LqH1GqBFFxNowTiD+ItSIKs4gBp07d1amlJRIb2+v7VFCiVAjytisDWpsaCTUPiHUiDpibVADJxBfEGqAWBvT0dEh7e3ttscIHUINfI1YG9LU2CDxOOd/kwg18FfE2pD6Ok4gJhFq4HbE2oD29nb54IMPbI8RGoQauBOxNoB/WDSHUAP943PWBkwpKZFPPvnY9hjqEWrg7tisM/Rf775LqA0g1EBixDpDjY0NtkdQj1ADA+MMkoF4PC4PFRfLZ599ansUtQg1kBw26wz84eRJQp0BQg0kj1hngBNI+gg1kBrOIGmKxWJSPHGCXLp40fYo6uTl5UnzocPy1NNP2x4FUIPNOk3HW1oIdRry8vLkwMFmQg2kKMf2AFo1NTXZHkEdNmogfZxB0tDT0yMTJxTJ5c8/tz2KGt5G/TfPPGN7FEAlziBpON7SQqhTQKiBzBHrNPzqV/tsj6AGoQbM4AySolgsJhMnFMnnly7ZHsV5hBowh806RSeOHyfUSSDUgFnEOkX79u21PYLzCDVgHmeQFPT29krxxAly8cIF26M4i1AD/mCzTsHvT5wg1AmMGDGCUAM+IdYp2L//17ZHcNaIESNk/4GDhBrwCbFOwYH9B2yP4KTJk0vkzbeOEWrAR3zdPEltbW1y7txZ22M4paTkYfnB5s3yyquvytChQ22PA4QasU7SAU4gIiLy4IPjZFXZKikvr+AdH0CAiHWS9v86urEeOWqUrFy5UsrKyqV0zhzJysqyPRIQOcQ6CR0dHfL+++/bHiNQw4YNk6VLl8mGjRvlublzJTs72/ZIQKQR6yREZavOycmR2aWlUlW1QZavWCHDhw+3PRKA/0esk9DcfND2CL7JysqS6dOny9p162X16tUyavRo2yMB6AffYBzAlcuXpWj8OInFYrZHMWr8+CJZs3aNrF23XqZOnWp7HAADYLMewJtvvhmaUBcUFMjyFS/KunXr+IdCQBliPYAjhw/bHiEjubm5Mn/BAqnasEGWLFkqQ4YMsT0SgDRwBkkgFovJuAful87OTtujpMS7Q1dt2CgVFRXynZEjbY8EIENs1gn88Z13VIW6uHiSVK6plLXr1suUKVNsjwPAIGKdwNGjR2yPMKDvjBwpZWVlUlm5Rp753ve4QwMhRawTOHnypO0R+pWXlyfLlq+Q8opyWbDgecnNzbU9EgCfEesEPvn4E9sjfGPIkCGycOEiWVVeJsuXr+DFSUDEEOsEBg2y+wbZ3NxcmfPcc7JqVZm8uHKl3HfffVbnAWAPsU6geFJx4K9Fzc3NlVmzZ0t5eYWsWLGCT3IAEBFindC8efPleEuL73/nnnvukfkLFsiyZctl2bJlBBrAHficdQIXL1yQR6Y8LF999ZXxZxcUFMiCBc/L0mVL5YXFS2TEiBHG/waA8CDWA/jZtm3yD3+/xcizHnposixeslgWLlwks0tLZfDgwUaeCyD8iPUA4vG4vLJ5s9Tuqkn5vx08eLA89fTTsnjxElm8ZAlfVAGQNmKdhHg8Ltu2bpV//qd/THgSycrKkkceeUTmzpsn8+bPl9mzS3knNAAjiHUKzp//THbs2CFv/eY38tFHH0k8Hpei8ePlyRkzZNbs2TLr2Vm8DxqAL4g1AChg91sfAICkEGsAUIBYA4ACxBoAFCDWAKAAsQYABYg1AChArAFAAWINAAoQawBQgFgDgALEGgAUINYAoACxBgAFiDUAKECsAUABYg0AChBrAFCAWAOAAsQaABQg1gCgALEGAAWINQAoQKwBQAFiDQAKEGsAUIBYA4ACxBoAFCDWAKAAsQYABYg1AChArAFAAWINAAoQawBQgFgDgALEGgAU+D/omUyH8NLjlAAAAABJRU5ErkJggg=="/>
						</defs>
					</svg>
				</button>
			<? endif; ?>
		</div>
		<div class="row block mb-3">
			<div class="col-6">
				<h3 class="title-secondary px-3">Личные данные</h3>
				<div class="border-block mb-3" style="overflow-x: auto">
					<div class="info">
						<div>Email / Логин</div><div></div><div><?= $arClient["EMAIL"] ?></div>
					</div>
					<div class="info">
						<div>ФИО</div><div></div><div><?= $arClient["LAST_NAME"] . " " . $arClient["NAME"] . (!empty($arClient["SECOND_NAME"]) ? " " . $arClient["SECOND_NAME"] : "") ?></div>
					</div>
					<div class="info">
						<div>Место рождения</div><div></div><div><?= $arClient["UF_PLACE_BIRTH"] ?>s</div>
					</div>
					<div class="info">
						<div>Ожидаемый клиентом результат</div><div></div><div><?= $arClient["UF_CUSTOMER_EXPECTED_OUTCOME"] ?></div>
					</div>
					<div class="info">
						<div>Трудоустройство и доход</div><div></div><div><?= $arClient["UF_EMPLOYMENT_INCOME"] ?></div>
					</div>
					<div class="info">
						<div>Контактный телефон</div><div></div><div><?= $arClient["PERSONAL_PHONE"] ?></div>
					</div>
					<div class="info">
						<div>Адрес места жительства</div><div></div><div><?= $arClient["UF_RESIDENCE_ADDRESS"] ?></div>
					</div>
				</div>
				<h3 class="title-secondary px-3">Паспортные данные</h3>
				<div class="border-block mb-3" style="overflow-x: auto"> 
					<div class="info">
						<div>Серия/номер</div><div></div><div><?= $arClient["UF_SERIES"] ?>/<?= $arClient["UF_NUMBER"] ?></div>
					</div>
					<div class="info">
						<div>Когда выдан</div><div></div><div><?= $arClient["UF_DATE_ISSUED"] ?></div>
					</div>
					<div class="info">
						<div>Кем выдан</div><div></div><div><?= $arClient["UF_ISSUING_AUTHORITY"] ?></div>
					</div>
					<div class="info">
						<div>ИНН</div><div></div><div><?= $arClient["UF_INN"] ?></div>
					</div>
					<div class="info">
						<div>СНИЛС</div><div></div><div><?= $arClient["UF_SNILS"] ?></div>
					</div>
				</div>
				<h3 class="title-secondary px-3">Состояние задолженности</h3>
				<div class="border-block mb-3" style="overflow-x: auto">
					<div class="info">
						<div>Наличие задолженности по кредитам и МФО</div><div></div><div><?= $arClient["UF_MFOS_DEBTS"] ?></div>
					</div>
					<div class="info">
						<div>Наличие просрочек на момент заключения договора</div><div></div><div><?= $arClient["UF_PREEXISTING_DELINQUENCIES"] ?></div>
					</div>
					<div class="info">
						<div>Наличие имущества</div><div></div><div><?= $arClient["UF_OWNERSHIP"] ?></div>
					</div>
					<div class="info">
						<div>Наличие вынесенных судебных актов по взысканию задолженности</div><div></div><div><?= $arClient["UF_JUDGMENT_DECREES"] ?></div>
					</div>
					<div class="info">
						<div>Наличие задолженности по ЖКХ</div><div></div><div><?= $arClient["UF_UTILITY_ARREARS"] ?></div>
					</div>
					<div class="info">
						<div>Наличие исполнительных производств</div><div></div><div><?= $arClient["UF_EXECUTION_PROCEEDINGS"] ?></div>
					</div>
					<div class="info">
						<div>Наличие задолженности по налогам и сборам</div><div></div><div><?= $arClient["UF_TAX_OBLIGATIONS"] ?></div>
					</div>
					<div class="info">
						<div>Наличие поручителей/созаемщиков по кредитным обязательствам</div><div></div><div><?= $arClient["UF_COSIGNERS_COBORROWERS"] ?></div>
					</div>
					<div class="info">
						<div>Общая сумма заложенности</div><div></div><div><?= $arClient["UF_COLLATERAL_VALUE"] ?></div>
					</div>
					<div class="info">
						<div>Сделки в трёхлетний период</div><div></div><div><?= $arClient["UF_THREE_YEAR_TRANSACTIONS"] ?></div>
					</div>
					<div class="info">
						<div>Количество кредиторов</div><div></div><div><?= $arClient["UF_CREDITOR_COUNT"] ?></div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<h3 class="title-secondary px-3">Примечание</h3>
				<div class="border-block mb-3 p-2">
					<p><?= $arClient["UF_NOTES"] ?></p>
				</div>
				<h3 class="title-secondary px-3">План работы</h3>
				<div class="border-block mb-3 p-2">
					<p><?= $arClient["UF_WORK_PLAN"] ?></p>
				</div>
				<h3 class="title-secondary px-3">История изменений</h3>
				<div class="border-block mb-3 p-2"> 
					<p><?= $arClient["UF_CHANGE_HISTORY"] ?></p>
				</div>
			</div>
		</div>
		 
		<h2 class="title">
			Задачи
		</h2>
		<div class="block mb-3">
			<?
				global $arFilter;
				$arFilter = array(
					"=PROPERTY_CONTRACT" => $arResult["ID"]
				);
				$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"contract-tasks",
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
				);
			?>
		</div>
		<h2 class="title">Документы</h2>
		<div class="block mb-4">
			<div class="row">
				<div class="col-6">
					<h3 class="title-secondary">Документы по клиенту</h3>
					<table class="table main-table mb-0"> 
						<thead class="table-secondary">
							<tr>
								<th>Дата загрузки</th>
								<th class="text-end">Загрузить/Скачать файл</th>
							</tr>
						</thead>
					</table>
					<div class="scroll-vertical-table" style="max-height: 300px; margin-bottom: 15px;">		
						<table class="table main-table mb-0">
							<tbody>
								<? foreach ($arResult["PROPERTIES"]["DOCS_BY_CLIENT"]["VALUE"] as $index => $doc): ?>
									<tr>
										<td>
											<?= $arResult["PROPERTIES"]["DOCS_BY_CLIENT"]["DESCRIPTION"][$index] ?>
										</td>
										<td class="d-flex align-items-center justify-content-end">
											<a href="<?= CFile::GetPath($doc) ?>" download><?= CFile::GetFileArray($doc)["ORIGINAL_NAME"] ?></a>
											<? if(checkAccess(1)): ?>
											<form action="/ajax/contracts/delete_file.php" method="post">
												<input type="text" name="contract_id" value="<?= $arResult["ID"] ?>" hidden>
												<input type="text" name="file_id" value="<?= $doc ?>" hidden>
												<input type="text" name="property_code" value="DOCS_BY_CLIENT" hidden>
												<input class="btn p-0 mx-2" type="submit" value="x">
											</form>
											<? endif; ?>
										</td>
									</tr>
								<? endforeach; ?>
							</tbody>
						</table>
					</div>
	
					<button class="btn btn-primary" data-bs-target="#docsByClient" data-bs-toggle="modal">Добавить документ</button>
	
					<div class="modal fade" id="docsByClient" aria-hidden="true" aria-labelledby="docsByClientLabel" tabindex="-1">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-body">
									<h3 class="title" id="docsByClientLabel">Загрузить документ</h3>
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
					<?
						global $arFilterDoc;
						$arFilterDoc = array(
							"=PROPERTY_CONTRACT" => $arResult["ID"]
						);
						$APPLICATION->IncludeComponent(
							"bitrix:news.list",
							"contract-incoming-documents",
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
								"FILTER_NAME" => "arFilterDoc",
								"HIDE_LINK_WHEN_NO_DETAIL" => "N",
								"IBLOCK_ID" => "incoming-documents",
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
								"PROPERTY_CODE" => array("CLIENT", "NUMBER", "GRACE_PERIOD", "ADVANCE_PAYMENT", "CONTRACT_TERM", "STATUS", "CONTRACT_AMOUNT", "TYPE", "LAWYER", "CONTRACT"),
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
								"CONTRACT" => $arResult["ID"]
							)
						);
					?>
			</div>
		</div>
		<div class="row">
			<div class="col-6">
				<div class="block">
					<h3 class="title">Кредиторы</h3>
					<?
					$arFilter = array(
						"IBLOCK_CODE" => "creditors",
						"IBLOCK_TYPE" => "content",
						"ID" => $arResult["PROPERTIES"]["CREDITOR"]["VALUE"]
					);
					$arSelect = array("ID", "NAME", "PROPERTY_*");
					$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
					$index = 1;
					while ($arElement = $rsElements->Fetch()) {?>
						<div class="d-flex flex-column mb-4">
							<div class="d-flex align-items-center mb-2">
								<span class="creditor-index"><?= sprintf('%02d', $index) ?></span>
								<span class="creditor-name"><?= $arElement["NAME"] ?></span>
								<? if(checkAccess(2)): ?>
								<form method="post" action="/ajax/contracts/delete_creditor.php">
									<input type="text" name="contract_id" value="<?= $arResult["ID"] ?>" hidden>
									<input type="text" name="creditor_id" value="<?= $arElement["ID"] ?>" hidden>
									<input class="btn" type="submit" value="x">
								</form>
								<? endif; ?>
							</div>
					<? $arFilterCreditor = array(
							"IBLOCK_CODE" => "contract_creditors",
							"IBLOCK_TYPE" => "content",
							"PROPERTY_CREDITOR" => $arElement["ID"],
							"PROPERTY_CONTRACT" => $arResult["ID"],
						);
						$arSelectCreditor = array("ID", "NAME", "PROPERTY_NUMBER", "PROPERTY_DATE");
						$rsElementsCreditor = CIBlockElement::GetList(array("ID"=>"DESC"), $arFilterCreditor, false, false, $arSelectCreditor);
						if ($arElementCreditor = $rsElementsCreditor->Fetch()) {
						?>
							<div class="creditor-item">
								<p class="creditor-descr">Номер договора</p>
								<div class="divider"></div>
								<span class="creditor-value">
									<?= $arElementCreditor["PROPERTY_NUMBER_VALUE"] ?>
								</span>
							</div>
							<div class="creditor-item">
								<p class="creditor-descr">Дата договора</p>
								<div class="divider"></div>
								<span class="creditor-value"><?= $arElementCreditor["PROPERTY_DATE_VALUE"] ?></span>
							</div>
						<?}
						unset($arElementCreditor);
						?>
						</div>
					<?
						$index++;
					}?>
					<? if(checkAccess(2)): ?>
					<button class="btn btn-primary" data-bs-target="#creditorModal" data-bs-toggle="modal">Добавить кредитора</button>
					<? endif; ?>
				</div>
			</div>
			<div class="col-6">
				<div class="block">
					<h3 class="title">График платежей</h3>
					<div class="info">
						<div>Сумма договора</div><div></div><div><?= $arResult["PROPERTIES"]["CONTRACT_AMOUNT"]["VALUE"] ?> руб</div>
					</div>
					<div class="info">
						<div>Срок договора</div><div></div><div><?= $arResult["PROPERTIES"]["CONTRACT_TERM"]["VALUE"] ?></div>
					</div>
					<div class="info">
						<div>Отсрочка</div><div></div><div><?= $arResult["PROPERTIES"]["GRACE_PERIOD"]["VALUE"] ?></div>
					</div>
					<div class="info">
						<div>Предоплата</div><div></div><div><?= $arResult["PROPERTIES"]["ADVANCE_PAYMENT"]["VALUE"] ?></div>
					</div>
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
							?>
							<form method="post" action="/ajax/contracts/payment/add_payment.php" class="graph-form">
								<input  type="text" name="contract_id" value="<?= $arResult["ID"] ?>" hidden>
								<tr class="create-graph">
									<td></td>
									<td><input style="width: 150px" class="form-control" name="date" type="date"></td>
									<td><input style="width: 150px" class="form-control" name="sum" type="text"></td>
									<td></td>
									<td>
										<select style="width: 150px" class="form-select" name="status-service">
										<?
											$property_enumsStatusService = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblockPayments["ID"], "CODE" => "STATUS_SERVICE"));
											while($enum_fieldsStatusService = $property_enumsStatusService->GetNext())
											{?>
												<option value="<?= $enum_fieldsStatusService["ID"] ?>"><?= $enum_fieldsStatusService["VALUE"] ?></option>
											<?} 
										?>
										</select>
									</td>
									<td><input style="width: 150px" class="form-control" name="postage" type="text"></td>
									<td></td>
									<td>
										<select style="width: 150px" class="form-select" name="status-post">
											<?
											$property_enumsStatusPost = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblockPayments["ID"], "CODE" => "STATUS_POST"));
											while($enum_fieldsStatusPost = $property_enumsStatusPost->GetNext())
											{?>
												<option value="<?= $enum_fieldsStatusPost["ID"] ?>"><?= $enum_fieldsStatusPost["VALUE"] ?></option>
											<?} ?>
										</select>
									</td>
									<input class="sub-btn-main" type="submit" style="opacity: 0; visibility: hidden; width: 0">
								</tr>
							</form>
							<?
								$arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_*");
								$arFilter = Array("IBLOCK_ID" => $iblockPayments["ID"], "ID" => $arResult["PROPERTIES"]["PAYMENT"]["VALUE"]);
								$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
								while($ob = $res->GetNextElement()){
									$arPaymentFields = $ob->GetFields();
									$arPayment = $ob->GetProperties();
									?>
										<form method="post" class="form-ajax"	 action="/ajax/contracts/payment_change.php">
											<tr>
												<td>
													<div class="d-flex">
														<input data-id="<?= $arPaymentFields["ID"] ?>" class="form-check-input graph-check me-1" type="checkbox" value="N">
														<input class="form-control ajax" type="date" name="date" value="<?= date("Y-m-d", strtotime($arPayment["DATE"]["VALUE"])) ?>">
													</div>
												</td>
												<td ><input class="form-control ajax" type="text" name="sum" value="<?= $arPayment["SUM"]["VALUE"] ?>" /></td>
												<td class="d-none">
													<? foreach ($arPayment["CHECK_SERVICE"]["VALUE"] as $check): ?>
														<div class="d-flex align-items-center gap-2">
															<?= CFile::GetFileArray($check)["ORIGINAL_NAME"] ?>
															<a style="display: flex; flex-shrink: 0" href="<?= CFile::GetPath($check) ?>" download><img src="/local/templates/crm-my-prava/images/download-icon.png" alt=""></a>
															<form action="/ajax/contracts/payment/delete_check.php" method="post">
																<input type="text" name="payment_id" value="<?= $arPaymentFields["ID"] ?>" hidden>
																<input type="text" name="file_id" value="<?= $check ?>" hidden>
																<input type="text" name="property_code" value="CHECK_SERVICE" hidden>
																<input class="btn p-0 mx-2" type="submit" value="x">
															</form>
														</div>
													<? endforeach; ?>
													<form style="width: 310px" enctype="multipart/form-data" method="post" action="/ajax/contracts/payment/add_check.php">
														<input type="text" name="payment_id" value="<?= $arPaymentFields["ID"] ?>" hidden>
														<input type="text" name="property_code" value="CHECK_SERVICE" hidden>
														<input class="form-control" type="file" name="files[]" multiple>
														<input class="btn btn-secondary mt-2" type="submit" value="Прикрепить">
													</form>
												</td>
												<td>
													<? foreach ($arPayment["CHECK_SERVICE"]["VALUE"] as $check): ?>
														<div class="d-flex align-items-center gap-2">
															<?= CFile::GetFileArray($check)["ORIGINAL_NAME"] ?>
															<a style="display: flex; flex-shrink: 0" href="<?= CFile::GetPath($check) ?>" download><img src="/local/templates/crm-my-prava/images/download-icon.png" alt=""></a>
															<form action="/ajax/contracts/payment/delete_check.php" method="post">
																<input type="text" name="payment_id" value="<?= $arPaymentFields["ID"] ?>" hidden>
																<input type="text" name="file_id" value="<?= $check ?>" hidden>
																<input type="text" name="property_code" value="CHECK_SERVICE" hidden>
																<input class="btn p-0 mx-2" type="submit" value="x">
															</form>
														</div>
													<? endforeach; ?>
													<button class="btn btn-secondary" data-bs-target="#check-service-<?= $arPaymentFields["ID"] ?>" data-bs-toggle="modal">Прикрепить</button>
													<div class="modal fade" id="check-service-<?= $arPaymentFields["ID"] ?>" aria-hidden="true" aria-labelledby="docsForClientLabel" tabindex="-1">
														<div class="modal-dialog modal-dialog-centered">
															<div class="modal-content">
																<div class="modal-body">
																	<h3 class="title" id="docsForClientLabel">Загрузить документ</h3>
																	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
																	<form style="width: 310px" enctype="multipart/form-data" method="post" action="/ajax/contracts/payment/add_check.php">
																		<input type="text" name="payment_id" value="<?= $arPaymentFields["ID"] ?>" hidden>
																		<input type="text" name="property_code" value="CHECK_SERVICE" hidden>
																		<input class="form-control" type="file" name="files[]" multiple>
																		<input class="btn btn-secondary mt-2" type="submit" value="Прикрепить">
																	</form>
																</div>
															</div>
														</div>
													</div>
												</td>
												<td>
													<select style="width: 150px" class="form-select ajax" name="status-service">
															<?
															$property_enumsStatusService = CIBlockPropertyEnum::GetList(Array("ID"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblockPayments["ID"], "CODE" => "STATUS_SERVICE"));
															while($enum_fieldsStatusService = $property_enumsStatusService->GetNext())
															{?>
																	<option value="<?= $enum_fieldsStatusService["ID"] ?>" <?= $arPayment["STATUS_SERVICE"]["VALUE_ENUM_ID"] == $enum_fieldsStatusService["ID"] ? "selected" : "" ?>><?= $enum_fieldsStatusService["VALUE"] ?></option>
															<?}
															?>
													</select>
												</td>
												<td><input class="form-control ajax" type="text" name="postage" value="<?= $arPayment["POSTAGE"]["VALUE"] ?>"></td>
												<td>
													<? foreach ($arPayment["CHECK_POST"]["VALUE"] as $check): ?>
														<div class="d-flex align-items-center gap-2">
															<?= CFile::GetFileArray($check)["ORIGINAL_NAME"] ?>
															<a style="display: flex; flex-shrink: 0" href="<?= CFile::GetPath($check) ?>" download><img src="/local/templates/crm-my-prava/images/download-icon.png" alt=""></a>
															<? if(checkAccess(2)): ?>
																<form action="/ajax/contracts/payment/delete_check.php" method="post">
																	<input type="text" name="payment_id" value="<?= $arPaymentFields["ID"] ?>" hidden>
																	<input type="text" name="file_id" value="<?= $check ?>" hidden>
																	<input type="text" name="property_code" value="CHECK_POST" hidden>
																	<input class="btn p-0 mx-2" type="submit" value="x">
																</form>
															<? endif; ?>
														</div>
													<? endforeach; ?>
													<button class="btn btn-secondary" data-bs-target="#check-post-<?= $arPaymentFields["ID"] ?>" data-bs-toggle="modal">Прикрепить</button>
													<div class="modal fade" id="check-post-<?= $arPaymentFields["ID"] ?>" aria-hidden="true" aria-labelledby="docsForClientLabel" tabindex="-1">
														<div class="modal-dialog modal-dialog-centered">
															<div class="modal-content">
																<div class="modal-body">
																	<h3 class="title" id="docsForClientLabel">Загрузить документ</h3>
																	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
																	<form style="width: 310px" enctype="multipart/form-data" method="post" action="/ajax/contracts/payment/add_check.php">
																		<input type="text" name="payment_id" value="<?= $arPaymentFields["ID"] ?>" hidden>
																		<input type="text" name="property_code" value="CHECK_POST" hidden>
																		<input class="form-control" type="file" name="files[]" multiple>
																		<input class="btn btn-secondary mt-2" type="submit" value="Прикрепить">
																	</form>
																</div>
															</div>
														</div>
													</div>
												</td>
												<td>
													<select style="width: 150px" class="form-select ajax" name="status-post">
															<?
															$property_enumsStatusPost = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblockPayments["ID"], "CODE" => "STATUS_POST"));
															while($enum_fieldsStatusPost = $property_enumsStatusPost->GetNext())
															{?>
																	<option value="<?= $enum_fieldsStatusPost["ID"] ?>" <?= $arPayment["STATUS_POST"]["VALUE_ENUM_ID"] == $enum_fieldsStatusService["ID"] ? "selected" : "" ?>><?= $enum_fieldsStatusPost["VALUE"] ?></option>
															<?} ?>
													</select>
												</td>
											</tr>
										</form>
									<?
								}
							?>
						</table>
					</div>
					<? if(checkAccess(2)): ?>
						<div class="d-flex align-items-center">
							<button type="button" class="btn btn-primary mt-3 me-2 add-btn">Добавить</button>
							<input class="btn btn-primary mt-3 me-2 sub-btn" type="submit">
							<button type="button" class="btn btn-danger mt-3 del-btn">Удалить</button>
						</div>
					<? endif; ?>
				</div>
			</div>
		</div>
	</div>
</main>


</div>

<div class="modal fade" id="editModal" aria-hidden="true" aria-labelledby="policy" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered" style="max-width: 1324px">
		<div class="modal-content">
			<div class="modal-body">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				<form method="post" action="/ajax/contracts/save_client_data.php">
					<input type="text" hidden name="client_id" value="<?= $arClient["ID"] ?>">
					<div>
						<div class="inputs-group">
							<h4 class="title-secondary"><?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_DATA") ?></h4>
							<div class="row">
								<div class="col-3">
									<label class="form-label" for="last_name">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_LAST") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="last_name" id="last_name" value="<?= $arClient["LAST_NAME"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="first_name">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_FIRST") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="first_name" id="first_name" value="<?= $arClient["NAME"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="second_name">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_SECOND") ?>
									</label>
									<input class="form-control" type="text" name="second_name" id="second_name" value="<?= $arClient["SECOND_NAME"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="place_birth">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_PLACE") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="place_birth" id="place_birth" value="<?= $arClient["UF_PLACE_BIRTH"] ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<label class="form-label" for="place_birth">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_DATE") ?><span>*</span>
									</label>
									<input class="form-control" type="date" name="date_birth" id="date_birth" value="<?= date("Y-m-d", strtotime($arClient["PERSONAL_BIRTHDAY"])) ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="email">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_EMAIL") ?><span>*</span>
									</label>
									<input class="form-control" type="email" name="email" id="email" value="<?= $arClient["EMAIL"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="employment_and_income">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_EMPLOYMENT_AND_INCOME") ?>
									</label>
									<input class="form-control" type="text" name="employment_and_income" id="employment_and_income" value="<?= $arClient["UF_EMPLOYMENT_INCOME"] ?>">
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<label class="form-label" for="property_presence">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_PROPERTY_PRESENCE") ?>
									</label>
									<input class="form-control" type="text" name="property_presence" id="property_presence" value="<?= $arClient["UF_OWNERSHIP"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="transactions_three_year_period">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_TRANSACTIONS_THREE_YEAR_PERIOD") ?>
									</label>
									<input class="form-control" type="text" name="transactions_three_year_period" id="transactions_three_year_period" value="<?= $arClient["UF_THREE_YEAR_TRANSACTIONS"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="expected_client_result">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_EXPECTED_CLIENT_RESULT") ?>
									</label>
									<input class="form-control" type="text" name="expected_client_result" id="expected_client_result" value="<?= $arClient["UF_CUSTOMER_EXPECTED_OUTCOME"] ?>">
								</div>
							</div>
						</div>
						<div class="inputs-group">
							<h4><?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_DATA") ?></h4>
							<div class="row">
								<div class="col-3">
									<label class="form-label" for="series">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_SERIES") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="series" id="series" value="<?= $arClient["UF_SERIES"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="snils">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_SNILS") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="snils" id="snils" value="<?= $arClient["UF_SNILS"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="date_issued">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_DATE_ISSUED") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="date_issued" id="date_issued" value="<?= $arClient["UF_DATE_ISSUED"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="residence_address">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_RESIDENCE_ADDRESS") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="residence_address" id="residence_address" value="<?= $arClient["UF_RESIDENCE_ADDRESS"] ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<label class="form-label" for="issued_by">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_ISSUED_BY") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="issued_by" id="issued_by" value="<?= $arClient["UF_ISSUING_AUTHORITY"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="number">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_NUMBER") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="number" id="number" value="<?= $arClient["UF_NUMBER"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="inn">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_INN") ?><span>*</span>
									</label>
									<input class="form-control" type="text" name="inn" id="inn" value="<?= $arClient["UF_INN"] ?>" required>
								</div>
								<div class="col-3">
									<label class="form-label" for="phone">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_PHONE") ?><span>*</span>
									</label>
									<input class="form-control" type="tel" name="phone" id="phone" value="<?= $arClient["PERSONAL_PHONE"] ?>" required>
								</div>
							</div>
						</div>
						<div class="inputs-group">
							<h4><?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_DATA") ?></h4>
							<div class="row">
								<div class="col-3">
									<label class="form-label" for="guarantors_cobligors">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_GUARANTORS_COBLIGORS") ?>
									</label>
									<input class="form-control" type="text" name="guarantors_cobligors" id="guarantors_cobligors" value="<?= $arClient["UF_COSIGNERS_COBORROWERS"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="delinquencies_at_contract">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_DELINQUENCIES_AT_CONTRACT") ?>
									</label>
									<input class="form-control" type="text" name="delinquencies_at_contract" id="delinquencies_at_contract" value="<?= $arClient["UF_PREEXISTING_DELINQUENCIES"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="overdue_mfo">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_OVERDUE_MFO") ?>
									</label>
									<input class="form-control" type="text" name="overdue_mfo" id="overdue_mfo" value="<?= $arClient["UF_MFOS_DEBTS"] ?>">
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<label class="form-label" for="overdue_housing_services">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_OVERDUE_HOUSING_SERVICES") ?>
									</label>
									<input class="form-control" type="text" name="overdue_housing_services" id="overdue_housing_services" value="<?= $arClient["UF_UTILITY_ARREARS"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="creditors_number">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_CREDITORS_NUMBER") ?>
									</label>
									<input class="form-control" type="text" name="creditors_number" id="creditors_number" value="<?= $arClient["UF_CREDITOR_COUNT"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="judicial_acts">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_JUDICIAL_ACTS") ?>
									</label>
									<input class="form-control" type="text" name="judicial_acts" id="judicial_acts" value="<?= $arClient["UF_JUDGMENT_DECREES"] ?>">
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<label class="form-label" for="total_encumbrance_amount">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_TOTAL_ENCUMBRANCE_AMOUNT") ?>
									</label>
									<input class="form-control" type="text" name="total_encumbrance_amount" id="total_encumbrance_amount" value="<?= $arClient["UF_COLLATERAL_VALUE"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="enforcement_proceedings">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_ENFORCEMENT_PROCEEDINGS") ?>
									</label>
									<input class="form-control" type="text" name="enforcement_proceedings" id="enforcement_proceedings" value="<?= $arClient["UF_EXECUTION_PROCEEDINGS"] ?>">
								</div>
								<div class="col-3">
									<label class="form-label" for="tax_liabilities">
										<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_TAX_LIABILITIES") ?>
									</label>
									<input class="form-control" type="text" name="tax_liabilities" id="tax_liabilities" value="<?= $arClient["UF_TAX_OBLIGATIONS"] ?>">
								</div>
							</div>
						</div>
						<div>
							<div class="row">
								<div class="col-6">
									<label class="form-label" for="notes">
										<?= GetMessage("TITLE_TAB_CLIENT_TEXTAREA_NOTES") ?>
									</label>
									<textarea class="form-control" name="notes" id="notes"><?= $arClient["UF_NOTES"] ?></textarea>
								</div>
							</div>
							<div class="row">
								<div class="col-6">
									<label class="form-label" for="change_history">
										<?= GetMessage("TITLE_TAB_CLIENT_TEXTAREA_CHANGE_HISTORY") ?>
									</label>
									<textarea class="form-control" name="change_history" id="change_history"><?= $arClient["UF_CHANGE_HISTORY"] ?></textarea>
								</div>
							</div>
							<div class="row">
								<div class="col-6">
									<label class="form-label" for="work_plan">
										<?= GetMessage("TITLE_TAB_CLIENT_TEXTAREA_WORK_PLAN") ?>
									</label>
									<textarea class="form-control" name="work_plan" id="work_plan"><?= $arClient["UF_WORK_PLAN"] ?></textarea>
								</div>
							</div>
						</div>
					</div>
					<button class="btn btn-primary mt-2" type="submit">Сохранить</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="creditorModal" aria-hidden="true" aria-labelledby="policy" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				<form class="d-flex flex-column align-items-start gap-3" method="post" action="/ajax/contracts/add_creditor.php">
					<h3 class="title-secondary">Список кредиторов</h3>
					<div class="d-flex align-items-center gap-2">
						<div style="width: 60%">
							<input type="text" name="contract_id" value="<?= $arResult["ID"] ?>" hidden>
							<label class="form-label" for="creditor">Наименование</label>
							<select class="form-select" id="creditor" name="creditor">
								<?
								$iblockCreditors = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'creditors'], true)->Fetch();
								if ($iblockCreditors) {
									$iblockId = $iblockCreditors['ID'];
		
									$arSelect = array("ID", "NAME");
									$arFilter = array("IBLOCK_ID" => $iblockId);
									$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		
									while ($ob = $res->GetNextElement()) {
										$arFields = $ob->GetFields();
										?>
										<option value="<?= $arFields["ID"] ?>"><?= $arFields["NAME"] ?></option>
									<?
									}
								}
								?>
							</select>
						</div>
						<div>
							<label class="form-label" for="number">Номер договора</label>
							<input class="form-control" id="number" type="number" name="number">
						</div>
						<div>
							<label class="form-label" for="date">Дата договора</label>
							<input class="form-control" id="date" type="date" name="date">
						</div>
					</div>
					<input class="btn btn-primary align-self-center" type="submit" value="Сохранить">
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	addEventListener("DOMContentLoaded", () => {
		document.querySelector('.add-btn').addEventListener('click', () => {
			document.querySelector('.add-btn').style.display = 'none'
			document.querySelector('.create-graph').style.display = 'table-row'
			document.querySelector('.sub-btn').style.display = 'block'
		})
	});

	$(function() {
		$('.form-reload').each(function() {
			$(this).submit(function(e) {
				var $form = $(this);
				$.ajax({
					type: $form.attr('method'),
					url: $form.attr('action'),
					data: $form.serialize()
				}).done(function(response) {
					result = jQuery.parseJSON(response)
					if(result) {
						location.reload()
					}
				}).fail(function() {
					console.log('fail');
				});

				e.preventDefault(); 
			});
		})

		$('.sub-btn').on('click', function() {
			$('.sub-btn-main').trigger('click')
		})

		$('.del-btn').on('click', function() {
			let arrId = []
			$('.graph-check:checked').each(function() {
				let id = $(this).attr('data-id')
				arrId.push(id)
			})

			$.ajax({
				type: "POST",
				url: '/ajax/contracts/payment/delete_payment.php',
				data: {payment_id: arrId }
			}).done(function(response) {
				console.log(response)
				location.reload()
			}).fail(function() {
				console.log('fail');
			});
		})

		$('.ajax').each(function() {
			$(this).on('change', function() {
				console.log('fwfewf')
				$('.form-ajax').submit()
			})
  	})	
	});
</script>
