<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <input type="image" src="{{ $getRecord()->high_image_link }}" disabled="disabled" width="100%" height="100%"
            style="
                border-radius: 5%;
            "/>
    </div>
</x-dynamic-component>
