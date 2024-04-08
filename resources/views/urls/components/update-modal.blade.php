<div
    class="text-white"
    x-data="{
        originalUrl: '',
        isActive: null,
        url_id: null,
        shortenedUrl: null,
        csrfToken: '{{ csrf_token() }}',
        initForm(values) {
            this.originalUrl = values.original_url;
            this.isActive = values.is_active ? true : false;
            this.url_id = values.id;
            this.shortenedUrl = values.shortened_url;
        },
        submitForm() {
            fetch('/urls/' + this.url_id + '/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({
                    original_url: this.originalUrl,
                    is_active: this.isActive
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
                    location.reload();
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
            $dispatch('close');
        }
    }"
    x-on:open-update-url-modal.window="initForm($event.detail)"
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

        <div class="mt-6">
            <div class="flex flex-row gap-3">
                <x-input-label for="is_active" value="{{ __('Is URL Active?') }}" />

                <input type="checkbox" id="is_active" name="is_active" value="is_active" x-model="isActive">
            </div>
        </div>

        <div class="mt-6">
            <x-input-label for="shortened_url" value="{{ __('Shortened URL') }}" />

            <x-text-input
                id="shortened_url"
                name="shortened_url"
                type="url"
                class="mt-1 block w-full"
                placeholder="{{ __('Enter URL') }}"
                x-model="shortenedUrl"
                readonly='true'
            />
        </div>

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