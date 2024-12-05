<?php

namespace App\Controller\Admin;

use App\Entity\PointOfInterest;
use App\Entity\Role;
use App\Entity\Route as RouteWithPOI;
use App\Entity\Tag;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

class AdminDashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirect($this->adminUrlGenerator->setController(RouteCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Aapodwalk');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud(new TranslatableMessage('Routes', [], 'admin'), 'fa-solid fa-spaghetti-monster-flying', RouteWithPOI::class);
        yield MenuItem::linkToCrud(new TranslatableMessage('Tags', [], 'admin'), 'fa-solid fa-cloud-meatball', Tag::class);
        yield MenuItem::linkToCrud(new TranslatableMessage('Points of interest', [], 'admin'), 'fas fa-ghost', PointOfInterest::class);
        yield MenuItem::linkToCrud(new TranslatableMessage('Users', [], 'admin'), 'fas fa-user', User::class)
            ->setPermission(Role::USER_ADMIN->value);

        yield MenuItem::section();
        yield MenuItem::linkToUrl(new TranslatableMessage('API documentation', [], 'admin'), 'fas fa-book', $this->generateUrl('api_doc'));
    }
}
