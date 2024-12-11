<?php

namespace App\Controller;

use App\Service\AppManager;
use App\Service\AppManager\App;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    public function __construct(
        private readonly AppManager $appManager,
    ) {
    }

    #[Route('/app', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
            'apps' => $this->appManager->getApps(),
        ]);
    }

    #[Route('/app/{id}/{path}', name: 'app_show', requirements: ['path' => '.+'])]
    public function show(string|int $id, ?string $path = null): Response
    {
        $app = $this->appManager->getApp($id);
        if (!$app) {
            throw new NotFoundHttpException();
        }

        $url = App::buildUrl($app, $path ?? '');

        // @todo Detect mobile device

        return $this->render('app/show.html.twig', [
            'the_app' => $app,
            'app_start_url' => $url,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Service\AppManager;
use App\Service\AppManager\App;
use Detection\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    public function __construct(
        private readonly AppManager $appManager,
    ) {
    }

    #[Route('/app', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
            'apps' => $this->appManager->getApps(),
        ]);
    }

    #[Route('/app/{id}/{path}', name: 'app_show', requirements: ['path' => '.+'])]
    public function show(string|int $id, ?string $path = null): Response
    {
        $app = $this->appManager->getApp($id);
        if (!$app) {
            throw new NotFoundHttpException();
        }

        $url = App::buildUrl($app, $path ?? '');

        // @todo Detect mobile device
        $detect = new MobileDetect();
        if ($detect->isMobile()) {
            return $this->redirect($url);
        }

        return $this->render('app/show.html.twig', [
            'the_app' => $app,
            'app_start_url' => $url,
        ]);
    }
}
