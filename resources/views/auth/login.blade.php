@extends('auth.app')
@section('title', 'Login')

@section('content')
    <section
        class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0 w-full sm:max-w-md">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold drop-shadow-md">
                <img class="w-8 h-8 mr-2 drop-shadow-md"
                    src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
                <span class="text-blue-700">Puri</span><span class="text-red-600">Apps</span>
            </a>
            <div class="w-full bg-white/40 backdrop-blur-lg rounded-xl shadow-2xl border border-white/50">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="text-xl text-center font-bold leading-tight tracking-tight text-gray-900 md:text-2xl drop-shadow-sm">
                        Welcome Back
                    </h1>
                    <form class="space-y-4 md:space-y-6" action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div>
                            <label for="username" class="block mb-2 text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username"
                                class="bg-gray-50/50 border border-gray-300 text-gray-900 rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/60 @error('username') border-red-500 focus:ring-red-500 focus:border-red-500 shake @enderror"
                                placeholder="Enter your username" required="" value="{{ old('username') }}">
                            @error('username')
                                <p class="mt-2 text-sm text-red-600 font-medium drop-shadow-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                    placeholder="••••••••"
                                    class="bg-gray-50/50 border border-gray-300 text-gray-900 rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/60 @error('password') border-red-500 focus:ring-red-500 focus:border-red-500 shake @enderror"
                                    required="">
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg x-show="!showPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" style="display: none;" class="h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 font-medium drop-shadow-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Math CAPTCHA -->
                        <div class="space-y-2">
                            <label for="captcha_answer" class="block mb-2 text-sm font-medium text-gray-700">Solve this:
                                <span id="captcha-question" class="font-bold text-blue-700">{{ $n1 }} + {{ $n2 }}</span> =
                                ?</label>
                            <div class="flex gap-2">
                                <input type="number" name="captcha_answer" id="captcha_answer"
                                    class="bg-gray-50/50 border border-gray-300 text-gray-900 rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 placeholder-gray-500 backdrop-blur-sm transition-all duration-300 hover:bg-white/60 @error('captcha_answer') border-red-500 focus:ring-red-500 focus:border-red-500 shake @enderror"
                                    placeholder="Answer" required="">
                                <button type="button" id="refresh-captcha"
                                    class="p-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors"
                                    title="Refresh Captcha">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('captcha_answer')
                                <p class="mt-2 text-sm text-red-600 font-medium drop-shadow-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="remember" aria-describedby="remember" type="checkbox" name="remember"
                                        class="w-4 h-4 border border-gray-400 rounded bg-gray-100 focus:ring-3 focus:ring-gray-500 ring-offset-gray-100 text-gray-600">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="remember" class="text-gray-600">Remember me</label>
                                </div>
                            </div>
                            <a href="#" id="forgot-password-link"
                                class="text-sm font-medium text-gray-600 hover:underline hover:text-gray-900 transition-colors">Forgot
                                password?</a>
                        </div>

                        <button type="submit"
                            class="w-full text-white bg-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 shadow-lg hover:shadow-gray-500/30 transform hover:-translate-y-0.5">Sign
                            in</button>
                    </form>
                    <p class="text-center text-gray-500 italic">
                        Created by Tim IT BPR Puriseger Sentosa
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('forgot-password-link').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Forgot Password?',
                text: "Enter your email address and we'll send you a link to reset your password.",
                input: 'email',
                inputPlaceholder: 'Enter your email address',
                showCancelButton: true,
                confirmButtonText: 'Send Reset Link',
                showLoaderOnConfirm: true,
                preConfirm: (email) => {
                    // Here you would typically make an AJAX request to your password reset route
                    return new Promise((resolve) => {
                        setTimeout(() => {
                            if (!email) {
                                Swal.showValidationMessage('Please enter an email address')
                            }
                            resolve()
                        }, 1000)
                    })
                },
                allowOutsideClick: () => !Swal.isLoading(),
                customClass: {
                    popup: 'bg-white/80 backdrop-blur-xl border border-white/50 rounded-xl shadow-2xl',
                    title: 'text-xl font-bold text-gray-900',
                    htmlContainer: 'text-gray-600',
                    input: 'bg-gray-50/50 border border-gray-300 text-gray-900 rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 placeholder-gray-500 backdrop-blur-sm transition-all duration-300',
                    confirmButton: 'text-white bg-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 shadow-lg hover:shadow-gray-500/30',
                    cancelButton: 'text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 border border-gray-300 hover:text-gray-900',
                    actions: 'space-x-3'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sent!',
                        text: 'Password reset link has been sent to your email.',
                        icon: 'success',
                        customClass: {
                            popup: 'bg-white/80 backdrop-blur-xl border border-white/50 rounded-xl shadow-2xl',
                            title: 'text-xl font-bold text-gray-900',
                            htmlContainer: 'text-gray-600',
                            confirmButton: 'text-white bg-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all duration-300 shadow-lg hover:shadow-gray-500/30'
                        },
                        buttonsStyling: false
                    })
                }
            })
        });

        document.getElementById('refresh-captcha').addEventListener('click', function () {
            const icon = this.querySelector('svg');
            icon.classList.add('animate-spin');

            fetch('{{ route('captcha.refresh') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('captcha-question').textContent = `${data.n1} + ${data.n2}`;
                    document.getElementById('captcha_answer').value = '';
                    icon.classList.remove('animate-spin');
                })
                .catch(error => {
                    console.error('Error refreshing captcha:', error);
                    icon.classList.remove('animate-spin');
                });
        });
    </script>
@endpush