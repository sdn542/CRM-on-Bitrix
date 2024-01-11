<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
IncludeTemplateLangFile(__FILE__);
?>
  <div class="modal fade" id="policyModal" aria-hidden="true" aria-labelledby="policy" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
          <? if(!empty($headerData["PRIVACY_POLICY"]["~VALUE"]["TEXT"])): ?>
            <?= $headerData["PRIVACY_POLICY"]["~VALUE"]["TEXT"] ?>
          <? endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade auth-modal" id="resetPassword" aria-hidden="true" aria-labelledby="resetPasswordLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
          <form id="reset-form" class="d-flex flex-column" action="/ajax/reset_password.php" method="post">
            <h3 class="modal-title text-center mb-3"><?= GetMessage("FORM_RESET_PASSWORD_TITLE") ?></h3>
            <label class="form-label" for="email">
              <?= GetMessage("FORM_RESET_PASSWORD_LABEL_EMAIL") ?><span>*</span>
            </label>
            <input class="form-control mb-4" type="email" name="email" required>
            <button class="btn btn-primary m-auto" type="submit"><?= GetMessage("FORM_RESET_PASSWORD_NAME_BUTTON") ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade auth-modal" id="changePassword" aria-hidden="true" aria-labelledby="changePasswordLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>

          <form id="changeForm" class="d-flex flex-column" action="/ajax/change_password.php" method="post">
            <h3 class="modal-title text-center mb-3"><?= GetMessage("FORM_CHANGE_PASSWORD_TITLE") ?></h3>
            <label class="form-label" for="old_password">
              <?= GetMessage("FORM_CHANGE_PASSWORD_LABEL_OLD_PASSWORD") ?><span>*</span>
            </label>
            <input class="form-control" type="password" name="old_password" required>
            <label class="form-label" for="new_password">
              <?= GetMessage("FORM_CHANGE_PASSWORD_LABEL_NEW_PASSWORD") ?><span>*</span>
            </label>
            <input class="form-control" type="password" name="new_password" required>
            <label class="form-label" for="confirm_new_password">
              <?= GetMessage("FORM_CHANGE_PASSWORD_LABEL_CONFIRM_NEW_PASSWORD") ?><span>*</span>
            </label>
            <input class="form-control mb-3" type="password" name="confirm_new_password" required>
            <input type="text" name="user_id" value="<?= $USER->GetID(); ?>" hidden required>
            <button class="btn btn-primary m-auto" type="submit"><?= GetMessage("FORM_CHANGE_PASSWORD_NAME_BUTTON") ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade auth-modal" id="alertModal" aria-hidden="true" aria-labelledby="alertModalLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
          <div id="alert-body" class="title-secondary text-center"></div> 
        </div>
      </div>
    </div>
  </div>
  <button style="position: absolute; top: 0; opacity: 0; visibility: hidden;" id="alert-btn" data-bs-target="#alertModal" data-bs-toggle="modal" class="btn btn-primary m-auto"></button>
  <script>
    $(function() {
      $('#reset-form').submit(function(e) {
        var $form = $(this);
        $.ajax({
          type: $form.attr('method'),
          url: $form.attr('action'),
          data: $form.serialize()
        }).done(function(response) {
          result = jQuery.parseJSON(response)
          if(result) {
            $('#alert-btn').trigger('click')
            $('#alert-body').html(result.message)
          }
        }).fail(function() {
          console.log('fail');
        });

        e.preventDefault(); 
      });
    });

    $(function() {
      $('#changeForm').submit(function(e) {
        var $form = $(this);
        $.ajax({
          type: $form.attr('method'),
          url: $form.attr('action'),
          data: $form.serialize()
        }).done(function(response) {
          result = jQuery.parseJSON(response)
          if(result) {
            $('#alert-btn').trigger('click')
            $('#alert-body').html(result.message)
          }
        }).fail(function() {
          console.log('fail');
        });

        e.preventDefault(); 
      });
    });
  </script>
	</body>
</html>