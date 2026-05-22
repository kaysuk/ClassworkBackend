<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="work_slider.css">
    <link rel="stylesheet" href="footer_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="icon" href="logo.ico" type="image/x-icon">
    <title>Drupal-Coder</title>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <link rel="stylesheet" href="style.css">

    <script type="text/x-template" id="modal-template">
      <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                        <div class="col-md-6 col-xs-12 col-sm-6">
                            <div class="field field--name-field-block-with-form-value field--type-entity-reference field--label-hidden field--item">
                                    <form id="contactForm" action="./index.php" method="post" enctype="multipart/form-data" class="contact-message-order-support-form contact-message-form contact-form antibot popup_form" >
                                        <button style="width:30px; height:30px; text-align: center; margin-bottom:20px;" class="modal-default-button" @click="$emit('close')">
                                            X
                                        </button>
                                        <div class="field--type-string field--name-field-vashe-imya field--widget-string-textfield form-group js-form-wrapper form-wrapper">      
                                            <div class="form-item js-form-item form-type-textfield js-form-type-textfield form-item-field-vashe-imya-0-value form-group">
                                                <input class="js-text-full text-full form-text form-control"  type="text" id="edit-field-vashe-imya-0-value" name="field_vashe_imya[0][value]" value="" size="60" maxlength="255" placeholder="Ваше имя">
                                            </div>
                                        </div>
                                        <div class="field--type-string field--name-field-telefon field--widget-string-textfield form-group js-form-wrapper form-wrapper">      
                                            <div class="form-item js-form-item form-type-textfield js-form-type-textfield form-item-field-telefon-0-value form-group">
                                                <input class="js-text-full text-full form-text required form-control" data-drupal-selector="edit-field-telefon-0-value" type="text" id="edit-field-telefon-0-value" name="field_telefon[0][value]" value="" size="60" maxlength="255" placeholder="Телефон" required="required" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="field--type-email field--name-field-e-mail field--widget-email-default form-group js-form-wrapper form-wrapper">      
                                            <div class="form-item js-form-item form-type-email js-form-type-email form-item-field-e-mail-0-value form-group">
                                                <input data-drupal-selector="edit-field-e-mail-0-value" class="form-email required form-control" type="email" id="edit-field-e-mail-0-value" name="field_e_mail[0][value]" value="" size="60" maxlength="254" placeholder="E-mail" required="required" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="field--type-string-long field--name-field-vash- field--widget-string-textarea form-group js-form-wrapper form-wrapper">      
                                            <div class="form-item js-form-item form-type-textarea js-form-type-textarea form-item-field-vash--0-value form-group">
                                                <div class="form-textarea-wrapper">
                                                    <textarea class="js-text-full text-full form-textarea form-control resize-vertical" data-drupal-selector="edit-field-vash-0-value" id="edit-field-vash-0-value" name="field_vash_[0][value]" rows="5" cols="60" placeholder="Ваш комментарий"></textarea>
                                                </div>
                                            </div>

                                            <div class="form-item js-form-item form-type-checkbox js-form-type-checkbox form-item-fz152-agreement checkbox">
                                                <label for="edit-fz152-agreement" class="control-label option js-form-required form-required">
                                                    <input required="required" data-drupal-selector="edit-fz152-agreement" class="form-checkbox required" type="checkbox" id="edit-fz152-agreement" name="fz152_agreement" value="1" aria-required="true">
                                                    <span class="checkmark"></span>
                                                    Отправляя заявку, я даю согласие на 
                                                    <a href="/privacy-policy" target="_blank" rel="nofollow">обработку своих персональных данных</a>.
                                                </label>
                                            </div>
                                            <div class="g-recaptcha" data-sitekey="6LdePk4pAAAAAP8itF6Aidg0o7vhDRU3osEqyHu3">
                                            </div>
                                            <div data-drupal-selector="edit-actions" class="form-actions form-group js-form-wrapper form-wrapper" id="edit-actions">
                                                <button  class="button button--primary js-form-submit form-submit btn-primary btn"  id="edit-submit" type="submit" value="Свяжитесь с нами">Свяжитесь с нами</button>
                                            </div>
                                    </form>
                            </div>
                        </div>
                </div>
            </div>
        </div>
      </transition>
    </script>
</head>
<body>
    <?php
    // Render the content based on the current module
    if (!empty($c['#content']['admin'])) {
      echo $c['#content']['admin'];
    } elseif (!empty($c['#content']['front'])) {
      echo $c['#content']['front'];
    }
    ?>
</body>
</html>
