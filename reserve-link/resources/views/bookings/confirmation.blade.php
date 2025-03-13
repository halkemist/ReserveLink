<x-guest-layout>
  <div class="container mx-auto px-4 py-8 max-w-5xl">
      <h1 class="text-2xl font-bold mb-6 text-center text-gray-800 dark:text-gray-200">
          Booking Confirmation
      </h1>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
          @if (session('success'))
              <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg mb-6">
                  <p class="text-green-800 dark:text-green-200 text-center">
                      {{ session('success') }}
                  </p>
              </div>
          @endif
          
          <div class="mb-8">
              <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-300">
                  Booking Details
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="border dark:border-gray-700 rounded-lg p-4">
                      <h3 class="font-medium text-gray-800 dark:text-gray-200 mb-3">
                          Date & Time
                      </h3>
                      <div class="space-y-2 text-gray-600 dark:text-gray-300">
                          <p class="flex justify-between">
                              <span>Date:</span>
                              <span class="font-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y') }}</span>
                          </p>
                          <p class="flex justify-between">
                              <span>Start Time:</span>
                              <span class="font-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</span>
                          </p>
                          <p class="flex justify-between">
                              <span>End Time:</span>
                              <span class="font-medium">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                          </p>
                          <p class="flex justify-between">
                              <span>Duration:</span>
                              <span class="font-medium">
                                  {{ \Carbon\Carbon::parse($booking->start_time)->diffInMinutes($booking->end_time) }} minutes
                              </span>
                          </p>
                      </div>
                  </div>
                  
                  <div class="border dark:border-gray-700 rounded-lg p-4">
                      <h3 class="font-medium text-gray-800 dark:text-gray-200 mb-3">
                          Status Information
                      </h3>
                      <div class="space-y-2 text-gray-600 dark:text-gray-300">
                          <p class="flex justify-between">
                              <span>Status:</span>
                              <span class="font-medium px-2 py-1 rounded-full text-xs 
                                  {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                    ($booking->status === 'canceled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                     'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                  {{ ucfirst($booking->status) }}
                              </span>
                          </p>
                          
                          @if($booking->user_id)
                              <p class="flex justify-between">
                                  <span>Booked By:</span>
                                  <span class="font-medium">
                                      {{ $booking->user ? $booking->user->name : 'Unknown User' }}
                                  </span>
                              </p>
                          @endif
                          
                          @if($booking->guest_email)
                              <p class="flex justify-between">
                                  <span>Guest Email:</span>
                                  <span class="font-medium">{{ $booking->guest_email }}</span>
                              </p>
                          @endif
                          
                          <p class="flex justify-between">
                              <span>With:</span>
                              <span class="font-medium">
                                  {{ $booking->owner ? $booking->owner->name : 'Unknown Owner' }}
                              </span>
                          </p>
                          
                          <p class="flex justify-between">
                              <span>Created On:</span>
                              <span class="font-medium">{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                          </p>
                      </div>
                  </div>
              </div>
          </div>
          
          <div class="flex justify-center mt-6">
              <a href="{{ route('dashboard') }}" 
                 class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md text-center transition-colors duration-200">
                  Back to Dashboard
              </a>
          </div>
          
          <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
              <p class="text-blue-800 dark:text-blue-200 text-center text-sm">
                  A confirmation email will be sent{{ $booking->guest_email ? ' to ' . $booking->guest_email : '' }} shortly.
              </p>
          </div>
      </div>
  </div>
</x-guest-layout>