<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<div class="content_box">
    <?php
        echo $this->Form->create($user);
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

    <div class="input">
        <label>
            Human?
        </label>
        <?php
            try {
                echo $this->Recaptcha->display();
            } catch (Exception $e) {
                echo '<div class="error-message">Error displaying CAPTCHA challenge</div>';
            }
        ?>
        <?php if (isset($recaptchaError)): ?>
            <div class="error-message">
                Invalid CAPTCHA response. Please try again.
            </div>
        <?php endif; ?>
    </div>

    <?php
        echo $this->Form->submit(
            'Register',
            ['class' => 'btn btn-default']
        );
        echo $this->Form->end();
    ?>
</div>

<?php $this->Html->script('/jscolor/jscolor.js', ['block' => true]); ?>
<?php $this->append('buffered_js'); ?>
    registration.init();
<?php $this->end(); ?>
