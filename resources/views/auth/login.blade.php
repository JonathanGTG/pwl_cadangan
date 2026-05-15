<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="nama@email.com"
                pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
                class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm
                @error('email') border-red-400 @enderror">
            @error('email')
                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="ph ph-warning-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <div class="relative">
                <input type="password" name="password" id="loginPass" required
                    minlength="8"
                    placeholder="Minimal 8 karakter"
                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-elco-mocha/30 text-sm pr-12
                    @error('password') border-red-400 @enderror">
                <button type="button" onclick="toggleLoginPass()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="ph ph-eye" id="loginPassIcon"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <i class="ph ph-warning-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

@push('scripts')
<script>
function toggleLoginPass() {
    const input = document.getElementById('loginPass');
    const icon  = document.getElementById('loginPassIcon');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('ph-eye');
    icon.classList.toggle('ph-eye-slash');
}
</script>
@endpush
</x-guest-layout>
