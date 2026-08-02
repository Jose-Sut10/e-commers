<?php

namespace App\Console;

abstract class Command
{
    abstract public function handle(array $arguments): void;
}