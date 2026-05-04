<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - KOSGORO™</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F9F9F9] font-sans antialiased min-h-screen">
    <div class="max-w-4xl mx-auto p-6 md:p-12">
        <!-- Header Section -->
        <div class="mb-12 relative">
            <div class="absolute -top-4 -left-4 w-12 h-12 bg-[#2563eb] border-4 border-black -z-10"></div>
            <h1 class="text-4xl md:text-5xl font-black text-black uppercase tracking-tighter leading-none">
                PENGATURAN <br><span class="text-[#2563eb]">PROFIL</span> USER
            </h1>
            <p class="mt-4 font-bold text-black/60 uppercase tracking-widest text-xs">IDENTITAS DIGITAL ANDA DALAM SISTEM</p>
        </div>

        <div class="grid gap-12">
            <!-- Profile Information Card -->
            <div class="bg-white border-4 border-black p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative">
                <div class="absolute top-0 right-0 px-4 py-1 bg-black text-white font-black text-[10px] uppercase tracking-[0.2em]">
                    SECTION_01: INFO
                </div>

                <h2 class="text-2xl font-black text-black uppercase mb-8 border-b-4 border-black pb-2 inline-block">
                    INFORMASI DASAR
                </h2>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PATCH')

                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Name Field -->
                        <div class="space-y-2">
                            <label for="name" class="block font-black text-sm uppercase tracking-wider text-black">NAMA LENGKAP</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-4 py-3 border-4 border-black bg-white font-bold text-black focus:ring-0 focus:border-[#2563eb] transition-colors placeholder-black/20"
                                placeholder="Masukkan nama...">
                            @error('name') <p class="text-[#ef4444] font-black text-xs uppercase mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label for="email" class="block font-black text-sm uppercase tracking-wider text-black">ALAMAT EMAIL</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-3 border-4 border-black bg-white font-bold text-black focus:ring-0 focus:border-[#2563eb] transition-colors placeholder-black/20"
                                placeholder="nama@domain.com">
                            @error('email') <p class="text-[#ef4444] font-black text-xs uppercase mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="inline-flex items-center justify-center px-10 py-4 bg-[#ef4444] text-white border-4 border-black font-black text-lg uppercase tracking-tight shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[4px] hover:translate-y-[4px] active:translate-x-[6px] active:translate-y-[6px] transition-all">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>

            <!-- Additional Settings Placeholder -->
            <div class="bg-[#FFDE00] border-4 border-black p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative group">
                <div class="absolute top-0 right-0 px-4 py-1 bg-black text-white font-black text-[10px] uppercase tracking-[0.2em]">
                    SECTION_02: AKSES
                </div>
                <h2 class="text-2xl font-black text-black uppercase mb-4">PENGATURAN KEAMANAN</h2>
                <p class="font-bold text-black/80 leading-relaxed mb-6">Ingin memperbarui kata sandi? Pastikan gunakan kombinasi yang sulit ditebak namun mudah diingat oleh Anda.</p>
                <a href="#" class="inline-block font-black text-sm uppercase underline decoration-4 underline-offset-4 hover:text-[#2563eb] transition-colors">GANTI PASSWORD SEKARANG →</a>
            </div>
        </div>

        <!-- Back Link -->
        <div class="mt-12 text-center">
            <a href="{{ route('dashboard') }}" class="font-black text-xs uppercase tracking-[0.2em] text-black/40 hover:text-black transition-colors">
                ← KEMBALI KE PANEL UTAMA
            </a>
        </div>
    </div>
</body>
</html>
