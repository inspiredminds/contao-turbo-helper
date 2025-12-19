<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoTurboHelper;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoTurboHelperBundle extends Bundle
{
    public const STREAM_MEDIA_TYPE = 'text/vnd.turbo-stream.html';

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
