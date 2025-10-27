<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Turbo Helper extension.
 *
 * (c) INSPIRED MINDS
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoTurboHelper\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Form;
use Contao\Widget;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Sets the response status code to 422 Unprocessable Entity if there was a
 * validation error in a Contao form.
 */
class SetFormResponseStatusCodeListener
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ScopeMatcher $scopeMatcher,
    ) {
    }

    #[AsHook('validateFormField', priority: -4096)]
    public function onValidateFormField(Widget $widget): Widget
    {
        // Check if a widget has an error to force a different response status code.
        if ($widget->hasErrors() && ($request = $this->requestStack->getMainRequest())) {
            $request->attributes->set('_contao_widget_error', true);
        }

        return $widget;
    }

    #[AsHook('processFormData', priority: -4096)]
    public function onProcessFormData(array $submittedData, array $formData, array|null $files, array $labels, Form $form): void
    {
        // Check if the form has an error to force a different response status code.
        if (method_exists($form, 'hasErrors') && $form->hasErrors() && ($request = $this->requestStack->getMainRequest())) {
            $request->attributes->set('_contao_widget_error', true);
        }
    }

    #[AsEventListener]
    public function onResponse(ResponseEvent $event): void
    {
        if (!$this->scopeMatcher->isFrontendMainRequest($event)) {
            return;
        }

        $request = $event->getRequest();

        if (!\in_array('text/vnd.turbo-stream.html', $request->getAcceptableContentTypes(), true)) {
            return;
        }

        $response = $event->getResponse();

        // Set the response status code to 422 if there was a form error.
        if (Response::HTTP_OK === $response->getStatusCode() && $request->attributes->has('_contao_widget_error')) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
