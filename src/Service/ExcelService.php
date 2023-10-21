<?php

namespace App\Service;

use App\Entity\SchoolClass;
use App\Entity\Teacher;
use App\Repository\SchoolClassRepository;
use App\Repository\TeacherRepository;
use App\Repository\TimeFrameRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExcelService
{

    private EntityManagerInterface $entityManager;
    private TeacherRepository $teacherRepository;
    private SchoolClassRepository $schoolClassRepository;
    private TimeFrameRepository $timeFrameRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                TeacherRepository      $teacherRepository,
                                SchoolClassRepository  $schoolClassRepository,
                                TimeFrameRepository    $timeFrameRepository
    )
    {

        $this->entityManager = $entityManager;
        $this->teacherRepository = $teacherRepository;
        $this->schoolClassRepository = $schoolClassRepository;
        $this->timeFrameRepository = $timeFrameRepository;
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function import(UploadedFile $file) :void
    {
        $reader = IOFactory::createReader("Xlsx");
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $teacherArray = [];
        $classesArray = [];
        foreach ($worksheet->getRowIterator(2,null) as $row) {
            $cellIterator = $row->getCellIterator('A', 'D');
            $teacherCode = null;
            foreach ($cellIterator as $cell) {
                if ($cell->getColumn() == 'A' && !empty($cell->getValue())) {
                    if (!array_key_exists($cell->getValue(), $teacherArray)) {
                        $teacherArray[$cell->getValue()] = [];
                    }
                    $teacherCode = $cell->getValue();
                } elseif ($cell->getColumn() == 'B' && !empty($cell->getValue()) &&  !array_key_exists('lastName', $teacherArray[$teacherCode])) {
                    $teacherArray[$teacherCode]['lastName'] = $cell->getValue();
                } elseif ($cell->getColumn() == 'C' && !empty($cell->getValue()) && !array_key_exists('firstName', $teacherArray[$teacherCode])) {
                    $teacherArray[$teacherCode]['firstName'] = $cell->getValue();
                } elseif ($cell->getColumn() == 'D' && !empty($cell->getValue())) {
                    if (!in_array($cell->getValue(), $classesArray)) {
                        $classesArray[] = $cell->getValue();
                    }
                    if (!array_key_exists('classes', $teacherArray[$teacherCode])) {
                        $teacherArray[$teacherCode]['classes'] = [];
                    }
                    if (!in_array($cell->getValue(), $teacherArray[$teacherCode]['classes'])) {
                        $teacherArray[$teacherCode]['classes'][] = $cell->getValue();
                    }

                }
            }
        }
        $this->createClasses($classesArray);
        $this->createTeachers($teacherArray);
    }

    public function createClasses(array $classesArray): void
    {
        foreach ($classesArray as $class) {
            if ($this->schoolClassRepository->findOneBy(['code'=>$class]) === null) {
                $newClass = new SchoolClass();
                $newClass->setCode($class);
                $this->entityManager->persist($newClass);
            }
        }
        $this->entityManager->flush();
    }

    public function createTeachers(array $teachersArray): void
    {
        $timeFrames = $this->timeFrameRepository->findAll();
        foreach ($teachersArray as $code => $teacher) {
            if ($this->teacherRepository->findOneBy(['code'=>$code]) === null) {
                $newTeacher = new Teacher();
                $newTeacher->setCode($code);
                $newTeacher->setToken(md5(uniqid(mt_rand(), true)));
                $newTeacher->setFirstName($teacher['firstName']);
                $newTeacher->setLastName($teacher['lastName']);
                foreach ($teacher['classes'] as $classCode) {
                    $newTeacher->addSchoolClass($this->schoolClassRepository->findOneBy(['code' => $classCode]));
                }
                foreach ($timeFrames as $timeFrame) {
                    $newTeacher->addAvailableTimeFrame($timeFrame);
                }
                $this->entityManager->persist($newTeacher);
            }
        }
        $this->entityManager->flush();
    }
}