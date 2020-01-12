<?php
namespace App\Tests\Security\Voter;

use App\Entity\Exercise;
use App\Entity\User;
use App\Security\AuthUser;
use App\Security\Voter\ExerciseRecordsVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ExerciseRecordsVoterTest extends TestCase
{
    /**
     * @dataProvider getVoteTests
     */
    public function testVote(UserInterface $user, Exercise $exercise, array $attributes, int $expected)
    {
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $voter = new ExerciseRecordsVoter();
        $result = $voter->vote(
            $token,
            $exercise,
            $attributes,
        );

        self::assertSame($expected, $result);
    }

    public function getVoteTests()
    {
        $user = new User('', '', '');
        $userId = $user->getId();
        $exercise = new Exercise($user, '', []);
        $validAuthUser = new AuthUser($userId, '');

        yield 'invalid attribute' => [
            $validAuthUser,
            new Exercise($user, '', []),
            ['invalid'],
            VoterInterface::ACCESS_ABSTAIN,
        ];

        yield 'user has no access' => [
            new AuthUser(uuid_v4(), ''),
            $exercise,
            ['EXERCISE_RECORDS_LIST'],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'user not instance of '.AuthUser::class => [
            new \Symfony\Component\Security\Core\User\User('test', ''),
            $exercise,
            ['EXERCISE_RECORDS_LIST'],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'list records' => [
            $validAuthUser,
            $exercise,
            ['EXERCISE_RECORDS_LIST'],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'create record' => [
            $validAuthUser,
            $exercise,
            ['EXERCISE_RECORDS_CREATE'],
            VoterInterface::ACCESS_GRANTED,
        ];
    }
}
