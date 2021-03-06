<?php

namespace Knplabs\Symfony2BundlesBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * Main controller
 *
 * @package Symfony2Bundles
 */
class MainController
{
    protected $response;
    protected $templating;

    /**
     * Constructor
     *
     * @param  EngineInterface $templating
     */
    public function __construct(Response $response, EngineInterface $templating)
    {
        $this->response = $response;
        $this->templating = $templating;
    }

    public function indexAction()
    {
        return $this->templating->renderResponse('KnplabsSymfony2BundlesBundle:Main:index.html.twig', array(), $this->response);
    }

    public function getRankCodeAction()
    {
        try {
            $scoreMethod = new \ReflectionMethod('Knplabs\Symfony2BundlesBundle\Entity\Repo', 'recalculateScore');
            $scoreMethodDefinition = $scoreMethod->getDocComment()."\n";
            $contents = file($scoreMethod->getDeclaringClass()->getFileName());
            for ($i = $scoreMethod->getStartLine()-1; $i < $scoreMethod->getEndLine(); $i++) {
                $scoreMethodDefinition.= $contents[$i];
            }
        } catch (Exception $e) {
            $scoreMethodDefinition = '';
        }

        $this->response->setContent($scoreMethodDefinition);
        $this->response->setStatusCode(200);
        // TODO: how could we ensure the cache is cleared if the code changes?
        $this->response->setTtl(3600);

        return $this->response;
    }

    public function apiAction()
    {
        $text = file_get_contents(__DIR__.'/../Resources/doc/02-Api.markdown');

        return $this->templating->renderResponse('KnplabsSymfony2BundlesBundle:Main:api.html.twig', array('text' => $text), $this->response);
    }

    public function notFoundAction()
    {
        $this->response->setStatusCode(404);

        return $this->templating->renderResponse('KnplabsSymfony2BundlesBundle:Main:notFoundAction.html.twig', array(), $this->response);
    }
}
