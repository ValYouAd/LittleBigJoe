<?php

namespace LittleBigJoe\Bundle\FrontendBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LittleBigJoeFrontendBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
