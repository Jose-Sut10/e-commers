<?php
namespace App\Console\Commands;
use App\Console\GeneratorCommand;

class MakeControllerCommand extends GeneratorCommand
{
    protected string $stub = 'controller';
    protected string $destination = 'app/Controllers';
    protected string $suffix = 'Controller';
}