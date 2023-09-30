<?php

namespace App\DataFixtures;

use App\Entity\SchoolClass;
use App\Entity\Teacher;
use App\Entity\TimeFrame;
use App\Entity\User;
use App\Repository\SchoolClassRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private SchoolClassRepository $schoolClassRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, SchoolClassRepository $schoolClassRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->schoolClassRepository = $schoolClassRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager);
        $this->createTimeFrame($manager);
        $this->createClass($manager);
        $this->createTeacher($manager);

    }

    private array $users = [
        ['email'=> 'admin@patrick-juergens.de', 'roles' => ['ROLE_SUPER_ADMIN'], 'plainPasswort' => 'seeval2023'],
    ];

    private function createUsers(ObjectManager $manager): void
    {
        foreach ($this->users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $userData['plainPasswort']
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private array $timeFrames = ['16:30', '16:40', '16:50', '17:00', '17:10', '17:20', '17:30', '17:40', '17:50',
            '18:00', '18:10', '18:20', '18:30', '18:40', '18:50'];
    private function createTimeFrame(ObjectManager $manager) :void
    {
        foreach ($this->timeFrames as $timeFrame) {
            $newTimeFrame = new TimeFrame($timeFrame);
            $manager->persist($newTimeFrame);
        }
        $manager->flush();
    }

    private array $teachers = [
        ['code'=>'WJP', 'firstName'=>'Patrick', 'lastName'=>'Jürgens', 'classes'=>['WIT1C', 'WIT2A']]
    ];

    private function createTeacher(ObjectManager $manager) :void
    {
        foreach ($this->teachers as $teacher) {
            $newTeacher = new Teacher();
            $newTeacher->setCode($teacher['code']);
            $newTeacher->setFirstName($teacher['firstName']);
            $newTeacher->setLastName($teacher['lastName']);
            foreach ($teacher['classes'] as $classCode) {
                $newTeacher->addSchoolClass($this->schoolClassRepository->findOneBy(['code'=>$classCode]));
            }
            $manager->persist($newTeacher);
        }
        $manager->flush();
    }

    private array $classes = ['WIT1A', 'WIT1B', 'WIT1C','WIT2A', 'WIT2B', 'WIT2C','WIT3A', 'WIT3B', 'WIT3C'];
    private function createClass(ObjectManager $manager): void
    {
        foreach ($this->classes as $class) {
            $newClass = new SchoolClass();
            $newClass->setCode($class);
            $manager->persist($newClass);
        }
        $manager->flush();
    }

}