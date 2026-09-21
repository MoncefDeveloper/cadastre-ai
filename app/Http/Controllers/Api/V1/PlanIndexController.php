<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Api\PlanService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PlanIndexController extends Controller
{
    /**
     * Deliver cached subscription packages directly to Framer pricing components.
     * Enforces the single-envelope {"data": [...]} schema to prevent double-wrapping crashes.
     */
    public function __invoke(PlanService $planService): JsonResponse
    {
        return response()->json([
            'data' => $planService->getActivePlans(),
        ], Response::HTTP_OK);
    }
}
