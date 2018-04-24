<?php

namespace RochaMarcelo\CakePimpleDi\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use RochaMarcelo\CakePimpleDi\Di\DiTrait;

class ActionInjectionListener implements EventListenerInterface
{
    use DiTrait;

    private $injectionsMap = [];

    public function __construct(array $injectionsMap)
    {
        $this->injectionsMap = $injectionsMap;
    }

    /**
     * Lista de eventos que a classe escuta
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Controller.beforeCallAction' => 'injectDependency',
        ];
    }

    /**
     * Envia e-mail 'novo pedido'
     *
     * @param \Cake\Event\Event $event Cake event Controller.beforeCallAction
     * @param array $actionArgs to be passed to action
     *
     * @return void
     */
    public function injectDependency(Event $event, array $actionArgs)
    {
        $className = '\\' . get_class($event->getSubject());
        $action = $event->getSubject()->getRequest()->getParam('action');

        if (isset($this->injectionsMap[$className][$action])) {
            $args = [];
            foreach ($this->injectionsMap[$className][$action] as $injection) {
                $args[] = $this->di()->get($injection);
            }
            $event->setResult(['actionArgs' => array_merge($args, $actionArgs)]);
        }
    }
}
