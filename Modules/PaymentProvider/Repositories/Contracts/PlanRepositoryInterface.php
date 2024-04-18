<?php

namespace Modules\PaymentProvider\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\PaymentProvider\Models\Plan;

interface PlanRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): Plan;
    public function create(array $data): Plan;
    public function update(array $data, int $id): plan;
    public function delete(int $id): void;
}
