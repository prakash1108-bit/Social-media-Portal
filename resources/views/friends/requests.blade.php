<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Friend Requests') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($requests->count())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg divide-y">
                    @foreach ($requests as $request)
                        <div class="p-4 flex justify-between items-center">
                            <div class="flex gap-4">
                                <img src="{{ asset('storage/' . $request->sender->image) }}"
                                    class="w-16 h-16 rounded-full object-cover border border-gray-300 shadow-sm">
                                <div class="mt-2">
                                    <p class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $request->sender->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request->sender->email }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('friend.request.accept', $request->id) }}">
                                    @csrf
                                    <x-primary-button>{{ __('Accept') }}</x-primary-button>
                                </form>
                                <form method="POST" action="{{ route('friend.request.reject', $request->id) }}">
                                    @csrf
                                    <x-danger-button>{{ __('Reject') }}</x-danger-button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-300">You have no friend requests.</p>
            @endif

            @if (session('success'))
                <div class="mt-4 p-4 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100 rounded">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
