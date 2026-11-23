<?php

namespace App\Tables;

class PostsTable extends Table
{
    public function prepare()
    {
        $this->tableDataURL = route('admin.posts.index.data');

        $this->tableFields = [
            'id' => __('ID'),
            'title' => __('Post title'),
            'status' => __('Status'),
            'slug' => __('Slug'),
        ];

        $this->sortable = ['id', 'title'];
        $this->hidden = ['id', 'slug'];
        $this->columnsSmall = ['status'];

        $this->setModalDelete([
            'title' => __('Delete Post?'),
            'text' => __('Delete post'),
            'permission' => 'admin-posts-delete',
        ]);

        $this->setModalBulkDelete([
            'title' => __('Delete Posts?'),
            'text' => __('Delete :count posts?'),
            'permission' => 'admin-posts-delete',
            'route' => route('admin.posts.bulk-destroy'),
        ]);
    }
}