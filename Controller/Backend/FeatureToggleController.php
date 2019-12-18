<?php

namespace FeatureToggleBundle\Controller\Backend;

use FeatureToggleBundle\Entity\FeatureToggle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureToggleController implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param Request $request
     * @return Response
     * @Route("/feature-toggle", name="feature_toggle")
     */
    public function featureToggleAction(Request $request){
        if($request->getMethod() == 'POST'){
            foreach ($this->container->get('doctrine.orm.entity_manager')->getRepository(FeatureToggle::class)->findAll() as $featureToggle){
                $featureToggle->setActive(isset($request->request->get('features')[$featureToggle->getId()]));

                if($this->container->get("security.authorization_checker")->isGranted('ROLE_SUPER_ADMIN')){
                    $featureToggle->setDescription($request->request->get('description')[$featureToggle->getId()]);
                    $featureToggle->setPublic(isset($request->request->get('public')[$featureToggle->getId()]));
                }
            }
            $this->container->get('doctrine.orm.entity_manager')->flush();
            $this->container->get('session')->getFlashBag()->add('notice','Features aktualisiert');
        }
        if($this->container->get("security.authorization_checker")->isGranted('ROLE_SUPER_ADMIN')){
            $filter = [];
        } else{
            $filter = ['public' => 1];
        }
        return new Response($this->container->get('twig')->render('@FeatureToggle/Backend/feature_toggle.html.twig',
            [
                'features' => $this->container->get('doctrine.orm.entity_manager')->getRepository(FeatureToggle::class)->findBy(
                    $filter,
                    [
                        'feature_name' => 'asc'
                    ]
                )
            ]
        ));
    }

    /**
     * @Route("feature-toggle/delete/{id}", name="feature_toggle_delete")
     * @ParamConverter("featureToggle", class="FeatureToggleBundle:FeatureToggle")
     */
    public function deleteFeature($featureToggle){
        $this->container->get('doctrine.orm.entity_manager')->remove($featureToggle);
        $this->container->get('doctrine.orm.entity_manager')->flush();
        $this->container->get('session')->getFlashBag()->add('notice', 'Feature '.$featureToggle->getFeatureName(). ' entfernt');
        return $this->redirectToRoute('feature_toggle');

    }

    /**
     * @Route("feature-toggle/add", name="feature_toggle_add")
     */
    public function addFeatureAction(Request $request){
        if($request->request->get('feature_name')){
            $featureToggle = new FeatureToggle();
            $featureToggle->setPublic($request->request->get('feature_public',0))
                ->setDescription($request->request->get('feature_description'))
                ->setFeatureName($request->request->get('feature_name'))
                ->setActive($request->request->get('feature_active', 0));
            $this->container->get('doctrine.orm.entity_manager')->persist($featureToggle);
            $this->container->get('doctrine.orm.entity_manager')->flush();
            $this->container->get('session')->getFlashBag()->add('notice', 'Feature '.$featureToggle->getFeatureName().' erstellt');
        }
        return $this->redirectToRoute('feature_toggle');
    }

    protected function redirectToRoute(string $routename): RedirectResponse
    {
        $route = $this->container->get('router')->generate($routename);
        return new RedirectResponse($route);
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
