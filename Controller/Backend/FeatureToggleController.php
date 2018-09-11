<?php

namespace FeatureToggleBundle\Controller\Backend;

use Doctrine\ORM\EntityManager;
use FeatureToggleBundle\Entity\FeatureToggle;
use FeatureToggleBundle\Entity\FeatureToggleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Class FeatureToggleController
 * @package FeatureToggleBundle\Controller\Backend
 */
class FeatureToggleController extends Controller
{

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
        if($request->getMethod() == 'POST'){
            foreach ($this->getDoctrine()->getRepository(FeatureToggle::class)->findAll() as $featureToggle){
                $featureToggle->setActive(isset($request->request->get('features')[$featureToggle->getId()]));

                if($this->get("security.authorization_checker")->isGranted('ROLE_SUPER_ADMIN')){
                    $featureToggle->setDescription($request->request->get('description')[$featureToggle->getId()]);
                    $featureToggle->setPublic(isset($request->request->get('public')[$featureToggle->getId()]));
                }
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice','Features aktualisiert');
        }
        if($this->get("security.authorization_checker")->isGranted('ROLE_SUPER_ADMIN')){
            $filter = [];
        } else{
            $filter = ['public' => 1];
        }
        return $this->render('@FeatureToggle/Backend/feature_toggle.html.twig',
            [
                'features' => $this->getDoctrine()->getRepository(FeatureToggle::class)->findBy(
                    $filter,
                    [
                        'feature_name' => 'asc'
                    ]
                )
            ]
        );
    }

    /**
     * @Route("feature-toggle/delete/{id}", name="feature_toggle_delete")
     * @ParamConverter("featureToggle", class="FeatureToggleBundle:FeatureToggle")
     * @param $featureToggle FeatureToggle
     * @return RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteFeature($featureToggle){
        $this->getDoctrine()->getManager()->remove($featureToggle);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('notice', 'Feature '.$featureToggle->getFeatureName(). ' entfernt');
        return $this->redirectToRoute('feature_toggle');

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
            $this->getDoctrine()->getManager()->persist($featureToggle);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', 'Feature '.$featureToggle->getFeatureName().' erstellt');
        }
        return $this->redirectToRoute('feature_toggle');
    }
}
