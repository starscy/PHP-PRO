<?php
namespace Starscy\Project\Http\Auth;

use Starscy\Project\Http\Request;
use Starscy\Project\models\User;

interface AuthenticationInterface
{
// Контракт описывает единственный метод,
// получающий пользователя из запроса

public function user(Request $request): User;
}
