<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Shortened URLs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 dark:text-white">
            <div class="flex justify-end items-center pb-6 ">
                <x-primary-button
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'create-modal')"
                ><i class="fa-solid fa-plus mr-2"></i>{{ __('Add New URL') }}</x-primary-button>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <table class="text-left w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Original URL</th>
                            <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Short URL</th>
                            <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Is Active?</th>
                            <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($urls as $url)
                        <tr class="hover:bg-grey-lighter">
                            <td
                            @class([
                                "py-4 px-6 border-b border-grey-light",
                                "text-gray-500" => ! $url->is_active
                            ])>
                                {{ $url->original_url }}
                            </td>
                            <td class="py-4 px-6 border-b border-grey-light"><a href="{{ route('url.access', $url->short_code) }}" target="_blank" class="text-blue-500">{{ route('url.access', $url->short_code) }}</a></td>
                            <td class="py-4 px-6 border-b border-grey-light">
                                @if($url->is_active)
                                    <i class="fa-regular fa-lg fa-circle-check text-green-500"></i>
                                @else
                                    <i class="fa-regular fa-lg fa-circle-xmark text-red-500"></i>
                                @endif
                            </td>
                            <td class="py-4 px-6 border-b border-grey-light flex gap-3">
                                <button
                                    class="text-blue-500 hover:text-blue-800"
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'update-modal') || $dispatch('open-update-url-modal',@js($url))"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="{{ route('url.access', $url->short_code) }}" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                @if($url->is_active)
                                <form action="{{ route('url.deactivate', [$url->id]) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-gray-400 hover:text-gray-600" onclick="return confirm('Are you sure about deactivating?')"><i class="fa-solid fa-toggle-off"></i></button>
                                </form>
                                @endif
                                <form action="{{ route('url.delete', [$url->id]) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-800" onclick="return confirm('Are you sure about deleting?')"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="py-4">
                {{ $urls->links() }}
            </div>
        </div>
    </div>

    <x-modal name="create-modal" :show="false" focusable>
        @include('urls.components.create-modal')
    </x-modal>

    <x-modal name="update-modal" :show="false" x-ref="updateModal" focusable>
        @include('urls.components.update-modal')
    </x-modal>
</x-app-layout>