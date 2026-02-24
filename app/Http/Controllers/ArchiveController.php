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
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $scope = $request->get('scope', 'projects');

        // ===============================
        // COUNTS (for toggle buttons)
        // ===============================

        $counts = [
            'projects'  => \App\Models\Project::whereNotNull('archived_at')->count(),
            'tasks'     => \App\Models\Task::whereNotNull('archived_at')->count(),
            'slides'    => \App\Models\Slide::onlyTrashed()->count(),
            'personnel' => \App\Models\User::where('account_status', 'inactive')->count(),
        ];

        // ===============================
        // DATA PER SCOPE
        // ===============================

        switch ($scope) {

            case 'tasks':
                $data = \App\Models\Task::whereNotNull('archived_at')
                    ->with(['project', 'assignedUser'])
                    ->latest('archived_at')
                    ->paginate(10)
                    ->withQueryString();
                break;

            case 'slides':
                $data = \App\Models\Slide::onlyTrashed()
                    ->latest('deleted_at')
                    ->paginate(10)
                    ->withQueryString();
                break;

            case 'personnel':
                $data = \App\Models\User::where('account_status', 'inactive')
                    ->latest('deactivated_at')
                    ->paginate(10)
                    ->withQueryString();
                break;

            default:
                $scope = 'projects';

                $data = \App\Models\Project::whereNotNull('archived_at')
                    ->latest('archived_at')
                    ->paginate(10)
                    ->withQueryString();
                break;
        }

        return view('archives.index', compact('data', 'scope', 'counts'));
    }
}
