<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Turbo Helper extension.
 *
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoTurboHelper\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Form;
use Contao\PageModel;
use Contao\StringUtil;
use InspiredMinds\ContaoTurboHelper\ContaoTurboHelperBundle;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces a load of a Contao form's target URL in case the target URL is outside
 * the current domain somewhere down the line. Otherwise it would be a CORS
 * violation with Turbo.
 */
class ForceReloadOnRedirectListener
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    #[AsHook('compileFormFields')]
    public function onCompileFormFields(array $fields, string $formId, Form $form): array
    {
        // Generate an automatic CSS ID, if none defined
        $this->getCssID($form);

        return $fields;
    }

    #[AsHook('processFormData')]
    public function onProcessFormData(array $submittedData, array $formData, array|null $files, array $labels, Form $form): void
    {
        if (!($request = $this->requestStack->getCurrentRequest())) {
            return;
        }

        if (!\in_array(ContaoTurboHelperBundle::STREAM_MEDIA_TYPE, $request->getAcceptableContentTypes(), true)) {
            return;
        }

        if (!($jumpTo = PageModel::findPublishedById($form->jumpTo))) {
            return;
        }

        $id = $this->getCssID($form);

        $response = new Response(
            '<turbo-stream action="append" target="'.$id.'"><template><script>window.location = \''.$jumpTo->getAbsoluteUrl()."';</script></template></turbo-stream>",
            Response::HTTP_OK,
            ['content-type' => ContaoTurboHelperBundle::STREAM_MEDIA_TYPE],
        );

        throw new ResponseException($response);
    }

    private function getCssID(Form $form): string|null
    {
        $attributes = StringUtil::deserialize($form->attributes, true) + ['', ''];

        if (!$attributes[0]) {
            $attributes[0] = 'auto_form_'.$form->id;
        }

        $form->attributes = serialize($attributes);

        return $attributes[0];
    }
}
