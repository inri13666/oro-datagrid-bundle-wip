<?php

namespace Oro\Bundle\DataGridBundle\Controller;

use Oro\Bundle\DataGridBundle\Datagrid\Manager;
use Oro\Bundle\DataGridBundle\Datagrid\RequestParameterBagFactory;
use Oro\Bundle\DataGridBundle\Exception\UserInputErrorExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides the ability to control of the Grid
 */
class GridController extends AbstractController
{
    /**
     * @Route(
     *      "/{gridName}",
     *      name="oro_datagrid_index",
     *      requirements={"gridName"="[\w\:-]+"}
     * )
     *
     * @param string $gridName
     *
     * @return Response
     * @throws \Exception
     */
    public function getAction($gridName)
    {
        $gridManager = $this->get(Manager::class);
        $gridConfig  = $gridManager->getConfigurationForGrid($gridName);
        $acl         = $gridConfig->getAclResource();

        if ($acl && !$this->isGranted($acl)) {
            throw new AccessDeniedException('Access denied.');
        }

        $grid = $gridManager->getDatagridByRequestParams($gridName);

        try {
            $result = $grid->getData();
        } catch (\Exception $e) {
            if ($e instanceof UserInputErrorExceptionInterface) {
                return new JsonResponse(
                    [
                        'type'    => UserInputErrorExceptionInterface::TYPE,
                        'message' => $this
                            ->get(TranslatorInterface::class)->trans($e->getMessageTemplate(), $e->getMessageParams())
                    ],
                    500
                );
            }
            throw $e;
        }

        return new JsonResponse($result->toArray());
    }

    /**
     * @Route("/{gridName}/filter-metadata", name="oro_datagrid_filter_metadata", options={"expose"=true})
     */
    public function filterMetadataAction(Request $request, $gridName)
    {
        $filterNames = $request->query->get('filterNames', []);

        $gridManager = $this->get(Manager::class);
        $gridConfig  = $gridManager->getConfigurationForGrid($gridName);
        $acl         = $gridConfig->getAclResource();

        if ($acl && !$this->isGranted($acl)) {
            throw new AccessDeniedException('Access denied.');
        }

        $grid = $gridManager->getDatagridByRequestParams($gridName);
        $meta = $grid->getResolvedMetadata();

        $filterData = [];
        foreach ($meta['filters'] as $filter) {
            if (!in_array($filter['name'], $filterNames)) {
                continue;
            }

            $filterData[$filter['name']] = $filter;
        }

        return new JsonResponse($filterData);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getRenderParams(Request $request)
    {
        $renderParams      = $request->get('renderParams', []);
        $renderParamsTypes = $request->get('renderParamsTypes', []);

        foreach ($renderParamsTypes as $param => $type) {
            if (array_key_exists($param, $renderParams)) {
                switch ($type) {
                    case 'bool':
                    case 'boolean':
                        $renderParams[$param] = (bool)$renderParams[$param];
                        break;
                    case 'int':
                    case 'integer':
                        $renderParams[$param] = (int)$renderParams[$param];
                        break;
                }
            }
        }

        return $renderParams;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                RequestParameterBagFactory::class,
                Manager::class
            ]
        );
    }
}
