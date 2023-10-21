<?php

namespace App\Table;

use App\Entity\SchoolClass;
use HelloSebastian\HelloBootstrapTableBundle\Columns\ColumnBuilder;
use HelloSebastian\HelloBootstrapTableBundle\Columns\TextColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\DateTimeColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\HiddenColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\ActionColumn;
use HelloSebastian\HelloBootstrapTableBundle\HelloBootstrapTable;

class SchoolClassTable extends HelloBootstrapTable
{
    protected function buildColumns(ColumnBuilder $builder, $options): void
    {
        $this->setTableDataset([
            'locale' => 'de-DE',
            'show-export' => $this->security->isGranted('ROLE_SUPER_ADMIN'),
        ]);

        $builder
            ->add("id", HiddenColumn::class)
            ->add('code', TextColumn::class, array(
                'title' => 'Kürzel'
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
                        'routeName' => 'app_school_class_show',
                        'classNames' => 'btn btn-xs' ,
                        'additionalClassNames' => 'btn-secondary mr-1',
                        'addIf' => function(SchoolClass $schoolClass) {
                            return $this->security->isGranted('ROLE_ADMIN');
                        }
                    ),
                    array(
                        'displayName' => 'Bearbeiten',
                        'routeName' => 'app_school_class_edit',
                        'classNames' => 'btn btn-xs btn-secondary',
                        'addIf' => function(SchoolClass $schoolClass) {
                            return $this->security->isGranted('ROLE_SUPER_ADMIN');
                        }
                    )
                )
            ));
    }

    protected function getEntityClass(): string
    {
        return SchoolClass::class;
    }
}