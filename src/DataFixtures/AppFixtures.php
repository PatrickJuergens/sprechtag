<?php

namespace App\DataFixtures;

use App\Entity\SchoolClass;
use App\Entity\Teacher;
use App\Entity\TimeFrame;
use App\Entity\User;
use App\Repository\SchoolClassRepository;
use App\Service\ExcelService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHash;
    private ExcelService $excelService;

    public function __construct(UserPasswordHasherInterface $passwordHash, ExcelService $excelService)
    {
        $this->passwordHash = $passwordHash;
        $this->excelService = $excelService;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager);
        $this->createTimeFrame($manager);
//        $this->excelService->createClasses($this->classes);
//        $this->excelService->createTeachers($this->teachers);
    }

    private array $classes = ['WIT1A', 'WIT1B', 'WIT1C','WIT2A', 'WIT2B', 'WIT2C','WIT3A', 'WIT3B', 'WIT3C'];

    private array $timeFrames = ['16:30', '16:40', '16:50', '17:00', '17:10', '17:20', '17:30', '17:40', '17:50',
        '18:00', '18:10', '18:20', '18:30', '18:40', '18:50'];

    private array $users = [
        ['email'=> 'admin@example.com', 'roles' => ['ROLE_SUPER_ADMIN'], 'plainPasswort' => 'bbs'],
    ];

    private array $teachers = [
        'WJP' => ['firstName'=>'Patrick', 'lastName'=>'Jürgens', 'classes'=>['WIT1C', 'WIT2A']]
    ];

    private function createUsers(ObjectManager $manager): void
    {
        foreach ($this->users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $hashedPassword = $this->passwordHash->hashPassword(
                $user,
                $userData['plainPasswort']
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function createTimeFrame(ObjectManager $manager) :void
    {
        foreach ($this->timeFrames as $timeFrame) {
            $newTimeFrame = new TimeFrame();
            $newTimeFrame->setName($timeFrame);
            $manager->persist($newTimeFrame);
        }
        $manager->flush();
    }

}