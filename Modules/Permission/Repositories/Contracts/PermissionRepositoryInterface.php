<?php

namespace Modules\Permission\Repositories\Contracts;

use Modules\Permission\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PermissionRepositoryInterface
{
    public function all(string $search = null, int $per_page, string $dir, string $sortCol): LengthAwarePaginator;
    public function find(string $slug): ?Permission;
    public function create(array $data): Permission;
    public function update(string $slug, array $data): ?Permission;
    public function delete(string $slug): void;
}
