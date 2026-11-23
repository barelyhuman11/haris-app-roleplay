<?php

namespace App\Tables;

class GroupsTable extends Table
{
    public function prepare()
    {
        $this->tableDataURL = route('admin.groups.index.data');

        $this->tableFields = [
            'id' => __('ID'),
            'name' => __('Group name'),
            'created_at' => __('Created at'),
        ];

        $this->sortable = ['id', 'name', 'created_at'];
        $this->hidden = ['id', 'created_at'];

        $this->setModalDelete([
            'title' => __('Delete Group?'),
            'text' => __('Delete group'),
            'permission' => 'admin-groups-delete',
        ]);
    }
}