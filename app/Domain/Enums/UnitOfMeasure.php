<?php

namespace App\Domain\Enums;

enum UnitOfMeasure: string
{
    case KILOGRAMS = 'kg';
    case TONS = 'tons';
    case LITERS = 'liters';
    case PIECES = 'pieces';
    case CUBIC_METERS = 'm3';
    case POUNDS = 'lb';
    case GALLONS = 'gal';
    case BOXES = 'boxes';
    case PALLETS = 'pallets';
}
