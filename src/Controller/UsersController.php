<?php
namespace App\Controller;

use App\Color\Color;
use App\Model\Entity\User;
use App\Model\Table\MessagesTable;
use App\Model\Table\ThoughtsTable;
use Authentication\Identifier\PasswordIdentifier;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property MessagesTable $Messages
 * @property \App\Model\Table\ThoughtsTable $Thoughts
 */
class UsersController extends AppController
{
    const COOKIE_AUTH_KEY = 'CookieAuth';

    /**
     * Initialize method
     *
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated([
            'checkColorAvailability',
            'forgotPassword',
            'index',
            'login',
            'register',
            'resetPassword',
            'view'
        ]);
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
     * @throws NotFoundException
     */
    public function view($color = null)
    {
        $user = $this->Users->getProfileInfo($color);
        /** @var MessagesTable $messagesTable */
        $messagesTable = TableRegistry::getTableLocator()->get('Messages');
        $this->set([
            'title_for_layout' => "Thinker #$color",
            'user' => $user,
            'colors' => $this->Users->getColorsWithThoughts(),
            'selectedColor' => $color,
            'colorName' => (new Color())->getClosestXkcdColor($color)
        ]);

        $userId = $this->Authentication->getIdentity()?->get('id');
        $selectedUserId = $this->Users->getIdFromColor($color);
        if ($userId) {
            $this->set('messagesCount', $messagesTable->getConversationCount($userId, $selectedUserId));
        }

        if ($user['acceptMessages']) {
            $this->set('messageEntity', $messagesTable->newEmptyEntity());
        }
    }

    /**
     * Renders /users/register
     *
     * @return Response|null
     */
    public function register()
    {
        $user = $this->Users->newEmptyEntity();
        $this->set([
            'title_for_layout' => 'Register Account',
            'user' => $user,
        ]);

        if ($this->request->is('post')) {
            if ($this->verifyForegroundCaptcha() && $this->verifyBackgroundCaptcha()) {
                return $this->processRegister($user);
            }

            $this->clearPassword();

            return null;
        }

        $this->request = $this->request->withData('color', $this->getRandomColor());
        $this->set('randomColor', true);

        return null;
    }

    /**
     * Renders /users/login
     *
     * @return Response|null
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $response = $this->processLogin();
            if ($response) {
                return $response;
            }
        } elseif ($this->request->getQuery('redirect')) {
            $this->Flash->notification('You\'ll need to log in before accessing that page');
        }
        $this->set([
            'title_for_layout' => 'Log in',
            'user' => $this->Users->newEmptyEntity()
        ]);

        return null;
    }

    /**
     * Renders /users/logout
     *
     * @return Response|null
     */
    public function logout()
    {
        return $this->redirect($this->Authentication->logout());
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

        $this->set(['result' => ['available' => !$this->Users->colorIsTaken($color)]]);
        $this->viewBuilder()->setOption('serialize', 'result');
    }

    /**
     * Renders /users/settings
     *
     * @return void
     */
    public function settings()
    {
        $userId = $this->Authentication->getIdentity()?->get('id');
        $user = $this->Users->get($userId);
        $this->set([
            'title_for_layout' => 'Settings',
            'user' => $user
        ]);

        if (!$this->request->is(['post', 'put'])) {
            return;
        }

        $user = $this->Users->patchEntity($user, $this->request->getData(), [
            'fieldList' => [
                'new_password',
                'confirm_password',
                'password',
                'profile',
                'acceptMessages',
                'messageNotification',
                'emailUpdates',
            ]
        ]);
        if ($user->hasErrors()) {
            $this->Flash->error(sprintf(
                'Please correct the indicated %s before continuing.',
                __n('error', 'errors', count($user->getErrors()))
            ));
            return;
        }

        $action = $this->request->getData('action');

        // Change password
        if ($action == 'change_password') {
            $user->password = $this->request->getData('new_password');
            $user->password_version = 3;

            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been changed.');
                return;
            }
            $this->Flash->error('There was an error changing your password. Please try again.');

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
        return;
    }

    /**
     * Allows the user to enter their email address and get a link to reset their password
     *
     * @return Response|null
     */
    public function forgotPassword()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $email = strtolower(trim($email));
            if (empty($email)) {
                $this->Flash->error('Please enter the email address you registered with to have your password reset.');
            } else {
                $userId = $this->Users->getIdWithEmail($email);
                if ($userId) {
                    $this->Users->sendPasswordResetEmail($userId);
                    $this->Flash->success('You did it! The email goblins should be delivering a link to reset your password forthwith.');

                    return $this->redirect('/');
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
     * @return Response|null
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
            'user' => $this->Users->newEmptyEntity()
        ]);

