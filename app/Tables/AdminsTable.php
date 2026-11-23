<?php

namespace App\Tables;

class AdminsTable extends Table
{
    public function prepare()
    {
        $this->tableDataURL = route('admin.admins.index.data');

        $this->tableFields = [
            'id' => __('ID'),
            'name' => __('Group name'),
            'created_at' => __('Created at'),
        ];

        $this->sortable = ['id', 'name', 'created_at'];
        $this->hidden = ['id', 'created_at'];

        $this->setModalDelete([
            'title' => __('Remove Group?'),
            'text' => __('Remove group from administrator'),
            'permission' => 'admin-admins-delete',
        ]);
    }
}