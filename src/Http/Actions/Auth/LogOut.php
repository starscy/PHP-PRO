<?php

namespace Starscy\Project\Http\Actions\Auth;
use DateTimeImmutable;
use Starscy\Project\Http\Auth\AuthException;
use Starscy\Project\Http\Auth\PasswordAuthenticationInterface;
use Starscy\Project\Http\Auth\TokenAuthenticationInterface;
use Starscy\Project\Http\ErrorResponse;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\Response;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\AuthToken;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\models\Repositories\Token\AuthTokenNotFoundException;
use Starscy\Project\models\Repositories\Token\AuthTokensRepositoryInterface;

class LogOut
{
    private const HEADER_PREFIX = 'Bearer ';
    public function __construct(

        private TokenAuthenticationInterface $authentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        $authToken->setExpiresOn(new DateTimeImmutable("now"));

        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => (string)$authToken->token(),
        ]);
    }
}