<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div id="content_title">
    <h1>
        Log in
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
            'password',
            [
                'class' => 'form-control',
                'placeholder' => 'Enter your password',
                'div' => [
                    'class' => 'form-group'
                ]
            ]
        );
        echo $this->Form->submit(
            'Log in',
            [
                'class' => 'btn btn-primary'
            ]
        );
        echo $this->Form->end();
    ?>

    <p>
        <?= $this->Html->link(
            'I forgot my password',
            [
                'controller' => 'Users',
                'action' => 'forgotPassword'
            ]
        ) ?>
    </p>
</div>
