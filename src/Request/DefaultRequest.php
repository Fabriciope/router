<?php

namespace Fabriciope\Router\Request;

use Fabriciope\Router\Interfaces\Validatable;

class DefaultRequest extends Request implements Validatable
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validate(): void
    {

    }
}
