<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Turbo Helper extension.
 *
 * (c) INSPIRED MINDS
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['turboFrameTarget'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w50', 'maxlength' => 128],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['turboFrameAction'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
    'options' => [
        'advance',
        'replace',
    ],
    'sql' => ['type' => 'string', 'length' => 8, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['palettes']['turbo_frame_start'] = '{type_legend},type;{turbo_frame_legend},turboFrameTarget,turboFrameAction;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['turbo_frame_stop'] = '{type_legend},type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests;{invisible_legend:hide},invisible,start,stop';
