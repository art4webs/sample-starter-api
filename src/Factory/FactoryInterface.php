<?php

namespace App\Factory;

interface FactoryInterface
{
    public function build(array $data): void;
}
