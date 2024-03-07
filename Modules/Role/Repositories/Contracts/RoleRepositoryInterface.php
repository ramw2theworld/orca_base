<?php

namespace Modules\Role\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Modules\Role\Models\Role;

interface RoleRepositoryInterface
{
    public function all(string $search = null, int $per_page, string $dir, string $sortCol): LengthAwarePaginator;
    public function find(string $slug): ?Role;
    public function create(array $data): Role;
    public function update(string $slug, array $data): ?Role;
    public function delete(string $slug): void;

    public function attachOrDetachPermissionsToRole(string $slug, Request $request): ?Role;
}
