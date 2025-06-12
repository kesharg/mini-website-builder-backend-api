<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SectionType extends Enum
{
    const HEADER  = 'header';
    const TEXT   = 'text';
    const HTML   = 'html';
    const IMAGE   = 'image';

}
