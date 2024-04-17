<?php

namespace Modules\CarCheck\Repositories\Contracts;

use Illuminate\Http\Request;

interface CarCheckRepositoryInterface
{
    function checkCarRegNumber(string $reg_number);
}
