<div class="flex items-center justify-center min-h-screen">

    {{-- Tambahkan Background --}}
    <style>
        body {
            background: url("{{ asset('images/bg_kpu.jpg') }}") no-repeat center center fixed !important;
            background-size: cover !important;
            background-position: center;
            padding: 0 5%;
        }

        /* Tambahkan overlay gelap */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Warna hitam dengan transparansi */
            z-index: -1; /* Pastikan overlay berada di belakang konten */
        }

        .btn-maroon {
            background-color: #dd0505;
            color: white;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            width: 100%;
            transition: background-color 0.3s;
        }

        .btn-maroon:hover {
            background-color: #ff1f1f; /* lebih terang saat hover */
        } 

    </style>

    {{-- Card Login --}}
    <div class="bg-white/95 backdrop-blur-sm rounded-4xl shadow-xl p-4 w-full max-w-md">
        
        {{-- Branding --}}
        <div class="text-center mb-6">
            <img 
                src="{{ asset('images/logo_kpu1.png') }}" 
                alt="Logo KPU" 
                class="mx-auto h-16 w-auto mb-3 object-contain"
            />
            <br>
            <h1 class="text-3xl font-bold text-gray-900">SIAGA</h1>
            <h1 class="text-2x1 text-gray-700">(Sistem Informasi Absensi peGAwai)</h1>
            <br>
        </div>

        {{-- Form Login --}}
        <x-filament-panels::form id="form" wire:submit="authenticate">
            {{ $this->form }}

            <x-filament::button type="submit" class="btn-maroon mt-4 w-full font-semibold py-2 px-4 rounded-lg">
                {{ __('Masuk') }}
            </x-filament::button>

        </x-filament-panels::form>
    </div>
</div>
