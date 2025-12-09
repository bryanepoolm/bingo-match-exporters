<?php

namespace App\Domain\Enums;

enum Incoterm: string
{
    case EXW = 'exw'; // Ex Works
    case FCA = 'fca'; // Free Carrier
    case FOB = 'fob'; // Free On Board
    case CFR = 'cfr'; // Cost and Freight
    case CIF = 'cif'; // Cost Insurance Freight
    case DAP = 'dap'; // Delivered at Place
    case DDP = 'ddp'; // Delivered Duty Paid
}
