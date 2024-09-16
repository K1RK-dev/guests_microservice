<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CountryResource;
use App\Models\Country;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CountriesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/countries",
     *     summary="Получение списка стран",
     *     description="Возвращает список всех существующих стран.",
     *     tags={"Countries"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *          response=200,
     *          description="Successful response",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="dial_code", type="string"),
     *                      @OA\Property(property="name", type="string"),
     *                  )
     *              )
     *          )
     *     )
     * )
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return CountryResource::collection(Country::all());
    }
}
