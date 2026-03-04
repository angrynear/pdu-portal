<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Slide;
use App\Models\User;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $scope = $request->get('scope');

        if (!$scope) {
            $scope = $user->isAdmin() ? 'projects' : 'tasks';
        }

        // ===============================
        // COUNTS
        // ===============================

        $counts = [
            'projects'  => Project::whereNotNull('archived_at')->count(),

            'tasks' => Task::whereNotNull('archived_at')
                ->when(!$user->isAdmin(), function ($q) use ($user) {
                    $q->whereNull('project_id')
                        ->where(function ($sub) use ($user) {
                            $sub->where('assigned_user_id', $user->id)
                                ->orWhere('created_by', $user->id);
                        });
                })
                ->count(),

            'slides'    => Slide::onlyTrashed()->count(),

            'personnel' => User::where('account_status', 'inactive')->count(),
        ];

        // ===============================
        // DATA
        // ===============================

        switch ($scope) {

            case 'tasks':

                $query = Task::whereNotNull('archived_at')
                    ->with(['project', 'assignedUser']);

                if (!$user->isAdmin()) {

                    $query->whereNull('project_id')
                        ->where(function ($q) use ($user) {

                            $q->where('assigned_user_id', $user->id)
                                ->orWhere('created_by', $user->id);
                        });
                }

                $data = $query
                    ->latest('archived_at')
                    ->paginate(10)
                    ->withQueryString();

                break;


            case 'slides':

                if (!$user->isAdmin()) {
                    abort(403);
                }

                $data = Slide::onlyTrashed()
                    ->latest('deleted_at')
                    ->paginate(10)
                    ->withQueryString();

                break;


            case 'personnel':

                if (!$user->isAdmin()) {
                    abort(403);
                }

                $data = User::where('account_status', 'inactive')
                    ->latest('deactivated_at')
                    ->paginate(10)
                    ->withQueryString();

                break;


            default:

                if (!$user->isAdmin()) {
                    abort(403);
                }

                $scope = 'projects';

                $data = Project::whereNotNull('archived_at')
                    ->latest('archived_at')
                    ->paginate(10)
                    ->withQueryString();

                break;
        }

        return view('archives.index', compact('data', 'scope', 'counts'));
    }
}
