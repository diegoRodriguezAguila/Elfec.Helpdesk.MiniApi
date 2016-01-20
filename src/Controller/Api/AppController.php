<?php
/**
 * Created by PhpStorm.
 * User: drodriguez
 * Date: 24/12/15
 * Time: 13:38
 */

namespace App\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;

class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', ['authenticate' => ['App/Security.HelpdeskToken'=>['headerDeviceId'=>'X-Api-DeviceId',
            'headerToken'=>'X-Api-Token']],
            'checkAuthIn' => 'Controller.initialize',
            'loginAction' => false,
            'unauthorizedRedirect' => false]);
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
}