<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Tersesat dalam Digital Arena</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F9F9F9] font-sans antialiased overflow-hidden">
    <div class="min-h-screen flex items-center justify-center p-6">
        <!-- Scanline Overlay -->
        <div class="fixed inset-0 pointer-events-none opacity-5 bg-[repeating-linear-gradient(0deg,transparent,transparent_2px,black_2px,black_4px)]"></div>
        
        <div class="max-w-xl w-full">
            <div class="bg-white border-4 border-black p-8 md:p-12 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden group">
                <!-- Decorative Corner -->
                <div class="absolute top-0 right-0 w-16 h-16 bg-[#FFDE00] border-l-4 border-b-4 border-black transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></div>
                
                <!-- Error Code -->
                <div class="font-black text-[120px] leading-none text-black/5 absolute -bottom-4 -right-4 select-none italic">404</div>

                <div class="relative z-10">
                    <div class="inline-block bg-[#ef4444] text-white border-4 border-black px-4 py-1 font-black text-xl mb-8 transform -rotate-1">
                        ERROR_CODE: 404
                    </div>

                    <h1 class="text-4xl md:text-5xl font-black text-black leading-none mb-6 uppercase tracking-tighter">
                        KOORDINAT TIDAK <br><span class="text-[#2563eb]">DITEMUKAN</span>.
                    </h1>

                    <p class="text-lg font-bold text-black/60 mb-10 max-w-md leading-relaxed">
                        Halaman yang Anda tuju telah menguap dalam bit dan byte. <br>
                        Mungkin ia tak pernah ada, atau sedang bersembunyi.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-[#2563eb] text-white border-4 border-black font-black text-lg uppercase tracking-tight shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[4px] hover:translate-y-[4px] active:translate-x-[6px] active:translate-y-[6px] transition-all">
                            KEMBALI KE DASHBOARD
                        </a>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t-4 border-black border-dashed">
                    <div class="flex items-center justify-between text-[10px] font-black text-black/40 uppercase tracking-[0.2em]">
                        <span>KOSGORO™ V1.0</span>
                        <span>SYSTEM_STATUS: ACTIVE</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
