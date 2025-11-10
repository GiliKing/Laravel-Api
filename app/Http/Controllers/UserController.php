<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use HttpResponses;

    public function index()
    {
        return UserResource::collection(User::where('user_type', 'staff')->latest()->simplePaginate(20))
            ->additional([
                'success' => true,
                'message' => 'Users Fetched Successfully',
            ]);
    }

    public function create(StaffRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password']
        ]);

        $user->employee()->create([
            'salary' => $validated['salary'],
            'position' => $validated['position'],
            'department' => $validated['department']
        ]);

        return $this->success(
            UserResource::make($user),
            'User Fetched Successfully',
        );
    }

    public function show(User $user)
    {
        return $this->success(
            UserResource::make($user),
            'User Fetched Successfully',
        );
    }

    public function update(StaffRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ?? $user->password,
        ]);

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'salary' => $validated['salary'],
                'position' => $validated['position'],
                'department' => $validated['department'],
            ]
        );

        return $this->success(
            UserResource::make($user->fresh()),
            'User updated successfully'
        );
    }

    public function destroy(User $user)
    {
        $user->employee()->delete();

        $user->delete();

        return $this->success(
            null,
            'User deleted successfully'
        );
    }

    public function totalSalary()
    {
        $totals = Employee::select('department', DB::raw('SUM(salary) as total_salary'))
            ->groupBy('department')
            ->get();

        return $this->success(
            $totals,
            'Total salary per department fetched successfully'
        );
    }
}
