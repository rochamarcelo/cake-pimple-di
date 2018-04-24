<?php

namespace RochaMarcelo\CakePimpleDi\Di;

use Cake\Controller\Exception\MissingActionException;
use LogicException;

/**
 * Provides an simple way to use the Di injection for controller action
 *
 * @property \Cake\Http\ServerRequest $request
 * @property string $name
 * @method bool isAction($action)
 * @method \Cake\Event\Event dispatchEvent($name, $data = null, $subject = null)
 */
trait InvokeActionTrait
{
    /**
     * Dispatches the controller action. Checks that the action
     * exists and isn't private.
     *
     * @return mixed The resulting response.
     * @throws \LogicException When request is not set.
     * @throws MissingActionException When actions are not defined or inaccessible.
     */
    public function invokeAction()
    {
        $request = $this->getRequest();
        if (!isset($request)) {
            throw new LogicException('No Request object configured. Cannot invoke action');
        }
        if (!$this->isAction($request->getParam('action'))) {
            throw new MissingActionException([
                'controller' => $this->name . 'Controller',
                'action' => $request->getParam('action'),
                'prefix' => $request->getParam('prefix') ?: '',
                'plugin' => $request->getParam('plugin'),
            ]);
        }
        /* @var callable $callable */
        $callable = [$this, $request->getParam('action')];

        $actionArgs = array_values($request->getParam('pass'));
        $event = $this->dispatchEvent('Controller.beforeCallAction', compact('actionArgs'));
        if (!empty($event->getResult()['actionArgs'])) {
            $actionArgs = $event->getResult()['actionArgs'];
        }

        return $callable(...$actionArgs);
    }
}
