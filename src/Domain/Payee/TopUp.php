<?php
namespace Domain\Payee;

class TopUp implements Payee
{

    public function getName()
    {
        return 'Bank Name';
    }
}