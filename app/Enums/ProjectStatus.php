<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Upcoming = 'upcoming';
    case Receiving = 'receiving';
    case Closed = 'closed';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Upcoming => 'Sắp mở nhận hồ sơ',
            self::Receiving => 'Đang nhận hồ sơ',
            self::Closed => 'Đã đóng nhận hồ sơ',
            self::Completed => 'Đã bàn giao',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $status): array => ['value' => $status->value, 'label' => $status->label()],
            self::cases(),
        );
    }
}
