<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Turbo Helper extension.
 *
 * (c) INSPIRED MINDS
 *
 * @license LGPL-3.0-or-later
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
