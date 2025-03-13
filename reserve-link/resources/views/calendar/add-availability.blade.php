<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('Add Availability') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                  
                  @if ($errors->any())
                      <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                          <ul class="list-disc pl-5">
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                  @endif

                  <form method="POST" action="{{ route('availability.store') }}">
                      @csrf
                      
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
                          <input id="start_time" name="start_time" type="time" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required />
                          <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                      </div>
                      
                      <!-- End time -->
                      <div class="mb-6">
                          <x-input-label for="end_time" :value="__('End time')" />
                          <input id="end_time" name="end_time" type="time" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required />
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
                      
                      <div class="flex items-center justify-end gap-4 mt-8">
                          <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-300 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                              Cancel
                          </a>
                          
                          <x-primary-button>
                              {{ __('Save Availability') }}
                          </x-primary-button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
</x-app-layout>