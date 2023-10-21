<?php

namespace App\Table;

use App\Entity\Appointment;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\QueryBuilder;
use HelloSebastian\HelloBootstrapTableBundle\Columns\ColumnBuilder;
use HelloSebastian\HelloBootstrapTableBundle\Columns\TextColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\DateTimeColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\HiddenColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\ActionColumn;
use HelloSebastian\HelloBootstrapTableBundle\HelloBootstrapTable;

class AppointmentTable extends HelloBootstrapTable
{
    protected function buildColumns(ColumnBuilder $builder, $options): void
    {
        $this->setTableDataset([
            'locale' => 'de-DE',
            'show-export' => $this->security->isGranted('ROLE_SUPER_ADMIN'),
        ]);

        $builder
            ->add("id", HiddenColumn::class)
            ->add('timeFrame', TextColumn::class, array(
                'title' => 'Zeitfenster',
                'data' => function (Appointment $appointment) { //entity from getEntityClass
                    //you can return what ever you want ...
                    return $appointment->getTimeFrame()->getName();
                },
                'search' => function (Composite $composite, QueryBuilder $qb, $search) {
                    //first add condition to $composite
                    //don't forget the ':' before the parameter for binding
                    $qb->join('appointment.timeFrame', 'tf');
                    $composite->add($qb->expr()->like('tf.name', ':timeFrameName'));
                    //then bind search to query
                    $qb->setParameter(":timeFrameName", $search . '%');
                }
            ))
            ->add('teacher', TextColumn::class, array(
                'title' => 'Lehrkraft',
                'data' => function (Appointment $appointment) { //entity from getEntityClass
                    //you can return what ever you want ...
                    return $appointment->getTeacher()->getFirstName() . ' ' . $appointment->getTeacher()->getLastName() . ' (' .$appointment->getTeacher()->getCode() . ')' ;
                },
                'search' => function (Composite $composite, QueryBuilder $qb, $search) {
                    //first add condition to $composite
                    //don't forget the ':' before the parameter for binding
                    $qb->join('appointment.teacher', 't');
                    $composite->add($qb->expr()->like('t.code', ':teacherCode'));
                    //then bind search to query
                    $qb->setParameter(":teacherCode", $search . '%');
                }
            ))
            ->add('schoolClass', TextColumn::class, array(
                'title' => 'Kurs / Klasse',
                'data' => function (Appointment $appointment) { //entity from getEntityClass
                    //you can return what ever you want ...
                    return $appointment->getSchoolClass()->getCode();
                },
                'search' => function (Composite $composite, QueryBuilder $qb, $search) {
                    //first add condition to $composite
                    //don't forget the ':' before the parameter for binding
                    $qb->join('appointment.schoolClass', 's');
                    $composite->add($qb->expr()->like('s.code', ':schoolClass'));
                    //then bind search to query
                    $qb->setParameter(":schoolClass", $search . '%');
                }
            ))
            ->add('studentFirstName', TextColumn::class, array(
                'title' => 'Vorname des Schülers'
            ))
            ->add('studentLastName', TextColumn::class, array(
                'title' => 'Nachname des Schülers'
            ))
            ->add('visitorFirstName', TextColumn::class, array(
                'title' => 'Vorname des Besuchers'
            ))
            ->add('visitorLastName', TextColumn::class, array(
                'title' => 'Nachname des Besuchers'
            ))
            ->add('createdBy', TextColumn::class, array(
                'title' => 'Angelegt von'
            ))
            ->add('updatedBy', TextColumn::class, array(
                'title' => 'Bearbeitet von'
            ))
            ->add('createdAt', DateTimeColumn::class, array(
                'title' => 'Angelegt am',
                'format' => 'd.m.Y H:i:s'
            ))
            ->add('updatedAt', DateTimeColumn::class, array(
                'title' => 'Bearbeitet am',
                'format' => 'd.m.Y H:i:s'
            ))
            ->add("actions", ActionColumn::class, array(
                'title' => 'Actions',
                'width' => 150,
                'buttons' => array( //see ActionButton for more examples.
                    array(
                        'displayName' => 'Anzeigen',
                        'routeName' => 'app_time_frame_show',
                        'classNames' => 'btn btn-xs' ,
                        'additionalClassNames' => 'btn-secondary mr-1',
                        'addIf' => function(Appointment $appointment) {
                            return $this->security->isGranted('ROLE_SUPER_ADMIN');
                        }
                    ),
                    array(
                        'displayName' => 'Bearbeiten',
                        'routeName' => 'app_time_frame_edit',
                        'classNames' => 'btn btn-xs btn-secondary',
                        'addIf' => function(Appointment $appointment) {
                            return $this->security->isGranted('ROLE_SUPER_ADMIN');
                        }
                    )
                )
            ));
    }

    protected function getEntityClass(): string
    {
        return Appointment::class;
    }
}