<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
CModule::IncludeModule("main");
CModule::IncludeModule("security");


function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if (isset($_POST['user_id']) && isset($_POST["old_password"]) && isset($_POST["new_password"])) {
	$userId = sanitizeInput(intval($_POST["user_id"]));
	$oldPassword = sanitizeInput($_POST['old_password']);
	$newPassword = sanitizeInput($_POST['new_password']);

	$user = CUser::GetByID($userId)->Fetch();

	if ($user) {
		if (\Bitrix\Main\Security\Password::equals($user['PASSWORD'], $oldPassword)) {
			// Проверка условий нового пароля
			if (preg_match("/^[a-zA-Z0-9]{10,}$/", $newPassword)) {
				$userFields = array("PASSWORD" => $newPassword, "CONFIRM_PASSWORD" => $newPassword);
				$userUpdateResult = $USER->Update($userId, $userFields);
				if ($userUpdateResult) {
					$response = array(
						"success" => true,
						"message" => "Успешно! Пароль изменен",
					);
				} else {
					$response = array(
						"success" => false,
						"message" => "Ошибка при изменении пароля: " . $userUpdateResult->LAST_ERROR
					);
				}
			} else {
				$response = array(
					"success" => false,
					"message" => "Новый пароль не соответствует условиям"
				);
			}
		} else {
			$response = array(
				"success" => false,
				"message" => "Старый пароль неверен",
			);
		}
	} else {
		$response = array(
			"success" => false,
			"message" => "Пользователь не найден.",
		);
	}
} else {
	$response = array(
		"success" => false,
		"message" => "Отсутствуют необходимые параметры",
	);
}

// echo Json::encode($response, JSON_UNESCAPED_UNICODE);
?>
<script>
	history.back()
</script>
