<?php

namespace FeatureToggleBundle\Controller\Backend;

use Doctrine\ORM\EntityManager;
use FeatureToggleBundle\Entity\FeatureToggle;
use FeatureToggleBundle\Entity\FeatureToggleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Class FeatureToggleController
 * @package FeatureToggleBundle\Controller\Backend
 * @Route(service="feature_toggle.controller")
 */
class FeatureToggleController
{
    /**
     * @var FeatureToggleRepository
     */
    private $toggleRepository;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var \Twig_Environment
     */
    private $twig_Environment;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * FeatureToggleController constructor.
     * @param FeatureToggleRepository $toggleRepository
     * @param Router $router
     * @param Session $session
     * @param \Twig_Environment $twig_Environment
     * @param EntityManager $entityManager
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(
        FeatureToggleRepository $toggleRepository,
        Router $router,
        Session $session,
        \Twig_Environment $twig_Environment,
        EntityManager $entityManager,
        AuthorizationChecker $authorizationChecker

    )
    {
        $this->toggleRepository = $toggleRepository;
        $this->router = $router;
        $this->session = $session;
        $this->twig_Environment = $twig_Environment;
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @Route("/feature-toggle", name="feature_toggle")
     */
    public function featureToggleAction(Request $request){
        if($request->request->has('features')){
            foreach ($request->request->get('features') as $id => $feature){
                /** @var FeatureToggle $featureToggle */
                $featureToggle = $this->toggleRepository->find($id);
                if(!$featureToggle){
                    continue;
                }
                $featureToggle->setActive($request->request->get('features')[$id]);
                if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
                    $featureToggle->setDescription($request->request->get('description')[$id]);
                    $featureToggle->setPublic($request->request->get('public')[$id]);
                }
                $this->entityManager->persist($featureToggle);
            }
            $this->session->getFlashBag()->add('notice','Features aktualisiert');
            $this->entityManager->flush();
        }
        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
            $filter = [];
        } else{
            $filter = ['public' => 1];
        }
        $output = $this->twig_Environment->render('@FeatureToggle/Backend/feature_toggle.html.twig',
            [
                'features' => $this->toggleRepository->findBy(
                    $filter,
                    [
                        'feature_name' => 'asc'
                    ]
                )
            ]
        );
        return new Response($output);
    }

    /**
     * @Route("feature-toggle/delete/{id}", name="feature_toggle_delete")
     * @ParamConverter("featureToggle", class="FeatureToggleBundle:FeatureToggle")
     * @param $featureToggle FeatureToggle
     * @return RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteFeature($featureToggle){
        $this->entityManager->remove($featureToggle);
        $this->entityManager->flush();
        $this->session->getFlashBag()->add('notice', 'Feature '.$featureToggle->getFeatureName(). ' entfernt');
        $route = $this->router->generate('feature_toggle');
        return new RedirectResponse($route);

    }

    /**
     * @Route("feature-toggle/add", name="feature_toggle_add")
     * @param Request $request
     * @return RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addFeatureAction(Request $request){
        if($request->request->get('feature_name')){
            $featureToggle = new FeatureToggle();
            $featureToggle->setPublic($request->request->get('feature_public',0))
                ->setDescription($request->request->get('feature_description'))
                ->setFeatureName($request->request->get('feature_name'))
                ->setActive($request->request->get('feature_active', 0));
            $this->entityManager->persist($featureToggle);
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('notice', 'Feature '.$featureToggle->getFeatureName().' erstellt');
        }
        $route = $this->router->generate('feature_toggle');
        return new RedirectResponse($route);
    }
}
