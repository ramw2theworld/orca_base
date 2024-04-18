<?php

namespace Modules\PaymentProvider\Repositories\Contracts;

use Modules\PaymentProvider\Models\Currency;

interface CurrencyRepositoryInterface
{
    public function all();
    public function create(array $data): Currency;
    public function find(int $id): Currency;
    public function update(array $data, int $id): Currency;
    public function delete(int $id):void;
}
