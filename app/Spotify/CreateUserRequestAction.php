<?php

namespace App\Spotify;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use Illuminate\Support\Collection;

class CreateUserRequestAction
{
    /**
     * Execute the pending request and return the response from the Spotify API.
     *
     * @param PendingRequest $pendingRequest
     * @param string $accessToken
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function execute(PendingRequest $pendingRequest,string $accessToken): array
    {
        $endpoint = $pendingRequest->endpoint;
        $responseArrayKey = $pendingRequest->responseArrayKey;

        $acceptedParams = collect($pendingRequest->acceptedParams);
        $requestedParams = collect($pendingRequest->requestedParams);
        $finalParams = $this->createFinalParams($acceptedParams, $requestedParams);

        $response = resolve(SpotifyUserRequest::class,['accessToken'=>$accessToken])->get($endpoint, $finalParams);

        if ($responseArrayKey) {
            return $response[$responseArrayKey];
        }

        return $response;
    }

    /**
     * This merges the requested and accepted parameters and outputs the final parameters ready for the API call.
     *
     * @param Collection $acceptedParams
     * @param Collection $requestedParams
     * @return array
     */
    private function createFinalParams(Collection $acceptedParams, Collection $requestedParams): array
    {
        $intersectedRequestedParams = $requestedParams->intersectByKeys($acceptedParams);

        $mergedParams = $acceptedParams->merge($intersectedRequestedParams);

        $validParams = $mergedParams->filter(function ($value) {
            return $value !== null;
        });

        return $validParams->toArray();
    }
}
