<?php

namespace App\UI\Admin;

use App\Order\Domain\Entity\DeliveryOrder;
use App\Quote\Domain\Entity\Quote;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DeliveryOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DeliveryOrder::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('quote')
                ->formatValue(function (Quote $val) {
                    return $val->getLocation()->getStore()->getName() . '#' . substr($val->getId(), -12);
                }),
            IdField::new('externalId')->hideOnIndex(),
            DateTimeField::new('createdAt'),
            TextField::new('shipmentRecipientName'),
            TextField::new('shipmentContactPhone'),
            TextField::new('shipmentContactEmail'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC']);
    }
}
