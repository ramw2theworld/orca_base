<?php

namespace Modules\PaymentProvider\Repositories\Contracts;

use Modules\PaymentProvider\Models\PaymentProvider;

interface CurrencyRepositoryInterface
{
    public function all();
    public function create(array $data): PaymentProvider;
    public function find(int $id): PaymentProvider;
    public function update(array $data, int $id): PaymentProvider;
    public function delete(int $id):void;
}
