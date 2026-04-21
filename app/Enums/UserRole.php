<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'ADMIN';
    case Manager = 'MANAGER';
    case FuelOperator = 'FUEL_OPERATOR';
    case ShopManager = 'SHOP_MANAGER';
    case Cashier = 'CASHIER';

    /**
     * @return array<int, array{code: string, name: string}>
     */
    public static function seedData(): array
    {
        return [
            ['code' => self::Admin->value, 'name' => 'Administrateur'],
            ['code' => self::Manager->value, 'name' => 'Manager'],
            ['code' => self::FuelOperator->value, 'name' => 'Opérateur carburant'],
            ['code' => self::ShopManager->value, 'name' => 'Responsable boutique'],
            ['code' => self::Cashier->value, 'name' => 'Caissier'],
        ];
    }
}
