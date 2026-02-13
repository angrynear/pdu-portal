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
                    'admin' => 'Task details updated successfully.',
                    'user'  => 'Your task details were updated.',
                ],
                'warning' => [
                    'admin' => 'No changes were made.',
                    'user' => 'No changes were made.',
                ],
            ],

            'task_progress_updated' => [
                'success' => [
                    'admin' => 'Task progress updated successfully.',
                    'user'  => 'Your progress update was saved.',
                ],
                'warning' => [
                    'admin' => 'No changes were made.',
                    'user' => 'No changes were made.',
                ],
                'error' => [
                    'admin' => 'Failed to update task progress.',
                    'user'  => 'Failed to update your task progress.',
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

            'task_set_dates' => [
                'success' => [
                    'admin' => 'Task dates successfully set.',
                    'user' => 'You have successfully set the task dates.',
                ],
                'warning' => [
                    'admin' => 'No changes were made.',
                    'user' => 'No changes were made.',
                ],
            ],

            'task_re-assign' => [
                'success' => [
                    'admin' => 'Task re-assigned successfully.',
                ],
                'warning' => [
                    'admin' => 'No changes were made.',
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
                'warning' => [
                    'admin' => 'No changes were made.',
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
                'warning' => [
                    'admin' => 'No changes were made.',
                    'user' => 'No changes were made.',
                ],
            ],

            'personnel_deactivated' => [
                'success' => [
                    'admin' => 'Personnel has been deactivated.',
                ],
                'warning' => [
                    'admin' => 'Personnel is already inactive.',
                ],
                'error' => [
                    'admin' => 'You cannot deactivate your own account.',
                ],
            ],

            'personnel_reactivated' => [
                'success' => [
                    'admin' => 'Personnel has been reactivated.',
                ],
                'warning' => [
                    'admin' => 'Personnel is already active.',
                ],
            ],

            // ===== PROFILE =====
            'profile_updated' => [
                'success' => [
                    'admin' => 'Your profile has been updated successfully.',
                    'user' => 'Your profile has been updated successfully.',
                ],
                'warning' => [
                    'admin' => 'No changes were made.',
                    'user' => 'No changes were made.',
                ],
            ],
        ];

        return $messages[$key][$type][self::role()]
            ?? $messages[$key][$type]['admin']
            ?? 'Action completed.';
    }
}
