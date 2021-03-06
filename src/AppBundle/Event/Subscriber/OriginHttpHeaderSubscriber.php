<?php

namespace AppBundle\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OriginHttpHeaderSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $clientUrl;

    /**
     * @param string $clientUrl
     */
    public function __construct($clientUrl)
    {
        $this->clientUrl = $clientUrl;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequest()->getMethod() === Request::METHOD_OPTIONS) {
            $response = new Response();

            $response->headers->add([
                'Access-Control-Allow-Origin' => $this->clientUrl,
                'Access-Control-Allow-Methods' => implode(',', [
                    Request::METHOD_GET,
                    Request::METHOD_POST,
                    Request::METHOD_PUT,
                    Request::METHOD_DELETE
                ]),
                'Access-Control-Allow-Headers' => 'X-AUTH-TOKEN'
            ]);

            $event->setResponse($response);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        $response->headers->add([
            'Access-Control-Allow-Origin' => $this->clientUrl
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 100],
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }
}
