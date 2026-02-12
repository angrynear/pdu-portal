<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class FlashMessage
{
    protected static function role(): string
    {
        return Auth::user()?->role ?? 'guest';
    }

    public static function success(string $key): string
    {
        return self::message($key, 'success');
    }

    public static function error(string $key): string
    {
        return self::message($key, 'error');
    }

    public static function warning(string $key): string
    {
        return self::message($key, 'warning');
    }

    protected static function message(string $key, string $type): string
    {
        $messages = [

            // ===== TASKS =====
            'task_created' => [
                'success' => [
                    'admin' => 'Task added successfully.',
                    'user'  => 'A new task was assigned to you.',
                ],
            ],

            'task_updated' => [
                'success' => [
                    'admin' => 'Task updated successfully.',
                    'user'  => 'Your task details were updated.',
                ],
            ],

            'task_progress_updated' => [
                'success' => [
                    'admin' => 'Task progress updated successfully.',
                    'user'  => 'Your progress update was saved.',
                ],
            ],

            'task_archived' => [
                'success' => [
                    'admin' => 'Task archived successfully.',
                ],
            ],

            'task_restored' => [
                'success' => [
                    'admin' => 'Task restored successfully.',
                ],
            ],

            'task_update_no_changes' => [
                'warning' => [
                    'admin' => 'No changes were made.',
                    'user' => 'No changes were made.',
                ],
            ],

            // ===== PROJECTS =====
            'project_created' => [
                'success' => [
                    'admin' => 'Project has been successfully created.',
                ],
            ],

            'project_updated' => [
                'success' => [
                    'admin' => 'Project has been successfully updated.',
                ],
            ],

            'project_archived' => [
                'success' => [
                    'admin' => 'Project and its tasks have been archived.',
                ],
            ],

            'project_restored' => [
                'success' => [
                    'admin' => 'Project has been restored.',
                ],
            ],

            // ===== PERSONNEL =====
            'personnel_created' => [
                'success' => [
                    'admin' => 'Personnel has been successfully created.',
                ],
            ],

            'personnel_updated' => [
                'success' => [
                    'admin' => 'Personnel has been successfully updated.',
                    'user' => 'Your details has been successfully updated.',
                ],
            ],

            'personnel_deactivated' => [
                'success' => [
                    'admin' => 'Personnel has been deactivated.',
                ],
                'error' => [
                    'admin' => 'You cannot deactivate your own account.',
                ],
            ],

            'personnel_reactivated' => [
                'success' => [
                    'admin' => 'Personnel has been reactivated.',
                ],
            ],

            // ===== PROFILE =====
            'profile_updated' => [
                'success' => [
                    'admin' => 'Your profile has been updated successfully.',
                    'user' => 'Your profile has been updated successfully.',
                ],
            ],
        ];

        return $messages[$key][$type][self::role()]
            ?? $messages[$key][$type]['admin']
            ?? 'Action completed.';
    }
}
