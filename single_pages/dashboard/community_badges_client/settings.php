<?php

/**
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var string $endpoint */
/** @var string $clientId */
/** @var string $clientSecret */
/** @var string $redirectUrl */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<form action="<?=$view->action('submit')?>" method="post">
    <?php echo $token->output("update_settings"); ?>

    <fieldset>
        <legend>
            <?php echo t("API"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("endpoint", t("Endpoint")); ?>
            <?php echo $form->text("endpoint", $endpoint, ["placeholder" => "e.g. example.com"]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("clientId", t("Client ID")); ?>
            <?php echo $form->text("clientId", $clientId); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("clientSecret", t("Client Secret")); ?>
            <?php echo $form->password("clientSecret", $clientSecret); ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </div>
</form>

<?php if ($showTestConnectionButton) { ?>
<form action="<?=$view->action('test_connection')?>" method="post">
    <?php echo $token->output("test_connection"); ?>
    <fieldset>
        <legend><?=t('Test Connection')?></legend>
        <button class="btn-block btn btn-outline-secondary" type="submit" name="testConnection"><?=t('Click to test your connection to the badges API.')?></button>
    </fieldset>
</form>
<?php } ?>
