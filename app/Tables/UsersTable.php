<?php

namespace App\Tables;

use App\Models\Group;
use Illuminate\Support\Facades\View;

class UsersTable extends Table
{
    public function prepare()
    {
        $this->tableDataURL = route('admin.users.index.data');
        
        $this->tableFields = [
            'id' => __('ID'),
            'username' => __('Username'),
            'email' => __('Email'),
            'created_at' => __('Created at'),
            'email_verified_at' => __('Status'),
            'group' => __('Group'),
        ];

        $this->sortable = ['id', 'username', 'email', 'created_at'];
        $this->hidden = ['id', 'created_at', 'email_verified_at'];

        $this->setModalDelete([
            'title' => __('Delete User?'),
            'text' => __('Delete user account'),
            'permission' => 'admin-users-delete',
        ]);

        $this->setModalBulkDelete([
            'title' => __('Delete Users?'),
            'text' => __('Delete :count users?'),
            'permission' => 'admin-users-delete',
            'route' => route('admin.users.bulk-destroy'),
        ]);

        $groups = Group::select('id', 'name')
            ->get();
        
        $this->setBulkActionModal([
            'button' => [
                'text' => __('Edit'),
                'target' => 'modalBulkEdit',
            ],
            'modalView' => View::make('admin.users._modal-edit', [
                'groups' => $groups->mapWithKeys(function ($group) {
                    return [$group->id => $group->name];
                }),
            ]),
            'permission' => 'admin-users-group',
        ]);

        $filterGroupChoices = $groups->transform(function ($group) {
                return [
                    'choiceID' => $group->id,
                    'choiceText' => $group->name,
                ];
            })->toArray();

        $this->setFilter([
            'filterKey' => 'group_id',
            'filterLabel' => __('Group'),
            'choices' => $filterGroupChoices,
        ]);

        $this->setFilter([
            'filterKey' => 'status',
            'filterLabel' => __('Status'),
            'choices' => [
                ['choiceID' => 'verified', 'choiceText' => __('Verified')],
                ['choiceID' => 'notverified', 'choiceText' => __('Not verified')],
            ],
        ]);
    }
}