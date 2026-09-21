<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Services\Api\ContactService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ContactStoreController extends Controller
{
    use ApiResponse;

    /**
     * Ingest public inquiries from Framer and dispatch notifications to active brokers.
     * Adheres to the architectural rule: ApiResponse is strictly used for mutation receipts.
     */
    public function __invoke(
        StoreContactRequest $request,
        ContactService $contactService
    ): JsonResponse {
        $contactService->storeContact(
            data: $request->validated(),
            ipAddress: $request->ip()
        );

        return $this->success(
            message: 'Advisory inquiry transmitted successfully. Our private office will respond within 24 business hours.',
            status: Response::HTTP_CREATED
        );
    }
}
