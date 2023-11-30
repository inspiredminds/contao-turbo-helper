<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Turbo Helper extension.
 *
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoTurboHelper\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Widget;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Sets the response status code to 422 Unprocessable Entity if there was a validation error in a Contao form.
 */
class SetFormResponseStatusCodeListener
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    #[AsHook('validateFormField')]
    public function onValidateFormField(Widget $widget): Widget
    {
        // Check if a form widget has an error and then sets a request attribute to force a different response status code.
        if ($widget->hasErrors() && ($request = $this->requestStack->getMainRequest())) {
            $request->attributes->set('_set_status', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $widget;
    }

    #[AsEventListener]
    public function onResponse(ResponseEvent $event): void
    {
        // Set the response status code, if one was set in the request attributes.
        if ($event->isMainRequest() && $event->getRequest()->attributes->has('_set_status')) {
            $event->getResponse()->setStatusCode($event->getRequest()->attributes->get('_set_status'));
        }
    }
}
