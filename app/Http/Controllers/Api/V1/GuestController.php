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
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return GuestResource::collection(Guest::all());
    }

    /**
     * Store a newly created resource in storage.
     *
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
     * Display the specified resource.
     *
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
     * Update the specified resource in storage.
     *
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
     * Remove the specified resource from storage.
     *
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
