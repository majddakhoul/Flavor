<?php

namespace App\Enums;

enum EmployeePosition: string
{
    case Manager = 'Manager';
    case Chef = 'Chef';
    case Waiter = 'Waiter';
    case Security = 'Security';
    case Delivery = 'Delivery';

    public function label(): string
    {
        return __('enums.employee_position.' . $this->value);
    }

    public function abilities(): array
    {
        return match ($this) {
            self::Manager => ['catalog', 'inventory', 'floor', 'sales', 'people', 'reports'],
            self::Chef => ['catalog', 'inventory'],
            self::Waiter => ['floor', 'sales'],
            self::Delivery => ['sales'],
            self::Security => ['floor'],
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
