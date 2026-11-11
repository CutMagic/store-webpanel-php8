{{-- [multimod] textes des tableaux DataTables dans la langue du site (objet JavaScript) --}}
@php
    $dtLang = app()->getLocale() === 'fr'
        ? ['search' => 'Rechercher :', 'lengthMenu' => '_MENU_ par page', 'info' => '_START_ à _END_ sur _TOTAL_',
           'paginate' => ['previous' => 'Préc.', 'next' => 'Suiv.'], 'zeroRecords' => 'Aucun résultat',
           'emptyTable' => 'Aucun objet', 'infoEmpty' => '', 'infoFiltered' => '(filtré sur _MAX_)']
        : ['zeroRecords' => 'No matching items', 'emptyTable' => 'No items', 'infoEmpty' => ''];
@endphp
{!! json_encode($dtLang, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
