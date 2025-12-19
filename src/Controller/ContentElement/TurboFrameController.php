<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoTurboHelper\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(type: 'turbo_frame_start', category: 'turbo_frame', template: 'ce_turbo_frame_start')]
#[AsContentElement(type: 'turbo_frame_stop', category: 'turbo_frame', template: 'ce_turbo_frame_stop')]
class TurboFrameController extends AbstractContentElementController
{
    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        if ('turbo_frame_start' === $model->type && !$template->cssID) {
            $template->cssID = ' id="turbo-frame-'.$model->id.'"';
        }

        return $template->getResponse();
    }
}
