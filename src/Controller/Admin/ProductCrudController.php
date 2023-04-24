<?php

namespace App\Controller\Admin;

use App\Entity\Product\Product;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Produits')
            ->setEntityLabelInSingular('Produit')
            ->setPageTitle('index', 'Pizzeria - Administration des produits')
            ->setPaginatorPageSize(10)
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->setLabel('Identifiant')
                ->hideOnForm(),
            TextField::new('imageFile')
                ->setLabel('Miniature')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('image')
                ->setBasePath('uploads/images/thumbnails/')
                ->setUploadDir('public/uploads/images/thumbnails/')
                ->onlyOnIndex(),
            TextField::new('title')
                ->setLabel('Titre'),
            SlugField::new('slug')->setTargetFieldName('title')->hideOnIndex(),
            TextareaField::new('description')
                ->setLabel('Description')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex(),
            AssociationField::new('category')
                ->setLabel('Catégorie'),
            MoneyField::new('price')
                ->setLabel('Prix')
                ->setStoredAsCents(true)
                ->setCurrency('EUR'),
            BooleanField::new('isVending')
                ->setLabel('En vente')
                ->hideOnDetail(),
        ];
    }
}