        return null;
    }

    public function myProfile()
    {
        $color = $this->Authentication->getIdentity()?->get('color');

        $this->view($color);

        return $this->render('view');
    }

    /**
     * Returns a boolean indicating if the last CAPTCHA response is valid and shows a Flash error message if not
     *
     * @return bool
     */
    private function verifyBackgroundCaptcha(): bool
    {
        $response = $this->request->getData('g-recaptcha-response');
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $secret = Configure::read('Recaptcha.secret');
        $postData = compact('secret', 'response');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $result = curl_exec($ch);
        curl_close($ch);

        $resultJson = json_decode($result, true);

        if ($resultJson['success'] ?? false) {
            return true;
        }

        $errorMsg = 'The background spam bot detection system has flagged your request as being suspicious. ' .
            'Please try again later, or try using a normal browsing session instead of private/incognito mode, or '.
            'hit up the <a href="/contact">contact page</a> for assistance.';
        $errorMsg .= isset($resultJson['error-codes'])
            ? ' Details: ' . implode('; ', $resultJson['error-codes'])
            : '';
        $this->Flash->error($errorMsg);

        return false;
    }

    /**
     * Processes a registration attempt, assuming that CAPTCHA challenge has been passed
     *
     * @param \App\Model\Entity\User $user
     * @return \Cake\Http\Response|null
     */
    private function processRegister(User $user): ?Response
    {
        $user = $this->Users->patchEntity($user, $this->request->getData());

        // Clean email and color
        $cleanEmail = trim($user->email);
        $cleanEmail = strtolower($cleanEmail);
        $cleanColor = strtolower($user->color);
        $cleanColor = preg_replace("/[^a-z0-9]/", '', $cleanColor);

        if ($this->Users->colorIsTaken($cleanColor)) {
            $this->Flash->error(
                "Sorry, the color #$cleanColor is already taken. You could try tweaking that color slightly " .
                'and seeing if the new color is available, or you could pick a completely different color.'
            );
            $this->clearPassword();

            return null;
        }

        $user->set([
            'password' => $this->request->getData('new_password'),
            'email' => $cleanEmail,
            'color' => $cleanColor,
            'password_version' => 3,
            'is_admin' => 0
        ]);

        if ($this->Users->save($user)) {
            $this->Authentication->setIdentity($user);
            $this->Flash->success('Your account has been registered, and you\'ve been logged in. Welcome!');

            return null;
        }

        $errorsMsgs = [];
        foreach ($user->getErrors() as $errors) {
            $errorsMsgs = array_merge($errorsMsgs, array_values($errors));
        }

        $this->Flash->error(
            'There was an error registering your account. Please try again. Details: '
            . implode('; ', $errorsMsgs)
        );
        $this->clearPassword();
        return null;
    }

    /**
     * Returns a random available color
     *
     * @return string
     */
    private function getRandomColor(): string
    {
        do {
            $randomColor = '';
            for ($n = 1; $n <= 3; $n++) {
                $randomColor .= str_pad(dechex(rand(0, 250)), 2, '0', STR_PAD_LEFT);
            }
            $isTaken = $this->Users->colorIsTaken($randomColor);
        } while ($isTaken);

        return $randomColor;
    }

    /**
     * Checks the result of the login attempt made by the Form authenticator, rehashing the
     * password if it was verified against a legacy hash, and redirects if successful
     *
     * @return Response|null
     */
    private function processLogin(): ?Response
    {
        if (!$this->Authentication->getResult()->isValid()) {
            $this->Flash->error('Email or password is incorrect');

            return null;
        }

        $identifier = $this->Authentication->getAuthenticationService()->getIdentificationProvider();
        if ($identifier instanceof PasswordIdentifier && $identifier->needsPasswordRehash()) {
            $user = $this->Users->get($this->Authentication->getIdentity()->get('id'));
            $user->password = $this->request->getData('password');
            $user->password_version = 3;
            $this->Users->save($user);
        }

        return $this->Authentication->redirectAfterLogin('/');
    }

    /**
     * Returns TRUE if the last user-input CAPTCHA response is valid and shows a Flash error message otherwise
     *
     * @return bool
     */
    private function verifyForegroundCaptcha(): bool
    {
        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');

        $mostRecent = $thoughtsTable->getThoughtwordWithMostRecentActivity();

        $input = trim(strtolower($this->request->getData('thoughtword-captcha')));
        $valid = $mostRecent == $input;

        if ($valid) {
            return true;
        }

        $this->Flash->error(
            'That wasn\'t the most recent thoughtword to be written to. Try copying and pasting it from the front page.'
        );

        return false;
    }

    /**
     * So the password fields aren't filled out automatically when the user
     * is bounced back to the page by a validation error
     *
     * @return void
     */
    private function clearPassword(): void
    {
        $this->request = $this->request->withData('new_password', null);
        $this->request = $this->request->withData('confirm_password', null);
        if ($this->viewBuilder()->hasVar('user')) {
            $user = $this->Users->newEmptyEntity();
            $user->new_password = null;
            $user->confirm_password = null;
            $this->viewBuilder()->setVar('user', $user);
        }
    }
}
