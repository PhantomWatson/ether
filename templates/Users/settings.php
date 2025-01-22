<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div id="account">
    <div class="row">
        <div class="offset-sm-3 col-sm-6">
            <h2>
                Introspection
            </h2>
            <p>
                Here, you're invited to write about yourself. This text will appear on
                <?= $this->Html->link(
                    'your profile page',
                    ['controller' => 'Users', 'action' => 'myProfile']
                ) ?>.
            </p>
            <?php
                echo $this->Form->create($user);
                echo $this->Form->control(
                    'profile',
                    [
                        'class' => 'form-control',
                        'label' => false,
                        'placeholder' => 'Share something about yourself.'
                    ]
                );
                echo $this->Form->control(
                    'action',
                    [
                        'type' => 'hidden',
                        'value' => 'introspection'
                    ]
                );
                echo $this->Form->submit(
                    'Update',
                    ['class' => 'btn btn-primary']
                );
                echo $this->Form->end();
            ?>
        </div>
    </div>

    <div class="row">
        <div class="offset-sm-3 col-sm-6">
            <h2>
                Change Password
            </h2>
            <?php
                echo $this->Form->create(
                    $user,
                    ['id' => 'UserChangePasswordForm']
                );
                echo $this->Form->control(
                    'new_password',
                    [
                        'autocomplete' => 'off',
                        'class' => 'form-control',
                        'div' => ['class' => 'form-group'],
                        'label' => 'Password',
                        'placeholder' => 'Enter your new password',
                        'type' => 'password',
                        'value' => ''
                    ]
                );
                echo $this->Form->control(
                    'confirm_password',
                    [
                        'autocomplete' => 'off',
                        'class' => 'form-control',
                        'div' => ['class' => 'form-group'],
                        'type' => 'password',
                        'placeholder' => 'Wasn\'t that fun? Do it again.',
                        'value' => ''
                    ]
                );
                echo $this->Form->control(
                    'action',
                    [
                        'type' => 'hidden',
                        'value' => 'change_password'
                    ]
                );
                echo $this->Form->submit(
                    'Change password',
                    ['class' => 'btn btn-primary']
                );
                echo $this->Form->end();
            ?>
        </div>
    </div>

    <div class="row">
        <div class="offset-sm-3 col-sm-6">
            <h2>
                Settings
            </h2>
            <?php
                echo $this->Form->create(
                    $user,
                    ['id' => 'UserAccountOptionsForm']
                );
                echo $this->Form->control(
                    'acceptMessages',
                    [
                        'label' => 'Allow other users to send me messages',
                        'type' => 'checkbox'
                    ]
                );
                echo $this->Form->control(
                    'messageNotification',
                    [
                        'label' => 'Notify me of new messages via email',
                        'type' => 'checkbox'
                    ]
                );
                echo $this->Form->control(
                    'emailUpdates',
                    [
                        'label' => 'Send me updates about Ether via email',
                        'type' => 'checkbox'
                    ]
                );
                echo $this->Form->control(
                    'action',
                    [
                        'type' => 'hidden',
                        'value' => 'options'
                    ]
                );
                echo $this->Form->submit(
                    'Update',
                    ['class' => 'btn btn-primary']
                );
                echo $this->Form->end();
            ?>
        </div>
    </div>
</div>
