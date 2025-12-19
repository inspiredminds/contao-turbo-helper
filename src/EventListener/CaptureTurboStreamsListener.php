<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoTurboHelper\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Template;
use InspiredMinds\ContaoTurboHelper\ContaoTurboHelperBundle;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Contracts\Service\ResetInterface;

class CaptureTurboStreamsListener implements ResetInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        /**
         * @var list<string>
         */
        private array $streams = [],
    ) {
    }

    public function reset(): void
    {
        $this->streams = [];
    }

    #[AsHook('parseTemplate')]
    public function onParseTemplate(Template $template): void
    {
        $template->isTurboStream = Template::once(fn (): bool => $this->isTurboStream());

        $template->startTurboStream = static function (): void {
            ob_start();
        };

        $template->endTurboStream = function (): void {
            $this->streams[] = ob_get_clean();
        };
    }

    #[AsEventListener]
    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        // We need to vary on 'Accept' as the response might differ depending on whether this was a stream request or not.
        $response->setVary(array_unique([...$response->getVary(), 'Accept']));

        // If this is a stream request override the response content with the recorded streams.
        if ($this->streams && $this->isTurboStream($event->getRequest())) {
            $response->setContent(implode('', $this->streams));
            $response->headers->set('Content-Type', ContaoTurboHelperBundle::STREAM_MEDIA_TYPE);
        }
    }

    private function isTurboStream(Request|null $request = null): bool
    {
        if (!($request ??= $this->requestStack->getMainRequest())) {
            return false;
        }

        return \in_array(ContaoTurboHelperBundle::STREAM_MEDIA_TYPE, $request->getAcceptableContentTypes(), true);
    }
}
