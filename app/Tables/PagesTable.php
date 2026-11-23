<?php

namespace App\Tables;

class PagesTable extends Table
{
    public function prepare()
    {
        $this->tableDataURL = route('admin.pages.index.data');

        $this->tableFields = [
            'id' => __('ID'),
            'title' => __('Page title'),
            'status' => __('Status'),
            'slug' => __('Slug'),
        ];

        $this->sortable = ['id', 'title'];
        $this->hidden = ['id', 'slug'];
        $this->columnsSmall = ['status'];

        $this->setModalDelete([
            'title' => __('Delete Page?'),
            'text' => __('Delete page'),
            'permission' => 'admin-pages-delete',
        ]);

        $this->setModalBulkDelete([
            'title' => __('Delete Pages?'),
            'text' => __('Delete :count pages?'),
            'permission' => 'admin-pages-delete',
            'route' => route('admin.pages.bulk-destroy'),
        ]);
    }
}