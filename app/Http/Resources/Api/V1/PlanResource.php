<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

/**
 * @mixin Plan
 */
class PlanResource extends JsonResource
{
    /**
     * Transform the Plan model into a normalized payload for Framer pricing components.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currency = $this->currency ?? 'USD';
        $monthlyDollars = (int) round($this->price_monthly / 100);
        $yearlyDollars = (int) round($this->price_yearly / 100);

        // Dynamically compute discount label if the yearly tier offers free months
        $discountLabel = null;
        if ($this->price_monthly > 0 && $this->price_yearly < ($this->price_monthly * 12)) {
            $monthsFree = (int) round((($this->price_monthly * 12) - $this->price_yearly) / $this->price_monthly);
            $discountLabel = $monthsFree === 1 ? '1 Month Free' : "{$monthsFree} Months Free";
        }

        // Derive short brand tier name (e.g. "Starter", "Professional", "Enterprise")
        $tierName = explode(' ', (string) $this->name)[0] ?? 'Tier';

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'currency'    => $currency,
            'is_popular'  => (bool) $this->is_featured,
            'price' => [
                'monthly' => [
                    'cents'     => (int) $this->price_monthly,
                    'dollars'   => $monthlyDollars,
                    'formatted' => Number::currency($monthlyDollars, in: $currency, precision: 0), // "$49"
                ],
                'yearly' => [
                    'cents'          => (int) $this->price_yearly,
                    'dollars'        => $yearlyDollars,
                    'formatted'      => Number::currency($yearlyDollars, in: $currency, precision: 0), // "$490"
                    'discount_label' => $discountLabel,
                ],
            ],
            'features'    => $this->features ?? [],
            'limits'      => $this->limits ?? [],
            'cta'         => [
                'label' => "Deploy {$tierName}",
                'url'   => rtrim((string) config('app.url', 'http://localhost'), '/') . '/admin/login',
            ],
        ];
    }
}
