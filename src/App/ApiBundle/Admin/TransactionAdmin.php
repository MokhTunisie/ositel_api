<?php

declare(strict_types=1);

namespace App\ApiBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DateRangePickerType;

final class TransactionAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('amount')
            ->add('isInput')
            ->add('isValid')
            ->add('createdAt', DateRangeFilter::class, ['field_type' => DateRangePickerType::class])
            ->add('updatedAt', DateRangeFilter::class, ['field_type' => DateRangePickerType::class])
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('title', null, [
                'editable' => true,
            ])
            ->add('description', null, [
                'editable' => true,
            ])
            ->add('category')
            ->add('amount', null, [
                'editable' => true,
            ])
            ->add('isInput', null, [
                'editable' => true,
            ])
            ->add('isValid', null, [
                'editable' => true,
            ])
            ->add('tags')
            ->add('createdAt', 'datetime', [
                'format' => 'd/m/Y H:i',
            ])
            ->add('updatedAt', 'datetime', [
                'format' => 'd/m/Y H:i',
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('title')
            ->add('description')
            ->add('category', ModelType::class, [
                'multiple' => false,
                'btn_add' => 'Ajouter une catégorie',
            ])
            ->add('amount')
            ->add('isInput')
            ->add('isValid')
            ->add('tags', ModelType::class, [
                'multiple' => true,
                'btn_add' => 'Ajouter un Tag',
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('category')
            ->add('amount')
            ->add('isInput')
            ->add('isValid')
            ->add('tags')
            ->add('createdAt', 'datetime', [
                'format' => 'd/m/Y H:i',
            ])
            ->add('updatedAt', 'datetime', [
                'format' => 'd/m/Y H:i',
            ])
            ;
    }
}
