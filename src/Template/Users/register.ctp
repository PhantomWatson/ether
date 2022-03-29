<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $title_for_layout
 */

use Cake\Core\Configure;

$this->Html->script('https://www.google.com/recaptcha/api.js', ['block' => true]);
$this->Html->script('/jscolor/jscolor.js', ['block' => true]);
?>
<?php $this->append('buffered_js'); ?>
    registration.init();
<?php $this->end(); ?>

<script>
    function onSubmit(token) {
        document.getElementById('register').submit();
    }
</script>

<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<?= $this->Form->create($user, ['id' => 'register']) ?>
<div class="content_box">
    <div class="form-group required">
        <label for="thoughtword-captcha">
            <strong>
                Spam Bot Check
            </strong>
            <br />
            Check the "Recent" section of the front page and enter <strong>the most recent thoughtword</strong> to have
            a thought or comment added to it.
        </label>
        <input type="text" id="thoughtword-captcha" name="thoughtword-captcha" class="form-control"
               required="required" />
        <?php if ($captchaError ?? false): ?>
            <div class="error-message">
                Invalid response. Please check the front page and try again.
            </div>
        <?php endif; ?>
        <p class="footnote">
            Ether gets a lot of spam bots trying to register accounts, so this is a check to confirm that you're
            a real person. Just go to the front page and look for the first word under "Recent". Need help? Try the
            <?= $this->Html->link('contact page', ['controller' => 'Pages', 'action' => 'contact']) ?>.
        </p>
    </div>
</div>

<div class="content_box">
    <?php
        echo $this->Form->control(
            'email',
            [
                'class' => 'form-control',
                'placeholder' => 'Enter your email address',
                'div' => [
                    'class' => 'form-group'
                ]
            ]
        );
        echo $this->Form->control(
            'new_password',
            [
                'label' => 'Password',
                'type' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Enter your password',
                'div' => [
                    'class' => 'form-group'
                ]
            ]
        );
        echo $this->Form->control(
            'confirm_password',
            [
                'type' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Enter your password',
                'div' => [
                    'class' => 'form-group'
                ]
            ]
        );
    ?>

    <div class="input form-group" id="reg_color_input">
        <label for="color_hex">
            Color
        </label>
        <div class="evaluation_message"></div>
        <input type="text" size="7" maxlength="7" name="color" id="color_hex" value="<?= $this->request->getData('color') ?>" class="form-control color" />
        <?php if (isset($randomColor)): ?>
            <div class="footnote">
                We've pre-selected a random color for you, but feel free to change it.
            </div>
        <?php endif; ?>
    </div>

    <?php
        echo $this->Form->submit(
            'Register',
            [
                'class' => 'btn btn-primary g-recaptcha',
                'data-sitekey' => Configure::read('Recaptcha.sitekey'),
                'data-callback' => 'onSubmit',
                'data-action' => 'submit',
            ]
        );
    ?>
</div>
<?= $this->Form->end() ?>
