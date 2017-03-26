<?php
namespace Domain\Payee;

class Merchant implements Payee
{
    public function getName()
    {
        return 'KFC';
    }
}