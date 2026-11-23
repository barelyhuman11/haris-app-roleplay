<?php

namespace App\Tables;

class ContactsTable extends Table
{
    public function prepare()
    {
        $this->tableDataURL = route('admin.contacts.index.data');

        $this->tableFields = [
            'id' => __('ID'),
            'name' => __('Name'),
            'email' => __('Email'),
            'subject' => __('Subject'),
            'status' => __('Status'),
            'created_at' => __('Created at'),
        ];

        $this->sortable = ['id', 'name', 'email', 'subject', 'created_at'];
        $this->hidden = ['id', 'name', 'email'];

        $this->setModalDelete([
            'title' => __('Delete Message?'),
            'text' => __('Delete message'),
            'permission' => 'admin-contacts-index',
        ]);

        $this->setModalBulkDelete([
            'title' => __('Delete Messages?'),
            'text' => __('Delete :count messages?'),
            'permission' => 'admin-contacts-index',
            'route' => route('admin.contacts.bulk-destroy'),
        ]);

        $this->setFilter([
            'filterKey' => 'status',
            'filterLabel' => __('Status'),
            'choices' => [
                ['choiceID' => 'UNREAD', 'choiceText' => __('Unread')],
                ['choiceID' => 'READ', 'choiceText' => __('Read')],
            ],
        ]);

        $this->setBulkActionForm([
            'method' => 'PUT',
            'buttonText' => __('Mark as read'),
            'route' => route('admin.contacts.bulk-read'),
            'permission' => 'admin-contacts-index',
        ]);
    }
}