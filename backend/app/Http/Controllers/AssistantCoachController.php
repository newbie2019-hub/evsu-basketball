<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistantCoachRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AssistantCoachController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $coach = User::with('players.athlete')->where('user_type', '<>', 'admin')
            ->where('position', 'Assistant-Coach')
            ->where(function ($query) use ($request) {
                $query->when($request->search, fn ($query, $search)
                => $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%'));
            })->orderBy('first_name', 'asc')->paginate($request->per_page);

        return $this->data($coach);
    }

    public function store(AssistantCoachRequest $request)
    {
        User::create($request->validated() + ['password' => '123123']);
        return $this->success('Account created successfully!');
    }

    public function update(AssistantCoachRequest $request, User $user)
    {
        $user->update($request->validated());
        return $this->success('Assistant Coach\'s data updated successfully!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->success('Assistant Coach data was removed successfully!');
    }


    public function getCoaches(Request $request)
    {
        $coach = User::where('user_type', '<>', 'admin')
            ->where('position', 'Assistant-Coach')
            ->when($request->search, fn ($query, $search)
            => $query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%'))
            ->latest()->take(10)->get(['id', 'first_name', 'last_name', 'email']);

        return $this->data($coach);
    }
}
