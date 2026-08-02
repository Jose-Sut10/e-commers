<?php
namespace Core\Middleware;
use Core\Request;

interface Middleware{
    public function handle(Request $request): void;
}