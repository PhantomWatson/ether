<div class="page-header">
    <h1>
        <?= $titleForLayout ?>
    </h1>
</div>

<p>
    Have you forgotten the password that you use to log in to your account?
    That's alright. It happens to the best of us. Case in point, Superman is notorious for not
    being able to remember his password for more than a couple weeks at a time.
    In the field below, enter the email address that is associated with your account,
    and you'll be emailed a link that you can use for the next 24 hours to reset your password.
</p>

<?php
    echo $this->Form->create($user);
    echo $this->Form->input(
        'email',
        [
            'class' => 'form-control',
            'div' => ['class' => 'form-group']
        ]
    );
    echo $this->Form->button(
        'Reset Password',
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();
?>
