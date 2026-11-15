<x-app-layout>
    <style> .glass-table { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 10px 30px rgba(0,0,0,0.03); } </style>
    <div class="pb-12 animate-fade-in-up">
        <div class="flex justify-between items-end mb-6">
            <div><h2 class="text-3xl font-bold text-[#2b3674]" style="font-family: 'Playfair Display', serif;">Ulasan & Rating</h2><p class="text-sm text-gray-500 mt-1">Pantau pendapat siswa tentang koleksi bukumu.</p></div>
        </div>
        <div class="glass-table overflow-hidden">
            <div class="overflow-x-auto p-2">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-gray-400 uppercase border-b border-gray-100">
                            <th class="py-4 px-6">Siswa & Buku</th>
                            <th class="py-4 px-6 text-center">Rating</th>
                            <th class="py-4 px-6">Komentar</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $r)
                        <tr class="border-b border-gray-50 hover:bg-white/50 transition">
                            <td class="py-3 px-6"><p class="font-bold text-[#2b3674] text-sm">{{ $r->user->name }}</p><p class="text-[10px] text-purple-600 font-bold">{{ $r->book->title }}</p></td>
                            <td class="py-3 px-6 text-center text-yellow-400 font-black text-lg">{{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}</td>
                            <td class="py-3 px-6 text-xs text-gray-600 italic">"{{ $r->comment }}"</td>
                            <td class="py-3 px-6 text-center">
                                <form action="{{ route('admin.ulasan.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 text-[10px] font-bold rounded-lg transition"><i data-feather="trash-2" class="w-3 h-3"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-12 text-center text-gray-400 italic">Belum ada ulasan dari siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>