<th 
    data-field="{{ $fieldKey }}" 
    {!! in_array($fieldKey, $sortable) ? 'data-sortable="true"' : 'data-sortable="false"' !!} 
    {!! in_array($fieldKey, $hidden) ? 'data-visible="false"' : 'data-visible="true"' !!} 
    {!! $fieldKey == 'id' || in_array($fieldKey, $columnsSmall) ? 'data-width="1"' : '' !!}
>{{ $fieldLabel }}</th>