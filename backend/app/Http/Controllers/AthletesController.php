<?php

namespace App\Http\Controllers;

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


    public function store(Request $request)
    {
        //
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
