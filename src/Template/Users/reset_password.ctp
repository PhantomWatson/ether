<div class="page-header">
    <h1>
        <?= $titleForLayout ?>
    </h1>
</div>

<p>
    Please enter your new password below. Afterward, you will be able to log in
    using this password and the email address <strong><?= $email ?></strong>.
</p>

<?php
    echo $this->Form->create($user);
    echo $this->Form->input(
        'new_password',
        [
            'autocomplete' => 'off',
            'class' => 'form-control',
            'div' => ['class' => 'form-group'],
            'label' => 'Change password',
            'type' => 'password'
        ]
    );
    echo $this->Form->input(
        'confirm_password',
        [
            'autocomplete' => 'off',
            'class' => 'form-control',
            'div' => ['class' => 'form-group'],
            'label' => 'Repeat new password',
            'type' => 'password'
        ]
    );
    echo $this->Form->button(
        'Reset Password',
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();