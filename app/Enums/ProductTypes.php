<?php

namespace App\Enums;

enum ProductTypes: string
{
    case ESTATE = 'estate';
    case FARM = 'farm';
    case SCHOOL = 'school';
    case CAR = 'car';
    case ELECTRONIC = 'electronic';
    case BUILDING = 'building';
}