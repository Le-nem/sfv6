<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class Helpers
{

    public function __construct(private LoggerInterface $logger){}

public function SayCc():string{
    return 'cc';
}
}