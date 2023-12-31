<?php

namespace App\Service;

use App\Entity\Appointment;
use App\Repository\TeacherRepository;
use App\Repository\TimeFrameRepository;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Language;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Collections\Collection;

class WordService
{

    private TeacherRepository $teacherRepository;
    private TimeFrameRepository $timeFrameRepository;

    public function __construct(TeacherRepository $teacherRepository, TimeFrameRepository $timeFrameRepository)
    {
        $this->teacherRepository = $teacherRepository;
        $this->timeFrameRepository = $timeFrameRepository;
    }

    /**
     * @throws Exception
     */
    public function export(?array $teachers = null) {
        $phpWord = new PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::DE_DE));
        $header = array('size' => 16, 'bold' => true);

        if ($teachers === null) {
            $teachers = $this->teacherRepository->findBy([],['lastName' => 'ASC']);
        }
        foreach ($teachers as $teacher) {
            if ($teacher->getAppointments()->count() <= 0) {
                continue;
            }

            $section = $phpWord->addSection();
            $section->addText(htmlspecialchars(
                "Termine für {$teacher->getFirstName()} {$teacher->getLastName()} ({$teacher->getCode()})"),
                $header);
            $section->addTextBreak(1);

            $section->addText(htmlspecialchars(
                "Im Folgenden sind alle vereinbarten Termin für Sie aufgelistet:"
            ));
            $section->addTextBreak(1);
            $section->addTextBreak(1);

            $styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80);
            $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '000000', 'bgColor' => '000000');
            $styleCell = array('valign' => 'center');
            $fontStyle = array('bold' => true, 'align' => 'center');
            $phpWord->addTableStyle('Fancy Table', $styleTable, $styleFirstRow);
            $table = $section->addTable('Fancy Table');
            $table->addRow(900);
            $table->addCell(1000, $styleCell)->addText(htmlspecialchars('Uhrzeit'), $fontStyle);
            $table->addCell(1600, $styleCell)->addText(htmlspecialchars('Klasse'), $fontStyle);
            $table->addCell(3200, $styleCell)->addText(htmlspecialchars('Schüler_in'), $fontStyle);
            $table->addCell(3200, $styleCell)->addText(htmlspecialchars('Eltern/Ausbilder_in'), $fontStyle);
            $appointments = $this->getAppointmentArray($teacher->getAppointments());
            foreach ($appointments as $time => $appointment) {
                $table->addRow();
                $table->addCell(1000)->addText(htmlspecialchars($time));
                $table->addCell(1600)->addText(htmlspecialchars($appointment && $appointment->getSchoolClass() != null ? $appointment->getSchoolClass()->getCode() : ''));
                $table->addCell(3200)->addText(htmlspecialchars($appointment ? $appointment->getStudentFirstName() .' ' .$appointment->getStudentLastName() : ''));
                $table->addCell(3200)->addText(htmlspecialchars($appointment ? $appointment->getVisitorFirstName() .' ' . $appointment->getVisitorLastName() : ''));
            }

            $section->addPageBreak();
        }


        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        $filesystem = new Filesystem();
        $path = $filesystem->tempnam('/tmp', 'wordExport_', '.docx');
        $objWriter->save($path, 'Word2007', true);

        return $path;
    }

    protected function getAppointmentArray(Collection $appointments) :array {
        $return = [];

        foreach ($this->timeFrameRepository->findAll() as $timeFrame) {
            $return[$timeFrame->getName()] = false;
        }

        /** @var Appointment $appointment */
        foreach ($appointments as $appointment) {
            $return[$appointment->getTimeFrame()->getName()] = $appointment;
        }

        return $return;
    }

}