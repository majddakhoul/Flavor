<?php

namespace App\Enums;

enum UserType: string
{
    case Manager = 'Manager';
    case Employee = 'Employee';
    case Customer = 'Customer';

    public function label(): string
    {
        return __('enums.user_type.' . $this->value);
    }

    public function isStaff(): bool
    {
        return $this !== self::Customer;
    }

    public static function options(): array
    {
        return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
