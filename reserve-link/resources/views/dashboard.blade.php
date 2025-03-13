<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Upcoming appointments -->
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    Upcoming appointments
                </h2>
                
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    @if(count($upcomingBookings) > 0)
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($upcomingBookings as $booking)
                                <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div>
                                            <p class="font-medium">
                                                {{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y') }}
                                                <span class="ml-2 font-normal text-gray-600 dark:text-gray-300">
                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                </span>
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                @if($booking->is_owner)
                                                    With: {{ $booking->user->name }}
                                                @else
                                                    With: {{ $booking->owner->name }}
                                                @endif
                                            </p>
                                        </div>
                                        <form method="POST" action="{{ route('booking.cancel', $booking->id) }}" 
                                              onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            You don't have any upcoming appointments.
                        </div>
                    @endif
                </div>
            </section>

            <!-- Availabilities -->
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        My availabilities
                    </h2>
                    <a href="{{ route('availability.add') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add
                    </a>
                </div>
                
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    @if(count($availabilities) > 0)
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($availabilities as $availability)
                                <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div>
                                            <p class="font-medium">
                                                {{ $dayNames[$availability->day_of_week] }}
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-300">
                                                {{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Time slot duration: {{ $availability->slot_duration }} minutes
                                            </p>
                                        </div>
                                        <div class="flex space-x-3">
                                            <a href="{{ route('availability.edit', $availability->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                                Edit
                                            </a>
                                            <form action="{{ route('availability.destroy', $availability->id) }}" 
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this availability?');">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                  Delete
                                              </button>
                                          </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            You don't have any availability.
                            <p class="mt-2">
                                Add time slots to allows people to book an appointment with you.
                            </p>
                        </div>
                    @endif
                </div>
                
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <p class="text-sm text-blue-800 dark:text-blue-300">
                            <span class="font-medium">Your appointment link:</span>
                            <br class="sm:hidden">
                            <span class="inline-block mt-1 sm:mt-0 sm:ml-1">
                                <a href="{{ route('calendar', ['email' => Auth::user()->email]) }}" class="underline" target="_blank">
                                    {{ route('calendar', ['email' => Auth::user()->email]) }}
                                </a>
                            </span>
                            <button onclick="navigator.clipboard.writeText('{{ route('calendar', ['email' => Auth::user()->email]) }}')" 
                                    class="ml-2 text-xs px-2 py-1 bg-blue-200 dark:bg-blue-800 rounded hover:bg-blue-300 dark:hover:bg-blue-700">
                                Copy
                            </button>
                        </p>
                    </div>
            </section>
        </div>
    </div>
</x-app-layout>
