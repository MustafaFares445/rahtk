<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-phone class="h-5 w-5" />
                Contact Information
            </div>
        </x-slot>

        <x-slot name="headerEnd">
            {{ $this->editContactAction }}
        </x-slot>

        @if($contact)
            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 flex items-center justify-center bg-green-100 dark:bg-green-900/20 rounded-full">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="h-5 w-5 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            WhatsApp
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate font-mono">
                            {{ $contact->whatsapp ?: 'Not set' }}
                        </p>
                    </div>
                    @if($contact->whatsapp)
                        <div class="flex-shrink-0">
                            <button
                                onclick="navigator.clipboard.writeText('{{ $contact->whatsapp }}');
                                         this.innerHTML = '<x-heroicon-o-check class=\'h-4 w-4\' />';
                                         setTimeout(() => this.innerHTML = '<x-heroicon-o-clipboard class=\'h-4 w-4\' />', 2000)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded transition-colors"
                                title="Copy to clipboard"
                            >
                                <x-heroicon-o-clipboard class="h-4 w-4" />
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/20 rounded-full">
                            <x-heroicon-o-phone class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Phone
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate font-mono">
                            {{ $contact->phone ?: 'Not set' }}
                        </p>
                    </div>
                    @if($contact->phone)
                        <div class="flex-shrink-0">
                            <button
                                onclick="navigator.clipboard.writeText('{{ $contact->phone }}');
                                         this.innerHTML = `{!! '<x-heroicon-o-check class=\'h-4 w-4\' />' !!}`;
                                         setTimeout(() => this.innerHTML = `{!! '<x-heroicon-o-clipboard class=\'h-4 w-4\' />' !!}`, 2000)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded transition-colors"
                                title="Copy to clipboard"
                            >
                                <x-heroicon-o-clipboard class="h-4 w-4" />
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 flex items-center justify-center bg-pink-100 dark:bg-pink-900/20 rounded-full">
                            <x-heroicon-o-camera class="h-5 w-5 text-pink-600 dark:text-pink-400" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Instagram
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate font-mono">
                            {{ $contact->instagram ?: 'Not set' }}
                        </p>
                    </div>
                    @if($contact->instagram)
                        <div class="flex-shrink-0">
                            <button
                                onclick="navigator.clipboard.writeText('{{ $contact->instagram }}');
                                         this.innerHTML = '<x-heroicon-o-check class=\'h-4 w-4\' />';
                                         setTimeout(() => this.innerHTML = '<x-heroicon-o-clipboard class=\'h-4 w-4\' />', 2000)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded transition-colors"
                                title="Copy to clipboard"
                            >
                                <x-heroicon-o-clipboard class="h-4 w-4" />
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/20 rounded-full">
                            <heroicon-m-globe-alt class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Facebook
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate font-mono">
                            {{ $contact->facebook ?: 'Not set' }}
                        </p>
                    </div>
                    @if($contact->facebook)
                        <div class="flex-shrink-0">
                            <button
                                onclick="navigator.clipboard.writeText('{{ $contact->facebook }}');
                                         this.innerHTML = '<x-heroicon-o-check class=\'h-4 w-4\' />';
                                         setTimeout(() => this.innerHTML = '<x-heroicon-o-clipboard class=\'h-4 w-4\' />', 2000)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded transition-colors"
                                title="Copy to clipboard"
                            >
                                <x-heroicon-o-clipboard class="h-4 w-4" />
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <div class="mx-auto h-16 w-16 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
                    <x-heroicon-o-phone class="h-8 w-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    No contact information
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Get started by adding your contact details.
                </p>
                <div>
                    {{ $this->editContactAction }}
                </div>
            </div>
        @endif
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>