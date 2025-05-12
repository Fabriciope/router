<?php

namespace Fabriciope\Router\Interfaces;

interface Validatable
{
    /**
    * Validate something
    *
    * @throws \Exception
    */
    public function validate(): void;
}
