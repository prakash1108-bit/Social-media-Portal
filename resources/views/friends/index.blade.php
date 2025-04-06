<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Your Friends') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($friends->count())
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg divide-y">
                    @foreach ($friends as $friend)
                        @if ($friend->receiver->id != auth()->user()->id)
                            <div class="p-4 flex justify-between items-center">
                                <div class="flex gap-4">
                                    <img src="{{ asset('storage/' . $friend->receiver->image) }}"
                                        class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-sm">
                                    <div class="mt-2">
                                        <p class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $friend->sender->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $friend->sender->email }}
                                        </p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('friends.remove', $friend->receiver->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button>{{ __('Remove Friend') }}</x-danger-button>
                                </form>
                            </div>
                        @else
                            <div class="p-4 flex justify-between items-center">
                                <div class="flex gap-4">
                                    <img src="{{ asset('storage/' . $friend->sender->image) }}"
                                        class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-sm">
                                    <div class="mt-2">
                                        <p class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $friend->sender->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $friend->sender->email }}
                                        </p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('friends.remove', $friend->sender->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button>{{ __('Remove Friend') }}</x-danger-button>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-300">
                    {{ __("You don't have any friends yet.") }}
                </p>
            @endif

            @if (session('success'))
                <div class="mt-4 p-4 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100 rounded">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
