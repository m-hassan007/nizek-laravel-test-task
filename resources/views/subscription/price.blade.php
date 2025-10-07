<x-auth-layout>
    <x-slot name="title">
        @lang('Register')
    </x-slot>

    <x-auth-card>
        <x-slot name="logo">
            <a href="/"></a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <!-- Pricing -->
        <div class="max-w-[100rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
            <!-- Title -->
            <div class="max-w-2xl mx-auto text-center mb-10 lg:mb-14">
                <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Subscription Plans</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Choose the plan that better fits your needs.</p>
            </div>
            <!-- End Title -->

            <!-- Flex Container for Horizontal Layout -->
            <div>
                <!-- Dynamic Plans -->
                <div>
                    <form action="{{ action('App\Http\Controllers\Frontend\SubscriptionController@submit') }}" method="POST">
                        @csrf
                        <!-- Title -->
                        <div class="max-w-2xl mx-auto text-center mb-10 lg:mb-14">
                            <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Subscription Plans</h2>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">Choose the plan that better fits your needs.</p>
                        </div>
                        <!-- End Title -->

                        <!-- Dynamic Plans -->
                        <div>
                            @foreach ($activePlans as $plan)
                                <div class="flex flex-col border {{ $loop->first ? 'border-indigo-600 shadow-xl' : 'border-gray-200' }} text-center rounded-xl p-8 dark:border-gray-700 w-full sm:max-w-xs">
                                    <input type="radio" id="{{ $plan->id }}" name="subscription_plan" value="{{ $plan->id }}" class="hidden" required />
                                    <label for="{{ $plan->id }}" class="cursor-pointer">
                                        @if ($loop->first)
                                            <p class="mb-3">
                                                <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-lg text-xs uppercase font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-600 dark:text-white">Most Popular</span>
                                            </p>
                                        @endif
                                        <h4 class="font-medium text-lg text-gray-800 dark:text-gray-200">{{ $plan->nickname ?? 'Unnamed Plan' }}</h4>
                                        <span class="mt-5 font-bold text-5xl text-gray-800 dark:text-gray-200">
                        ${{ number_format($plan->amount / 100, 2) }}
                    </span>
                                        <p class="mt-2 text-sm text-gray-500">
                                            Billed {{ $plan->interval_count }} {{ Str::plural($plan->interval, $plan->interval_count) }}
                                        </p>
                                    </label>
                                </div>
                            @endforeach

                        </div>
                        <!-- End Dynamic Plans -->

                        <!-- Submit Button -->
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="submit" style="padding: 10px 20px; background-color: blue; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                                Sign up and Proceed
                            </button>
                        </div>
                    </form>
                </div>
                <!-- End Dynamic Plans -->

                <!-- Submit Button -->
                <div class="mt-6 text-center">
                    <button type="submit" class="py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                        Sign up and Proceed
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 text-center">
                <button type="submit" class="py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    Sign up and Proceed
                </button>
            </div>
        </div>
        <!-- End Pricing -->

        <x-slot name="extra">
            <p class="text-center text-gray-600 mt-4">
                Already have an account? <a href="{{ route('login') }}" class="underline hover:text-gray-900">Login</a>.
            </p>
        </x-slot>
    </x-auth-card>
</x-auth-layout>
