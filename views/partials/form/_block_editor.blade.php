@php
    $label = $label ?? twillTrans('twill::lang.fields.block-editor.add-content');
    $name = $name ?? 'default';
    $allowedBlocks = generate_list_of_available_blocks($blocks ?? null, $group ?? $groups ?? null);
    $existingBlocks = array_values(array_filter($form_fields['blocks'] ?? [], function ($block) use ($name) {
        return $block['name'] === $name;
    }));
@endphp

@unless($withoutSeparator ?? false)
<hr/>
@endunless
<a17-blocks title="{{ $label }}" section="{{ $name }}"></a17-blocks>

@push('vuexStore')
    window['{{ config('twill.js_namespace') }}'].STORE.form.availableBlocks['{{ $name }}'] = {!! json_encode(array_values($allowedBlocks)) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.form.blocks['{{ $name }}'] = {!! json_encode($existingBlocks) !!}

    @foreach($form_fields['blocksFields'] ?? [] as $field)
        window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({!! json_encode($field) !!})
    @endforeach

    @foreach($form_fields['blocksMedias'] ?? [] as $name => $medias)
        window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}"] = {!! json_encode($medias) !!}
    @endforeach

    @foreach($form_fields['blocksFiles'] ?? [] as $name => $files)
        window['{{ config('twill.js_namespace') }}'].STORE.medias.selected["{{ $name }}"] = {!! json_encode($files) !!}
    @endforeach

    @foreach($form_fields['blocksBrowsers'] ?? [] as $name => $browser)
        window['{{ config('twill.js_namespace') }}'].STORE.browser.selected["{{ $name }}"] = {!! json_encode($browser) !!}
    @endforeach
@endpush
