<?php

namespace Softgroup\FinalBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class MessageAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('messagetext')
            ->add('createdate')
            ->add('deletedate')
            ->add('deleteto')
            ->add('url')
            ->add('password')
            ->add('email')
            ->add('creatorip')
            ->add('readerip')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('messagetext')
            ->add('createdate')
            ->add('deletedate')
            ->add('deleteto')
            ->add('url')
            ->add('password')
            ->add('email')
            ->add('creatorip')
            ->add('readerip')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('messagetext')
            ->add('createdate')
            ->add('deletedate')
            ->add('deleteto')
            ->add('url')
            ->add('password')
            ->add('email')
            ->add('creatorip')
            ->add('readerip')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('messagetext')
            ->add('createdate')
            ->add('deletedate')
            ->add('deleteto')
            ->add('url')
            ->add('password')
            ->add('email')
            ->add('creatorip')
            ->add('readerip')
        ;
    }
}
