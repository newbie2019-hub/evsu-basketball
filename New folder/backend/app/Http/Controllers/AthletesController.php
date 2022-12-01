<?php

namespace App\Http\Controllers;

use App\Http\Requests\AthleteRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class AthletesController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $athletes = User::where('user_type', '<>', 'admin')
        ->when($request->search, fn ($query, $search)
            => $query->where('first_name', 'like', '%' . $search . '%')
            ->orWhere('last_name', 'like', '%'. $search .'%')
            ->orWhere('email', 'like', '%'. $search .'%'))
        ->paginate($request->per_page);

        return $this->data($athletes);
    }

    public function store(AthleteRequest $request)
    {
        User::create($request->validated() + ['password' => '123123']);
        return $this->success('Account created successfully!');
    }

    public function update(AthleteRequest $request, User $user)
    {
        $user->update($request->validated());
        return $this->success('User data updated successfully!');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return $this->success('Athlete\'s data was removed successfully!');
    }
}
