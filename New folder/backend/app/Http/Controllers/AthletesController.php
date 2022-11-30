<?php

namespace App\Http\Controllers;

use App\Http\Requests\AthleteRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class AthletesController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $athletes = User::where('user_type', '<>', 'admin')->paginate(10);
        return $this->data($athletes);
    }


    public function store(AthleteRequest $request)
    {
        User::create($request->validated() + ['password' => '123123']);
        return $this->success('Account created successfully!');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        User::destroy($id);
        return $this->success('Athlete\'s data was removed successfully!');
    }
}
