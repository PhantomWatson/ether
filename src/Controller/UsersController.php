<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['index', 'register', 'login', 'view', 'check_color_availability']);

        if ($this->request->action === 'register') {
            $this->loadComponent('Recaptcha.Recaptcha');
        }
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set(array(
            'title_for_layout' => 'Thinkers',
            'colors' => $this->Users->getColorsWithThoughts(),
        ));
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
            'user' => $user
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

    public function register()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            if ($this->Recaptcha->verify()) {
                $user = $this->Users->patchEntity($user, $this->request->data);

                // Clean email and color
                $cleanEmail = trim($user->email);
                $cleanEmail = strtolower($cleanEmail);
                $cleanColor = strtolower($user->color);
                $cleanColor = preg_replace("/[^a-z0-9]/", '', $cleanColor);

                $user->set([
                    'password' => $this->request->data['User']['password'],
                    'email' => $cleanEmail,
                    'color' => $cleanColor,
                    'password_version' => 3
                ]);

                if ($this->Users->save($user)) {
                    $this->Flash->success('Your account has been registered. You may now log in.');
                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error('There was an error registering your account. Please try again.');
                }
            } else {
                $this->Flash->error('Invalid CAPTCHA response');
            }
        } else {
            // Select a random available color
            do {
                $this->request->data['color'] = '#';
                for ($n = 1; $n <= 3; $n++) {
                    $this->request->data['color'] .= str_pad(dechex(rand(0, 250)), 2, '0', STR_PAD_LEFT);
                }
                $isTaken = $this->Users->colorIsTaken($this->request->data['color']);
            } while ($isTaken);
            $this->set('random_color', true);
        }

        /* So the password fields aren't filled out automatically when the user
         * is bounced back to the page by a validation error */
        $this->request->data['User']['new_password'] = null;
        $this->request->data['User']['confirm_password'] = null;

        $this->set([
            'title_for_layout' => 'Register Account',
        ]);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success('The user has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The user could not be saved. Please, try again.');
            }
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success('The user has been deleted.');
        } else {
            $this->Flash->error('The user could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
                    $user = $this->Users->get($this->Auth->user('id'));
                    $user->password = $this->request->data('password');
                    $user->password_version = 3;
                    $this->Users->save($user);
                }
                $this->AutoLogin->setCookie();
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error('Email or password is incorrect');
            }
        }
        $this->set(array(
            'title_for_layout' => 'Log in'
        ));
    }

    public function logout()
    {
        $this->AutoLogin->destroyCookie();
        return $this->redirect($this->Auth->logout());
    }

    public function check_color_availability($color = null)
    {
        $this->layout = 'ajax';
        $this->set('available', ! $this->Users->colorIsTaken($color));
    }

    public function account()
    {
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);
        $this->set([
            'title_for_layout' => 'Account',
            'user' => $user
        ]);

        if (! ($this->request->is('post') || $this->request->is('put'))) {
            return;
        }

        $user = $this->Users->patchEntity($user, $this->request->data, [
            'fieldList' => ['new_password', 'confirm_password', 'password', 'profile', 'acceptMessages', 'messageNotification', 'emailUpdates']
        ]);
        if ($user->errors()) {
            $this->Flash->error('Please correct the indicated '.__n('error', 'errors', count($user->errors())).' before continuing.');
            return;
        }

        $action = $this->request->data['action'];

        // Change password
        if ($action == 'change_password') {
            $user->password = $this->request->data['new_password'];
            $user->password_version = 3;
            if ($this->Users->save($user)) {

                // Reset AutoLogin cookie with new password
                $this->request->data['email'] = $user->email;
                $this->request->data['password'] = $this->request->data['new_password'];
                $this->AutoLogin->setCookie();

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
}
