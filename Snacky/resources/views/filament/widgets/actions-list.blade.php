<x-filament-widgets::widget>
    <script>
        async function receiveCategories() {
            document.querySelector('.hidable').style.display = '';
            let response = await fetch('{{ url('receiveCategories') }}', {
                headers: {
                    accept:'application/json',
                    credentials: 'same-origin',
                    'Content-Type': 'application/json;charset=utf-8'
                }
            });
            if (response.ok) {
                new FilamentNotification()
                    .title('Received successfully!')
                    .success()
                    .send()
            } else {
                new FilamentNotification()
                    .title('Error! Something went wrong!')
                    .danger()
                    .send()
            }
            document.querySelector('.hidable').style.display = 'none';
        }
    </script>
    <x-filament::section>
        <x-filament::button
            outlined
            color="info"
            icon="heroicon-o-arrow-down-on-square"
            tag="button"
            style="width: 50%"
            onclick="receiveCategories()"
        >
        <div style="display:flex; align-items: center; justify-content: center;">
            {{ 'Load categories' }}
        <x-filament::loading-indicator class="h-5 w-5 hidable" style="margin-left: 1em; display: none"/>
        </div>
        </x-filament::button>
    </x-filament::section>
    <x-filament::section>
        <form
            action="{{ route('redirectToTelescope') }}"
            method="get"
        >

            <x-filament::button
                color="info"
                icon="heroicon-o-beaker"
                tag="button"
                type="submit"
                style="width: 50%"
            >
                {{ 'Telescope' }}
            </x-filament::button>
        </form>
    </x-filament::section>
    <x-filament::section>
        <form
            action="{{ route('redirectToSentry') }}"
            method="get"
        >

            <x-filament::button
                color="info"
                icon="heroicon-o-bolt"
                tag="button"
                type="submit"
                style="width: 50%"
            >
                {{ 'Sentry' }}
            </x-filament::button>
        </form>
    </x-filament::section>
    <x-filament::section>
        <form
            action="{{ route('redirectToLogViewer') }}"
            method="get"
        >

            <x-filament::button
                color="info"
                icon="heroicon-o-server-stack"
                tag="button"
                type="submit"
                style="width: 50%"
            >
                {{ 'Log viewer' }}
            </x-filament::button>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
