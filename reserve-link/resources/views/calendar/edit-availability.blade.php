<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Edit Availability') }}
      </h2>
  </x-slot>

  <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden p-6">
              
            <form method="POST" action="{{ route('availability.update', $availability->id) }}">
                @csrf
                @method('PUT')
                
                <!-- Day of week -->
                <div class="mb-6">
                    <x-input-label for="day_of_week" :value="__('Day of week')" />
                    <select id="day_of_week" name="day_of_week" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                        <option value="0">Sunday</option>
                    </select>
                    <x-input-error :messages="$errors->get('day_of_week')" class="mt-2" />
                </div>

                <!-- Start time -->
                <div class="mb-6">
                    <x-input-label for="start_time" :value="__('Start time')" />
                    <input id="start_time" name="start_time" type="time" value="{{ old('start_time', \Carbon\Carbon::parse($availability->start_time)->format('H:i')) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required />
                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                </div>

                <!-- End time -->
                <div class="mb-6">
                    <x-input-label for="end_time" :value="__('End time')" />
                    <input id="end_time" name="end_time" type="time" value="{{ old('end_time', \Carbon\Carbon::parse($availability->end_time)->format('H:i')) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required />
                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                </div>

                <!-- Slot duration -->
                <div class="mb-6">
                    <x-input-label for="slot_duration" :value="__('Time slot duration (minutes)')" />
                    <select id="slot_duration" name="slot_duration" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="60">1 hour</option>
                    </select>
                    <x-input-error :messages="$errors->get('slot_duration')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-8">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:underline">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Update Availability') }}
                    </x-primary-button>
                </div>
            </form>
              
          </div>
      </div>
  </div>
</x-app-layout>