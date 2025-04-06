<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Find Friends') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <form action="{{ route('friends.search') }}" method="GET" class="mb-6">
                <div class="flex items-center gap-2">
                    <x-text-input type="text" name="query" placeholder="Search by name or email"
                        value="{{ request('query') }}" class="w-full" />
                    <x-primary-button>
                        {{ __('Search') }}
                    </x-primary-button>
                </div>
            </form>

            @if (session('success'))
                <div class="mt-4 p-4 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mt-4 p-4 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Search Results -->
            @if (isset($users) && $users->count())
                @if (request('query'))
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg divide-y">
                        @foreach ($users as $user)
                            <div class="p-4 flex justify-between items-center">
                                <div class="flex gap-4">
                                    <img src="{{ asset('storage/' . $user->image) }}"
                                        class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-sm">
                                    <div class="mt-2">
                                        <p class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $user->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                                <form action="{{ route('friend.request.send', $user->id) }}" method="POST">
                                    @csrf
                                    <x-primary-button>
                                        {{ __('Send Friend Request') }}
                                    </x-primary-button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            @elseif(request('query'))
                <p class="text-gray-600 dark:text-gray-300">No users found for "{{ request('query') }}"</p>
            @endif
        </div>
    </div>
</x-app-layout>
