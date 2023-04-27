<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Transporter;
use App\Entity\Product\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Pizzeria - Administration')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        // Redirection vers la page d'accueil
        yield MenuItem::linkToUrl('Accueil', 'fa fa-home', '/');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa-solid fa-users', User::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tag', Category::class);
        yield MenuItem::linkToCrud('Livraisons', 'fa-solid fa-truck-fast', Transporter::class);
    }
}
