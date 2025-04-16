<?php

namespace App\Enum;

enum ProductStatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';

    public static function labels(): array
    {
        return [
            Self::Draft->value => __('Draft'),
            Self::Published->value => __('Published'), // __ for tranlate string helper - to Translate the given message.
        ];
    }

    public static function colors(): array
    {
        return [
            'gray' => Self::Draft->value,
            'success' => Self::Published->value,
        ];
    }
}
