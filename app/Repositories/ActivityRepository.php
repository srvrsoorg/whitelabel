<?php

namespace App\Repositories;

use App\Interfaces\ActivityRepositoryInterface;

class ActivityRepository implements ActivityRepositoryInterface 
{
    /**
     * Retrieve activities for a specific user with optional filters.
     *
     * @param  \App\Models\User  $user  The user for whom to retrieve activities.
     * @return array
     */
    public function getUserActivities($user) 
    {
        // Activities of user side index
        $activities = $user->activities()
            ->select('*')
            ->when(request()->query('type'),fn ($query) => $query->where('on', request()->query('type')))
            ->when(request()->query('action'),fn ($query) => $query->where('action', request()->query('action')))
            ->orderBy('created_at','DESC');

        // Getting list of available types
        $types = $user->activities()->distinct('on')->pluck('on');

        // Getting list of available actions
        $actions = $user->activities()->distinct('action')->pluck('action');

        $activities = $activities->paginate(request()->input('per_page'))->appends(request()->query());

        // Success response
        return [
            'activities' => $activities,
            'types' => $types,
            'actions' => $actions
        ];
    }
}