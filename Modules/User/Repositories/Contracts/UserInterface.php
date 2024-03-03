<?php

namespace Modules\User\Repositories\Contracts;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Models\User;

interface UserInterface
{
    public function all(string $search, int $per_page, string $dir, string $sortCol): LengthAwarePaginator;
    public function find(string $username): ?User;
    public function create(array $data): User;
    public function update($id, array $data);
    public function delete($id);
}
