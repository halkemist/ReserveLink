<x-guest-layout>
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800 dark:text-gray-200">
          {{ $user->name }} availabilities
        </h1>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            @if(count($slots) > 0)
                <div class="mb-6">
                    <p class="text-gray-600 dark:text-gray-300 text-center">
                        Select a slot to book a meeting
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        $currentDate = null;
                        $datesWithSlots = collect($slots)
                            ->groupBy('date')
                            ->map(function($items) {
                                return [
                                    'date' => $items->first()['date'],
                                    'day_name' => $items->first()['day_name'],
                                    'slots' => $items->toArray()
                                ];
                            })
                            ->values()
                            ->toArray();
                    @endphp
                    
                    @foreach($datesWithSlots as $dateGroup)
                        <div class="date-card border dark:border-gray-700 rounded-lg overflow-hidden">
                            <div class="bg-gray-100 dark:bg-gray-700 p-3">
                                <h3 class="font-medium text-center">
                                    {{ \Carbon\Carbon::parse($dateGroup['date'])->format('d/m/Y') }}
                                    <span class="block text-sm text-gray-600 dark:text-gray-300">
                                        {{ $dateGroup['day_name'] }}
                                    </span>
                                </h3>
                            </div>
                            
                            <div class="p-2 flex flex-col space-y-2 max-h-64 overflow-y-auto">
                                @foreach($dateGroup['slots'] as $slot)
                                    <a href="{{ route('booking.create', ['user' => $user->id, 'start' => $slot['start_time'], 'end' => $slot['end_time']]) }}" 
                                       class="p-3 text-center rounded-md border border-gray-200 dark:border-gray-600 hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors duration-200 text-blue-600 dark:text-blue-300 font-medium">
                                        {{ \Carbon\Carbon::parse($slot['start_time'])->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($slot['end_time'])->format('H:i') }}
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">
                                            {{ $slot['duration'] }} min
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg text-center">
                    <p class="text-blue-800 dark:text-blue-200">
                        No availabilities found for the next 30 days.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>