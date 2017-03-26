<?php
/**
 * Created by PhpStorm.
 * User: caevi
 * Date: 26/03/2017
 * Time: 22:15
 */

namespace Domain\Card;


class NotEnoughBalanceException extends \Exception
{

    /**
     * NotEnoughtBalanceException constructor.
     */
    public function __construct()
    {
    }
}