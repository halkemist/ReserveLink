<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Manage availability slots (authentified user).
 */
class AvailabilityController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();
        $availabilities = $authUser->availabilities;
        return $availabilities;
    }

    public function store(AvailabilityRequest $request)
    {
        $authUser = Auth::user();
        $authUser->availabilities->create($request->validated());
    }
}
