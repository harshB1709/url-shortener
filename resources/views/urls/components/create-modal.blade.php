<div
    class="text-white"
    x-data="{
        originalUrl: '',
        csrfToken: '{{ csrf_token() }}',
        shortenedURL: null,
        submitForm() {
            fetch('{{ route('url.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({
                    original_url: this.originalUrl
                })
            })
            .then((response) => {
                if(response.ok) {
                    return response.json();
                }
                return Promise.reject(response);
            })
            .then(data => {
                if(data.success) {
                    this.shortenedURL = data.newUrl.shortened_url;
                }
            })
            .catch((error) => {
                error.json().then((json) => {
                    alert(json.message);
                })
            });
        },
        closeModal() {
            this.originalUrl = '';
            this.shortenedURL = '';
            $dispatch('close');
        }
    }"
>
	<form method="post" @submit.prevent="submitForm" class="p-6">

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Create New Url') }}
        </h2>

        <div class="mt-6">
            <x-input-label for="original_url" value="{{ __('Original URL') }}" />

            <x-text-input
                id="original_url"
                name="original_url"
                type="url"
                class="mt-1 block w-full"
                placeholder="{{ __('Enter URL') }}"
                x-model="originalUrl"
            />
        </div>

        <template x-if="shortenedURL">
            <div class="mt-6">
                <p>Your shortened URL is:</p>
                <a x-bind:href="shortenedURL" x-text="shortenedURL" class="text-blue-500" target="_blank"></a>
            </div>
        </template>

        <div class="mt-6 flex justify-end">
            <x-secondary-button @click="closeModal">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
            	{{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</div>