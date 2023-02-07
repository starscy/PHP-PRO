<?php

namespace Starscy\Project\Http\Actions;

use Starscy\Project\Http\Request;
use Starscy\Project\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}