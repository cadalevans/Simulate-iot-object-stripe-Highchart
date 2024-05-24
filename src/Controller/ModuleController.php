<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ModuleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Repository\ModuleRepository;
//use App\Event\DatabaseChangeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

class ModuleController extends AbstractController
{
    private $security;
    public function __construct(Security $security, EventDispatcherInterface $eventDispatcher)
    {
        //$this->eventDispatcher = $eventDispatcher;
        $this->security = $security;
    }

    #[Route('/moduleqsd', name: 'app_module')]
    public function index(): Response
    {
        return $this->render('module/index1.html.twig', [
            'controller_name' => 'ModuleController',
        ]);
    }
    #[Route('/module', name:'app_module')]
    public function addView(ModuleRepository $moduleRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $module = new Module();

        // Get the current user
        $currentUser = $this->security->getUser();

        // Check if the current user exists and is authenticated
        if ($currentUser) {
            // Set the current user in the module entity
            $module->setUser($currentUser);
        } else {


            // $module->setUser(null); // in my case i have set it to null
            //but note that normaly it was never going to be null because in case off unsuccessful login we can't enter in
            // in that page but some time let us say that we can have a problem with the credentials or a web attack so you can put it on null as you want

        }

        $form = $this->createForm(ModuleType::class, $module);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $module->setIsOperating(true);
            $module->setDate(new \DateTime());
            $entityManager->persist($module);
            $entityManager->flush();

            $this->addFlash('success', 'Your module has been added.');
            return $this->redirectToRoute('module_add');
        }

        // Get the current user ID,
        $userId = $this->getUser()->getId();


        // Fetch modules based on the user ID
        $modules = $moduleRepository->findModulesByUserId($userId);

        return $this->render('module/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/allObject', name:'module_add')]
    public function view(ModuleRepository $moduleRepository,EventDispatcherInterface $eventDispatcher): Response
    {
        // Get the current user ID, you might have your own logic to retrieve it
        $userId = $this->getUser()->getId();

        // Fetch modules based on the user ID
        $modules = $moduleRepository->findModulesByUserId($userId);

        // Do something with the modules
        // ...
        // Inside your controller, service, or other relevant part of your code

        return $this->render('module/view.html.twig', [
            'modules' => $modules,
        ]);
    }

    #[Route('/spline', name: 'spline')]
    public function spline(ModuleRepository $moduleRepository, ChartBuilderInterface $chartBuilder): Response
    {
        $currentUser = $this->getUser();
        $modules = $moduleRepository->findBy(['user' => $currentUser]);
        $chartsData = [];

        foreach ($modules as $module) {
            $moduleData = $module->getData();
            $dataPoints = [];
            $measuredValues = [];
            $temperatures = [];
            $speeds = [];
            $averages = [];

            foreach ($moduleData as $data) {
                $dataPoints[] = $data->getWorkingTime()->format('Y-m-d H:i:s');
                $measuredValues[] = $data->getMeasuredValue();
                $temperatures[] = $data->getTemperature();
                $speeds[] = $data->getSpeed();
                $averages[] = ($data->getMeasuredValue() + $data->getTemperature() + $data->getSpeed()) / 3;
            }

            $chartsData[] = [
                'moduleId' => $module->getId(),
                'moduleCategory' => $module->getCategory(),
                'moduleName' => $module->getName(),
                'dataPoints' => $dataPoints,
                'measuredValues' => $measuredValues,
                'temperatures' => $temperatures,
                'speeds' => $speeds,
                'averages' => $averages,
                'sumMeasuredValues' => array_sum($measuredValues),
                'sumTemperatures' => array_sum($temperatures),
                'sumSpeeds' => array_sum($speeds)
            ];
        }

        return $this->render('module/spline.html.twig', [
            'chartsData' => $chartsData,
        ]);
    }

    #[Route('/{id}/D', name: 'delete_module', methods: ['POST'])]
    public function delete(Request $request, Module $module, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$module->getId(), $request->request->get('_token'))) {
            $entityManager->remove($module);
            $entityManager->flush();
        }

        return $this->redirectToRoute('module_add', [], Response::HTTP_SEE_OTHER);
    }

}
