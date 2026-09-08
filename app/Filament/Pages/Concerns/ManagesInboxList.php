<?php

declare(strict_types=1);

namespace App\Filament\Pages\Concerns;

use App\Models\Thread;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

trait ManagesInboxList
{
    use WithPagination;

    // UI State for Column 1
    public string $activeTab = 'all'; // 'all', 'unread', 'read'
    public bool $sortDesc = true;
    public string $searchQuery = '';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage(); // Crucial: Reset pagination when switching tabs
    }

    public function toggleSort(): void
    {
        $this->sortDesc = !$this->sortDesc;
        $this->resetPage(); // Crucial: Reset pagination when sorting
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage(); // Crucial: Reset pagination when searching
    }

    /**
     * Strictly uses Filament's generic 'primary' color
     * so it respects your global theme choices.
     */
    public function getAvatarColor(int $clientId): string
    {
        // 5 distinct colors, exactly the same saturation and dark mode opacity
        $colors = [
            'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400',
            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400',
            'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400',
            'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-400',
            'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/50 dark:text-cyan-400',
        ];
        return $colors[$clientId % count($colors)];
    }

    #[Computed]
    public function threads()
    {
        $query = Thread::with(['client'])
            ->withCount(['messages as is_unread_count' => fn($q) => $q->where('is_unread', true)]);

        // 1. Search Filtering
        if (filled($this->searchQuery)) {
            $search = '%' . $this->searchQuery . '%';
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', $search)
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        // 2. Tab Filtering
        if ($this->activeTab === 'unread') {
            $query->where('is_unread', true);
        } elseif ($this->activeTab === 'read') {
            $query->where('is_unread', false);
        }

        // 3. Sorting
        if ($this->sortDesc) {
            $query->orderByDesc('last_message_at');
        } else {
            $query->orderBy('last_message_at');
        }

        // 4. Pagination (Limit to 10 per page)
        return $query->paginate(10);
    }

    // --- Badge Counters ---

    #[Computed]
    public function allCount(): int
    {
        return Thread::count();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return Thread::where('is_unread', true)->count();
    }

    #[Computed]
    public function readCount(): int
    {
        return Thread::where('is_unread', false)->count();
    }
}
