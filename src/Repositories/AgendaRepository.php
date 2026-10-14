<?php

namespace Bale\Umpak\Repositories;

use Bale\Umpak\Contracts\AgendaRepositoryInterface;
use Bale\Umpak\DTOs\AgendaData;
use Bale\Umpak\Exceptions\AgendaNotFoundException;
use Bale\Umpak\Models\Agenda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AgendaRepository implements AgendaRepositoryInterface
{
    public function getById(string $id): AgendaData
    {
        $agenda = $this->findById($id);

        if ($agenda === null) {
            throw AgendaNotFoundException::forId($id);
        }

        return $agenda;
    }

    public function findById(string $id): ?AgendaData
    {
        return Agenda::visible()
            ->whereKey($id)
            ->first()
            ?->toData();
    }

    public function upcoming(int $limit = 10): Collection
    {
        return Agenda::visible()
            ->upcoming()
            ->limit($limit)
            ->get()
            ->map(fn (Agenda $agenda) => $agenda->toData());
    }

    public function latest(int $limit = 10): Collection
    {
        return Agenda::visible()
            ->orderByDesc('start_date')
            ->limit($limit)
            ->get()
            ->map(fn (Agenda $agenda) => $agenda->toData());
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Agenda::visible()
            ->orderByDesc('start_date')
            ->paginate($perPage);
    }
}
