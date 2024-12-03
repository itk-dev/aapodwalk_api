<?php

namespace App\Controller\Admin;

use App\Entity\ApiUser;
use App\Entity\PointOfInterest;
use App\Entity\Role;
use App\Entity\Route as RouteWithPOI;
use App\Entity\Tags;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $dashboard = $this->adminUrlGenerator
            ->setController(PointOfInterestController::class)->setAction(Crud::PAGE_INDEX)
            ->generateUrl();

        return $this->redirect($dashboard);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Aapodwalk');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('API Users', 'fas fa-user-pen', ApiUser::class)
            ->setPermission(Role::USER_ADMIN->value);
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class)
            ->setPermission(Role::USER_ADMIN->value);
        yield MenuItem::linkToCrud('Route', 'fa-solid fa-spaghetti-monster-flying', RouteWithPOI::class);
        yield MenuItem::linkToCrud('Tags', 'fa-solid fa-cloud-meatball', Tags::class);
        yield MenuItem::linkToCrud('Points of interest', 'fas fa-ghost', PointOfInterest::class);
    }
}
