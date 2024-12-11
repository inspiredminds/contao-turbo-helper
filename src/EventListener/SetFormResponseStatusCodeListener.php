<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Turbo Helper extension.
 *
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoTurboHelper\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
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
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    #[AsHook('parseWidget')]
    public function onValidateFormField(string $buffer, Widget $widget): string
    {
        // Check if a widget has an error to force a different response status code.
        if ($widget->hasErrors() && ($request = $this->requestStack->getMainRequest())) {
            $request->attributes->set('_has_widget_error', true);
        }

        return $buffer;
    }

    #[AsHook('processFormData', priority: -10000)]
    public function onProcessFormData(array $submittedData, array $formData, array|null $files, array $labels, Form $form): void
    {
        if (method_exists($form, 'hasErrors') && $form->hasErrors() && ($request = $this->requestStack->getMainRequest())) {
            $request->attributes->set('_set_status', true);
        }
    }

    #[AsEventListener]
    public function onResponse(ResponseEvent $event): void
    {
        // Set the response status code to 422 if there was a widget with an error.
        if ($event->isMainRequest() && $event->getResponse()->isSuccessful() && $event->getRequest()->attributes->has('_has_widget_error')) {
            $event->getResponse()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
