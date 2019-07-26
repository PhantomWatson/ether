<?php
namespace App\Controller;

use App\Color\Color;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Recaptcha\Controller\Component\RecaptchaComponent;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\MessagesTable $Messages
 * @property RecaptchaComponent $Recaptcha
 */
class UsersController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'checkColorAvailability',
            'forgotPassword',
            'index',
            'login',
            'register',
            'resetPassword',
            'view'
        ]);

        if ($this->request->action === 'register') {
            $this->loadComponent('Recaptcha.Recaptcha');
        }

        $this->loadComponent('RequestHandler');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set([
            'title_for_layout' => 'Thinkers',
            'colors' => $this->Users->getColorsWithThoughts(),
        ]);
    }

    /**
     * View method
     *
     * @param string|null $color User color
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($color = null)
    {
        $user = $this->Users->getProfileInfo($color);
        $this->loadModel('Messages');
        $this->set([
            'title_for_layout' => "Thinker #$color",
            'user' => $user,
            'colors' => $this->Users->getColorsWithThoughts(),
            'selectedColor' => $color,
            'colorName' => (new Color())->getClosestXkcdColor($color)
        ]);

        $userId = $this->Auth->user('id');
        $selectedUserId = $this->Users->getIdFromColor($color);
        if ($userId) {
            $this->set('messagesCount', $this->Messages->getConversationCount($userId, $selectedUserId));
        }

        if ($user['acceptMessages']) {
            $this->set('messageEntity', $this->Messages->newEntity());
        }
    }

    /**
     * Renders /users/register
     *
     * @return \Cake\Http\Response|null
     */
    public function register()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            if ($this->Recaptcha->verify()) {
                $user = $this->Users->patchEntity($user, $this->request->getData());

                // Clean email and color
                $cleanEmail = trim($user->email);
                $cleanEmail = strtolower($cleanEmail);
                $cleanColor = strtolower($user->color);
                $cleanColor = preg_replace("/[^a-z0-9]/", '', $cleanColor);

                if (!$this->Users->colorIsTaken($cleanColor)) {
                    $user->set([
                        'password' => $this->request->getData('new_password'),
                        'email' => $cleanEmail,
                        'color' => $cleanColor,
                        'password_version' => 3,
                        'is_admin' => 0
                    ]);

                    if ($this->Users->save($user)) {
                        $this->Flash->success('Your account has been registered. You may now log in.');

                        return $this->redirect(['action' => 'login']);
                    } else {
                        $this->Flash->error('There was an error registering your account. Please try again.');
                    }
                } else {
                    $this->Flash->error(
                        "Sorry, the color #$cleanColor is already taken. You could try tweaking that color slightly " .
                        'and seeing if the new color is available, or you could pick a completely different color.'
                    );
                }
            } else {
                $this->set('recaptchaError', true);
            }
        } else {
            // Select a random available color
            do {
                $randomColor = '';
                for ($n = 1; $n <= 3; $n++) {
                    $randomColor .= str_pad(dechex(rand(0, 250)), 2, '0', STR_PAD_LEFT);
                }
                $isTaken = $this->Users->colorIsTaken($randomColor);
            } while ($isTaken);
            $this->request = $this->request->withData('color', $randomColor);
            $this->set('randomColor', true);
        }

        /* So the password fields aren't filled out automatically when the user
         * is bounced back to the page by a validation error */
        $this->request = $this->request->withData('new_password', null);
        $this->request = $this->request->withData('confirm_password', null);

        $this->set([
            'title_for_layout' => 'Register Account',
            'user' => $user
        ]);

        return null;
    }

    /**
     * Renders /users/login
     *
     * @return \Cake\Http\Response|null
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
                    $user = $this->Users->get($this->Auth->user('id'));
                    $user->password = $this->request->getData('password');
                    $user->password_version = 3;
                    $this->Users->save($user);
                }

                // Remember login in cookie
                $this->Cookie->configKey('CookieAuth', [
                    'expires' => '+1 year',
                    'httpOnly' => true
                ]);
                $this->Cookie->write('CookieAuth', [
                    'email' => $this->request->getData('email'),
                    'password' => $this->request->getData('password')
                ]);

                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error('Email or password is incorrect');
            }
        }
        $this->set([
            'title_for_layout' => 'Log in'
        ]);

        return null;
    }

    /**
     * Renders /users/logout
     *
     * @return \Cake\Http\Response|null
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Renders /users/check-color-availability
     *
     * @param string|null $color
     * @return void
     */
    public function checkColorAvailability($color = null)
    {
        $this->viewBuilder()->setClassName('Json');

        $this->set([
            'available' => !$this->Users->colorIsTaken($color),
            '_serialize' => ['available']
        ]);
    }

    /**
     * Renders /users/settings
     *
     * @return void
     */
    public function settings()
    {
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);
        $this->set([
            'title_for_layout' => 'Settings',
            'user' => $user
        ]);

        if (! ($this->request->is('post') || $this->request->is('put'))) {
            return;
        }

        $user = $this->Users->patchEntity($user, $this->request->getData(), [
            'fieldList' => ['new_password', 'confirm_password', 'password', 'profile', 'acceptMessages', 'messageNotification', 'emailUpdates']
        ]);
        if ($user->getErrors()) {
            $this->Flash->error('Please correct the indicated '.__n('error', 'errors', count($user->getErrors())).' before continuing.');
            return;
        }

        $action = $this->request->getData('action');

        // Change password
        if ($action == 'change_password') {
            $user->password = $this->request->getData('new_password');
            $user->password_version = 3;
            if ($this->Users->save($user)) {
                // Remember new credentials in cookie
                $this->Cookie->configKey('CookieAuth', [
                    'expires' => '+1 year',
                    'httpOnly' => true
                ]);
                $this->Cookie->write('CookieAuth', [
                    'email' => $user->email,
                    'password' => $this->request->getData('new_password')
                ]);

                $this->Flash->success('Your password has been changed.');
            } else {
                $this->Flash->error('There was an error changing your password. Please try again.');
            }

        // Introspection
        } elseif ($action == 'introspection') {
            if ($this->Users->save($user)) {
                $this->Flash->success('Introspection updated.');
            } else {
                $this->Flash->error('There was an error updating your introspection. Please try again.');
            }

        // Options
        } elseif ($action == 'options') {
            if ($this->Users->save($user)) {
                $this->Flash->success('Account settings updated.');
            } else {
                $this->Flash->error('There was an error updating your account settings.');
            }
        }
    }

    /**
     * Allows the user to enter their email address and get a link to reset their password
     *
     * @return \Cake\Http\Response|null
     */
    public function forgotPassword()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $email = strtolower(trim($email));
            if (empty($email)) {
                $this->Flash->error('Please enter the email address you registered with to have your password reset.');
            } else {
                $userId = $this->Users->getIdWithEmail($email);
                if ($userId) {
                    if ($this->Users->sendPasswordResetEmail($userId)) {
                        $this->Flash->success('You did it! The email goblins should be delivering a link to reset your password forthwith.');

                        return $this->redirect('/');
                    } else {
                        $msg = 'There was an error sending your password-resetting email. I swear this never happens. :(';
                        $this->Flash->error($msg);
                    }
                } else {
                    $msg = 'I couldn\'t find an account registered with the email address <strong>'.$email.'</strong>. ';
                    $msg .= 'Please make sure you spelled it correctly.';
                    $this->Flash->error($msg);
                }
            }
        }
        $this->set([
            'titleForLayout' => 'Forgot Password',
            'user' => $user
        ]);

        return null;
    }

    /**
     * Renders /users/reset-password
     *
     * @param int|null $userId
     * @param int|null $timestamp
     * @param string|null $hash
     * @return \Cake\Http\Response|null
     */
    public function resetPassword($userId = null, $timestamp = null, $hash = null)
    {
        if (! $userId || ! $timestamp && ! $hash) {
            throw new NotFoundException('Incomplete URL for password-resetting. Did you leave out part of the URL when you copied and pasted it?');
        }

        if (time() - $timestamp > 60 * 60 * 24) {
            throw new ForbiddenException('Sorry, that link has expired.');
        }

        $expectedHash = $this->Users->getPasswordResetHash($userId, $timestamp);
        if ($hash != $expectedHash) {
            throw new ForbiddenException('Invalid security key');
        }

        $user = $this->Users->get($userId);
        $email = $user->email;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $data['password'] = $this->request->getData('new_password');
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been updated.');

                return $this->redirect(['action' => 'login']);
            }
        }
        $this->request = $this->request->withData('new_password', null);
        $this->request = $this->request->withData('confirm_password', null);

        $this->set([
            'email' => $email,
            'titleForLayout' => 'Reset Password',
            'user' => $this->Users->newEntity()
        ]);

        return null;
    }
}
