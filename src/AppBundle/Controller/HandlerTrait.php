<?php
namespace AppBundle\Controller;

use AppBundle\Handler\MerchantHandler;
use AppBundle\Handler\UserHandler;

trait HandlerTrait
{
    /**
     * @return MerchantHandler
     */
    protected function getMerchantHandler()
    {
        return parent::get('app.merchant.handler');
    }

    /**
     * @return UserHandler
     */
    protected function getUserHandler()
    {
        return parent::get('app.user.handler');
    }
}