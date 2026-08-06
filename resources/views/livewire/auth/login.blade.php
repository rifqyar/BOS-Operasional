<div class="flex flex-col gap-7">
    <div class="space-y-2">
        <p class="text-sm font-medium text-sky-700">Selamat datang kembali</p>
        <h2 class="text-3xl font-semibold leading-tight tracking-normal text-neutral-950">Login BOS Operasional</h2>
        <p class="text-sm leading-6 text-neutral-600">Gunakan akun terdaftar untuk melanjutkan ke dashboard operasional.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status
        class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-800"
        :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- username -->
        <flux:field>
            <flux:label class="text-neutral-600! dark:text-neutral-600!">
                {{ __('Username') }}
            </flux:label>

            <flux:input wire:model="username" type="text" name="username" required autofocus autocomplete="username"
                placeholder="Masukkan Username Anda"
                input:class="text-neutral-600! dark:text-neutral-600! placeholder:text-neutral-400! dark:placeholder:text-neutral-400!" />

            <flux:error name="username" />
        </flux:field>

        <!-- Password -->
        <div class="relative space-y-2">
            <flux:field>
                <flux:label class="text-neutral-600! dark:text-neutral-600!">
                    {{ __('Password') }}
                </flux:label>

                <flux:input wire:model="password" type="password" name="password" required
                    autocomplete="current-password" placeholder="Masukan Password Anda"
                    input:class="text-neutral-600! dark:text-neutral-600! placeholder:text-neutral-400! dark:placeholder:text-neutral-400!" />
            </flux:field>
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full !bg-sky-700 !text-white hover:!bg-sky-800">
                {{ __('Log in') }}
            </flux:button>
        </div>
    </form>
</div>
