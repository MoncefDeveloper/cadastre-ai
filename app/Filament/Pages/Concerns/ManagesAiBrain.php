<?php

declare(strict_types=1);

namespace App\Filament\Pages\Concerns;

use App\Enums\Category\CategoryType;
use App\Enums\Thread\ThreadChannel;
use App\Jobs\ModifyAiDraftJob;
use App\Jobs\ApplyTemplateJob;
use App\Models\AiModifier;
use App\Models\Category;
use App\Models\Message;
use App\Models\Template;
use App\Models\Thread;
use Livewire\Attributes\Computed;
use Filament\Notifications\Notification;

trait ManagesAiBrain
{
    // Template Filters
    public ?int $templateCategoryFilter = null;
    public ?int $templateChannelFilter = 1; // Default: EMAIL

    #[Computed]
    public function activeModifiers()
    {
        // 1. GLOBAL GATE: If the agent lacks the permission, return an empty collection immediately
        if (! auth()->user()->can('use_advanced_ai_modifiers')) {
            return collect();
        }

        return AiModifier::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function templateCategories()
    {
        return Category::where('is_active', true)
            ->where('type', CategoryType::TEMPLATE)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function availableTemplates()
    {
        // 2. GLOBAL GATE: If the agent lacks the permission, return an empty collection immediately
        if (! auth()->user()->can('apply_legally_binding_templates')) {
            return collect();
        }

        $query = Template::with('category')
            ->where('is_active', true)
            ->addSelect([
                'usage_count' => Message::selectRaw('count(*)')
                    ->whereColumn('template_id', 'templates.id')
            ]);

        return $query->when($this->templateCategoryFilter, fn($q, $v) => $q->where('category_id', $v))
            ->when($this->templateChannelFilter, fn($q, $v) => $q->where('channel', $v))
            ->orderByDesc('usage_count')
            ->get();
    }

    public function applyAiModifier(int $modifierId): void
    {
        // 3. SECURE GUARD: Prevent Livewire request injection bypass
        if (! auth()->user()->can('use_advanced_ai_modifiers')) {
            Notification::make()
                ->title('Unauthorized Action')
                ->body('You lack permissions to use AI modifiers.')
                ->danger()
                ->send();
            return;
        }

        $modifier = AiModifier::find($modifierId);

        if (!$modifier || !$this->activeDraft) return;

        $this->draftUpdatedAt = $this->activeDraft->updated_at->toDateTimeString();
        $this->isGeneratingDraft = true;

        ModifyAiDraftJob::dispatch($this->activeDraft, $modifier->instruction);

        Notification::make()->title("Applying: {$modifier->label}")->success()->send();
    }

    public function applyTemplate(int $templateId): void
    {
        // 4. SECURE GUARD: Prevent Livewire request injection bypass
        if (! auth()->user()->can('apply_legally_binding_templates')) {
            Notification::make()
                ->title('Unauthorized Action')
                ->body('You lack permissions to apply templates.')
                ->danger()
                ->send();
            return;
        }

        $template = Template::find($templateId);
        $thread = Thread::find($this->activeThreadId);
        $user = auth()->user();

        if (!$template || !$thread) return;

        \Illuminate\Support\Facades\Log::info('[Template Verification] Initiating Template Application', [
            'agent' => $user ? $user->toArray() : 'System',
            'template_details' => $template->toArray(),
            'old_draft_html' => $this->activeDraft ? $this->activeDraft->body_html : 'None (No previous draft existed)'
        ]);

        if ($this->activeDraft) {
            $this->activeDraft->delete();
            $this->activeDraft = null;
            $this->draftForm->fill([]);
            unset($this->activeThread);
        }

        $this->draftUpdatedAt = null;
        $this->isGeneratingDraft = true;

        ApplyTemplateJob::dispatch($thread, $template, $user);

        $this->dispatch('jump-to-ai-tab');

        Notification::make()
            ->title("Writing via {$template->name}")
            ->body('Applying template instructions...')
            ->success()
            ->send();
    }
}
