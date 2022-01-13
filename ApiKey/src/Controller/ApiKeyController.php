<?php


namespace Drupal\ApiKey\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiKeyController extends ControllerBase
{
    protected $configFactory;

    /**
     * ApiKeyController constructor.
     * @param ConfigFactoryInterface $configFactory
     */
    public function __construct(ConfigFactoryInterface $configFactory) {
        $this->configFactory = $configFactory;
    }

    /**
     * @param ContainerInterface $container
     * @return ControllerBase|ApiKeyController
     */
    public static function create(ContainerInterface $container) {
        $configFactory = $container->get('config.factory');

        return new static($configFactory);
    }

    /**
     * @param $type
     * @param $nid
     * @return JsonResponse
     */
    public function ApiKey($type, $nid) {
        $node = \Drupal\node\Entity\Node::load($nid);
       // echo '<pre>' ; print_r($node); die;
        if ($type != $node->getType() || !$this->checkSiteApiKey()) {
            return new JsonResponse('{
                "error": {
                    "code": 403,
                    "message": "Access Denied"
                }
            }', 403, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse($node->get('body')->value, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @return bool
     */
    public function checkSiteApiKey() {
        $site_config = $this->configFactory->get('ApiKey.site');
        $siteapikey = $site_config->get('siteapikey');
        if ($siteapikey != "No API Key yet" && isset($siteapikey)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}