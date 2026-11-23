<?php

namespace App\Tables;
use Illuminate\Support\Facades\View;

abstract class Table
{
    protected $tableName = '';
    
    protected $itemNameSingular = 'Item';
    protected $tableDataURL = '';
    protected $tableFields = [];
    protected $sortable = [];
    protected $hidden = [];
    protected $modalDelete = [];
    protected $modalBulkDelete = [];
    protected $filters = [];
    protected $permissions;
    protected $bulkActionModals = [];
    protected $bulkActionForms = [];
    protected $columnsSmall = [];
    protected $isSmall = false;

    public function __construct($tableName = 'mainTable')
    {
        $this->tableName = $tableName;
    }

    public function displayHTML()
    {
        $tableFields = $this->generateTableFields();

        return View::make('components.admin.tables.container', [
            'tableName' => $this->tableName,
            'itemNameSingular' => $this->itemNameSingular,
            'tableDataURL' => $this->tableDataURL,
            'tableFields' => $tableFields,
            'modalDelete' => $this->modalDelete,
            'modalBulkDelete' => $this->modalBulkDelete,
            'filters' => $this->filters,
            'bulkActionModals' => $this->bulkActionModals,
            'bulkActionForms' => $this->bulkActionForms,
            'isSmall' => $this->isSmall,
        ]);
    }

    public function displayJS()
    {
        return View::make('components.admin.tables.js', [
            'tableName' => $this->tableName,
            'tableDataURL' => $this->tableDataURL
        ]);
    }

    protected function generateTableFields()
    {
        $tableFields = $this->tableFields;

        $output = '';
        foreach ($tableFields as $fieldKey => $fieldLabel) {
            $output .= View::make('components.admin.tables.fields', [
                'fieldKey' => $fieldKey,
                'fieldLabel' => $fieldLabel,
                'sortable' => $this->sortable,
                'hidden' => $this->hidden,
                'columnsSmall' => $this->columnsSmall,
            ]);
        }
        return $output;
    }

    protected function setModalDelete($modalDelete = [])
    {
        if (empty($modalDelete['title'])) {
            $modalDelete['title'] = __('Delete Item?');
        }

        if (empty($modalDelete['text'])) {
            $modalDelete['text'] = __('Delete item');
        }

        if (!empty($modalDelete['permission']) && $this->permissions->authorize($modalDelete['permission'])) {
            $this->modalDelete = $modalDelete;
        }
    }

    protected function setModalBulkDelete($modalBulkDelete = [])
    {
        if (empty($modalBulkDelete['title'])) {
            $modalBulkDelete['title'] = __('Delete Items?');
        }

        if (empty($modalBulkDelete['text'])) {
            $modalBulkDelete['text'] = __('Delete :count items?', ['count' => '<span data-checked-count=""></span>']);
        }
        else {
            $modalBulkDelete['text'] = __($modalBulkDelete['text'], ['count' => '<span data-checked-count=""></span>']);
        }

        if (!empty($modalBulkDelete['route']) && !empty($modalBulkDelete['permission']) && $this->permissions->authorize($modalBulkDelete['permission'])) {
            $this->modalBulkDelete = $modalBulkDelete;
        }
    }

    protected function setFilter($filterSettings = [])
    {
        $createFilter = [
            'filterKey' => $filterSettings['filterKey'],
            'filterLabel' => $filterSettings['filterLabel'],
            'noFilterID' => !empty($filterSettings['noFilterID']) ? $filterSettings['noFilterID'] : '0',
            'noFilterText' => !empty($filterSettings['noFilterText']) ? $filterSettings['noFilterText'] : __('All'),
            'choices' => !empty($filterSettings['choices']) ? $filterSettings['choices'] : [], // ['choiceID' => '', 'choiceText' => '']
        ];

        $this->filters[] = $createFilter;
    }

    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    public function setBulkActionModal($bulkAction = [])
    {
        if (empty($bulkAction['button'])) {
            if (empty($bulkAction['button']['text'])) {
                $bulkAction['button']['text'] = 'Button';
            }

            if (empty($bulkAction['button']['target'])) {
                $bulkAction['button']['target'] = 'modalID';
            }
        }

        if (empty($bulkAction['modalView'])) {
            $bulkAction['modalView'] = '';
        }

        if (!empty($bulkAction['permission']) && $this->permissions->authorize($bulkAction['permission'])) {
            $this->bulkActionModals[] = $bulkAction;
        }
    }

    public function setBulkActionForm($bulkAction = [])
    {
        if (empty($bulkAction['method'])) {
            $bulkAction['method'] = 'POST';
        }

        if (empty($bulkAction['buttonText'])) {
            $bulkAction['buttonText'] = 'Button';
        }

        if (!empty($bulkAction['route']) && !empty($bulkAction['permission']) && $this->permissions->authorize($bulkAction['permission'])) {
            $this->bulkActionForms[] = $bulkAction;
        }
    }
}