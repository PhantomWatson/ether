<?php
namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

class CookieAuthenticate extends BaseAuthenticate
{

    /**
     * Constructor method
     *
     * @param ComponentRegistry $registry The Component registry used on this request.
     * @param array $config Array of config to use.
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->setConfig([
            'cookie' => [
                'name' => 'CookieAuth',
            ],
            'fields' => [
                'username' => 'username',
                'password' => 'password',
            ],
            'passwordHasher' => [
                'className' => 'Fallback',
                'hashers' => [
                    'Default',
                    'Weak' => ['hashType' => 'sha1'],
                ],
            ],
        ]);
        parent::__construct($registry, $config);
    }

    /**
     * Authenticate a user based on the request information.
     *
     * @param ServerRequest $request Request to get authentication information from.
     * @param Response $response A response object that can have headers added.
     * @return false|array Either false on failure, or an array of user data on success.
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $cookie = $request->getCookie($this->_config['cookie']['name']);
        if (empty($cookie)) {
            return false;
        }

        $username = $this->_config['fields']['username'];
        $password = $this->_config['fields']['password'];
        if (empty($cookie[$username]) || empty($cookie[$password])) {
            return false;
        }

        $user = $this->_findUser($cookie[$username], $cookie[$password]);

        return $user ?: false;
    }

    /**
     * Returns a list of all events that this authenticate class will listen to.
     *
     * An authenticate class can listen to following events fired by AuthComponent:
     *
     * - `Auth.afterIdentify` - Fired after a user has been identified using one of
     *   configured authenticate class. The callback function should have signature
     *   like `afterIdentify(Event $event, array $user)` when `$user` is the
     *   identified user record.
     *
     * - `Auth.logout` - Fired when AuthComponent::logout() is called. The callback
     *   function should have signature like `logout(Event $event, array $user)`
     *   where `$user` is the user about to be logged out.
     *
     * @return array List of events this class listens to. Defaults to `[]`.
     */
    public function implementedEvents(): array
    {
        return [
            'Auth.logout' => 'logout',
        ];
    }

    /**
     * Delete cookies when a user logs out.
     *
     * @return void
     */
    public function logout()
    {
        $controller = $this->_registry->getController();
        $cookieCollection = $controller->getRequest()->getCookieCollection();
        $cookieName = $this->_config['cookie']['name'];
        if (!$cookieCollection->has($cookieName)) {
            return;
        }

        $cookie = $cookieCollection->get($cookieName);

        $this->_registry->getController()->setResponse($controller
            ->getResponse()
            ->withExpiredCookie($cookie));
    }
}
