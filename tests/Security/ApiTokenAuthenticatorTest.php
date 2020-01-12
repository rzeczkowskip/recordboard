<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ApiTokenAuthenticator;
use App\Security\AuthUser;
use Doctrine\ORM\NoResultException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiTokenAuthenticatorTest extends TestCase
{
    /**
     * @var UserRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    protected function tearDown(): void
    {
        unset($this->userRepository);
    }

    /**
     * @dataProvider supportsProvider
     */
    public function testSupports(string $header, bool $expectedResult): void
    {
        $httpHeader = 'HTTP_'.$header;

        $authenticator = new ApiTokenAuthenticator($this->userRepository);
        $request = new Request([], [], [], [], [], [$httpHeader => 'test']);

        $result = $authenticator->supports($request);

        static::assertEquals($expectedResult, $result);
    }

    public function supportsProvider(): \Generator
    {
        yield ['Authorization', true];
        yield ['Invalid', false];
    }

    public function testGetCredentialsRemovesBearerPrefix(): void
    {
        $apiKey = 'secret';
        $authHeaderValue = 'Bearer '.$apiKey;

        $request = new Request([], [], [], [], [], ['HTTP_Authorization' => $authHeaderValue]);
        $authenticator = new ApiTokenAuthenticator($this->userRepository);

        $credentials = $authenticator->getCredentials($request);

        static::assertEquals($apiKey, $credentials);
    }

    public function testGetUser(): void
    {
        $credentials = 'secret';
        $email = 'admin@example.com';

        $userProvider = $this->createMock(UserProviderInterface::class);
        $user = new User($email, '', '');
        $authUser = new AuthUser($user->getId(), '');

        $this->userRepository
            ->expects(static::once())
            ->method('getUserByApiToken')
            ->with($credentials)
            ->willReturn($user);

        $userProvider
            ->expects(static::once())
            ->method('loadUserByUsername')
            ->with($email)
            ->willReturn($authUser);

        $authenticator = new ApiTokenAuthenticator($this->userRepository);

        $result = $authenticator->getUser($credentials, $userProvider);

        static::assertEquals($authUser, $result);
    }

    public function testGetUserReturnsNullOfApiTokenNowFound(): void
    {
        $credentials = 'secret';
        $userProvider = $this->createMock(UserProviderInterface::class);

        $this->userRepository
            ->expects(static::once())
            ->method('getUserByApiToken')
            ->with($credentials)
            ->willThrowException(new NoResultException());

        $userProvider
            ->expects(static::never())
            ->method('loadUserByUsername');

        $authenticator = new ApiTokenAuthenticator($this->userRepository);

        $result = $authenticator->getUser($credentials, $userProvider);

        static::assertNull($result);
    }

    public function testStartReturns401Response(): void
    {
        $authenticator = new ApiTokenAuthenticator($this->userRepository);

        $result = $authenticator->start(new Request());

        static::assertInstanceOf(Response::class, $result);
        static::assertEquals(401, $result->getStatusCode());
    }

    public function testRememberMeSupportIsDisabled(): void
    {
        $authenticator = new ApiTokenAuthenticator($this->userRepository);

        static::assertFalse($authenticator->supportsRememberMe());
    }

    public function testCheckCredentialsIsTrue(): void
    {
        $authenticator = new ApiTokenAuthenticator($this->userRepository);

        $result = $authenticator->checkCredentials(
            '',
            new AuthUser(uuid_v4(), '')
        );

        static::assertTrue($result);
    }
}
