<x-guest-layout>
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800 dark:text-gray-200">
          {{ $user->name }} availabilities
        </h1>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6" x-data="{
            selectedSlot: null,
            guestEmail: '',
            selectSlot(ownerId, startTime, endTime, slotId) {
                this.selectedSlot = {
                    owner_id: ownerId,
                    start_time: startTime,
                    end_time: endTime,
                    id: slotId
                };
                window.scrollTo(0, 0);
            },
            resetSelection() {
                this.selectedSlot = null;
                this.guestEmail = '';
            },
            isValidEmail() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(this.guestEmail);
            }
        }">
            @if(count($slots) > 0)
                <div class="mb-6">
                    <p class="text-gray-600 dark:text-gray-300 text-center">
                        Select a slot to book a meeting
                    </p>
                </div>
                
                <!-- Selected slot form -->
                <div x-show="selectedSlot !== null" class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                    <div class="text-center mb-4">
                        <p class="text-blue-800 dark:text-blue-200 font-medium">
                            Selected slot: 
                            <span x-text="
                                new Date(selectedSlot?.start_time).toLocaleDateString() + ' ' +
                                new Date(selectedSlot?.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + 
                                ' - ' + 
                                new Date(selectedSlot?.end_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                            "></span>
                        </p>
                        <button 
                            @click="resetSelection" 
                            class="text-sm text-red-600 dark:text-red-400 mt-1 hover:underline"
                        >
                            Cancel selection
                        </button>
                    </div>
                    
                    <form method="POST" action="{{ route('booking.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="owner_id" x-bind:value="selectedSlot?.owner_id">
                        <input type="hidden" name="start_time" x-bind:value="selectedSlot?.start_time">
                        <input type="hidden" name="end_time" x-bind:value="selectedSlot?.end_time">
                        
                        <div>
                            <label for="guest_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email address
                            </label>
                            <input 
                                type="email" 
                                name="guest_email" 
                                id="guest_email"
                                x-model="guestEmail"
                                placeholder="Enter your email"
                                class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                        </div>
                        
                        <div class="text-center">
                            <button 
                                type="submit" 
                                x-bind:disabled="!isValidEmail()"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Confirm booking
                            </button>
                        </div>
                    </form>
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
                                    <button 
                                        type="button" 
                                        @click="selectSlot('{{ $user->id }}', '{{ $slot['start_time'] }}', '{{ $slot['end_time'] }}', '{{ $loop->index }}')" 
                                        :class="selectedSlot && selectedSlot.owner_id == '{{ $user->id }}' && selectedSlot.start_time == '{{ $slot['start_time'] }}' && selectedSlot.end_time == '{{ $slot['end_time'] }}' ? 'bg-blue-100 dark:bg-blue-800 border-blue-500 dark:border-blue-400' : 'border-gray-200 dark:border-gray-600 hover:bg-blue-50 dark:hover:bg-blue-900'" 
                                        class="w-full p-3 text-center rounded-md border transition-colors duration-200 text-blue-600 dark:text-blue-300 font-medium">
                                        {{ \Carbon\Carbon::parse($slot['start_time'])->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($slot['end_time'])->format('H:i') }}
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">
                                            {{ $slot['duration'] }} min
                                        </span>
                                    </button>
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