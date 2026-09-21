<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Api\FaqService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FaqIndexController extends Controller
{
    /**
     * Deliver cached knowledge base FAQs directly to Framer accordion components.
     * Enforces the single-envelope {"data": [...]} schema to prevent double-wrapping.
     */
    public function __invoke(FaqService $faqService): JsonResponse
    {
        return response()->json([
            'data' => $faqService->getActiveFaqs(),
        ], Response::HTTP_OK);
    }
}
