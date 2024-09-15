<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Http\Resources\V1\GuestResource;
use App\Models\Country;
use App\Models\Guest;
use App\Utils\PhoneNumberUtils;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GuestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/guests",
     *     summary="Получение списка гостей",
     *     description="Возвращает список всех существующих гостей.",
     *     tags={"Guests"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *          response=200,
     *          description="Successful response",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="firstname", type="string"),
     *                      @OA\Property(property="lastname", type="string"),
     *                      @OA\Property(property="phone", type="string"),
     *                      @OA\Property(property="email", type="string"),
     *                      @OA\Property(property="country_id", type="integer"),
     *                  )
     *              )
     *          )
     *     )
     * )
     *
     * Display a listing of the resource.
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return GuestResource::collection(Guest::all());
    }

    /**
     * @OA\Post(path="/api/v1/guests",
     *     summary="Создание нового гостя",
     *     description="Создает нового гостя по указаным параметрам.",
     *     tags={"Guests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="firstname", in="query", description="Имя гостя", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="lastname", in="query", description="Фамилия гостя", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="phone", in="query", description="Номер телефона гостя", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="email", in="query", description="Электронная почта гостя", required=false,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="country_id", in="query", description="Идентификатор страны", required=false,
     *          @OA\Schema(type="int")
     *     ),
     *     @OA\Response(response=201, description="Ответ содержит полные данные о созданном госте",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="firstname", type="string"),
     *                  @OA\Property(property="lastname", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="country_id", type="integer"),
     *              ),
     *          )
     *     )
     * )
     *
     * Store a newly created resource in storage.
     * @param  StoreGuestRequest  $request
     * @return GuestResource | JsonResponse
     */
    public function store(StoreGuestRequest $request)
    {
        $data = $request->all();
        if(!isset($data['country_id'])){
            $dialCode = PhoneNumberUtils::parseDialCodeByPhoneNumber($request->input('phone', ''));
            if(empty($dialCode)){
                return response()->json([
                    'message' => 'Cannot find country by phone number'
                ]);
            }
            $data['country_id'] = Country::getCountryByDialCode($dialCode);
        }
        return new GuestResource(Guest::create($data));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/guests/{id}",
     *     summary="Получение данных о госте по идентификатору.",
     *     description="Возвращает данные о госте, по идентификатору.",
     *     tags={"Guests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful response",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="firstname", type="string"),
     *                  @OA\Property(property="lastname", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="country_id", type="integer"),
     *              ),
     *          )
     *     )
     * )
     *
     * Display the specified resource.
     * @param  Guest  $guest
     * @return GuestResource | JsonResponse
     */
    public function show(Guest $guest)
    {
        try{
            return new GuestResource($guest);
        }
        catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/guests/{id}",
     *     summary="Обновление данных о госте",
     *     description="Обновляет данные гостя по уникальному идентификатору.",
     *     tags={"Guests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(name="firstname", in="query", description="Имя гостя", required=false,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="lastname", in="query", description="Фамилия гостя", required=false,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="phone", in="query", description="Номер телефона гостя", required=false,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="email", in="query", description="Электронная почта гостя", required=false,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="country_id", in="query", description="Идентификатор страны", required=false,
     *          @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful response",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="firstname", type="string"),
     *                  @OA\Property(property="lastname", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="country_id", type="integer"),
     *              ),
     *          )
     *     )
     * )
     *
     * Update the specified resource in storage.
     * @param  UpdateGuestRequest  $request
     * @param  Guest  $guest
     * @return GuestResource
     */
    public function update(UpdateGuestRequest $request, Guest $guest)
    {
        $guest->update($request->all());
        return new GuestResource($guest);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/guests/{id}",
     *     summary="Удаление гостя.",
     *     description="Удаляет данные гостя по уникальному идентификатору.",
     *     tags={"Guests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful response",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="message", type="string", example="Success"),
     *          )
     *     )
     * )
     *
     * Remove the specified resource from storage.
     * @param  Guest  $guest
     * @return JsonResponse
     */
    public function destroy(Guest $guest)
    {
        $guest->delete();
        return response()->json([
            'message' => 'Success'
        ]);
    }
}
