<?php
namespace App\Tests\Security;

use App\Repository\UserRepository;
use App\Security\AuthUser;
use App\Security\AuthUserProvider;
use Doctrine\ORM\NoResultException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthUserProviderTest extends TestCase
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

    public function testLoadUserByUsername(): void
    {
        $id = Uuid::uuid4();
        $email = 'admin@example.com';
        $password = 'secret';

        $userAuthData = [
            'id' => $id,
            'password' => $password,
        ];

        $this->userRepository
            ->expects(static::once())
            ->method('getAuthData')
            ->with($email)
            ->willReturn($userAuthData);

        $provider = new AuthUserProvider($this->userRepository);
        $result = $provider->loadUserByUsername($email);

        static::assertInstanceOf(AuthUser::class, $result);
        static::assertEquals($id->toString(), $result->getUsername());
        static::assertEquals($password, $result->getPassword());
    }

    public function testLoadUserByUsernameInvalidUser(): void
    {
        $this->userRepository
            ->method('getAuthData')
            ->willThrowException(new NoResultException());

        $provider = new AuthUserProvider($this->userRepository);

        $this->expectException(UsernameNotFoundException::class);

        $provider->loadUserByUsername('');
    }

    public function testRefreshUser(): void
    {
        $id = Uuid::uuid4();
        $user = new AuthUser($id, '');

        $provider = $this->createPartialMock(AuthUserProvider::class, ['loadUserByUsername']);

        $provider
            ->expects(static::once())
            ->method('loadUserByUsername')
            ->with($id->toString())
            ->willReturn($user);

        $result = $provider->refreshUser($user);

        static::assertEquals($user, $result);
    }

    public function testRefreshUnsupportedUserThrowsException(): void
    {
        $provider = new AuthUserProvider($this->userRepository);

        $this->expectException(UnsupportedUserException::class);

        $provider->refreshUser($this->createMock(UserInterface::class));
    }

    /**
     * @dataProvider supportedClassesProvider
     */
    public function testSupportedClasses(string $class, bool $expectedResult): void
    {
        $provider = new AuthUserProvider($this->userRepository);
        $result = $provider->supportsClass($class);

        static::assertEquals($expectedResult, $result);
    }

    public function supportedClassesProvider(): \Generator
    {
        yield [AuthUser::class, true];
        yield [\stdClass::class, false];
        yield [UserInterface::class, false];
    }
}
