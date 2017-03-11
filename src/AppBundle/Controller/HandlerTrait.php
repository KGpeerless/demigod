<?php
namespace AppBundle\Controller;

use AppBundle\Handler\UserHandler;

trait HandlerTrait
{
    /**
     * @return UserHandler
     */
    protected function getUserHandler()
    {
        return parent::get('app.user.handler');
    }
}