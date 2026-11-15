<x-app-layout>
    <style> .glass-table { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 10px 30px rgba(0,0,0,0.03); } .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); border-radius: 20px; border: 1px solid rgba(255,255,255,1); } </style>
    <div class="pb-12 animate-fade-in-up">
        <div class="flex justify-between items-end mb-6">
            <div><h2 class="text-3xl font-bold text-[#2b3674]" style="font-family: 'Playfair Display', serif;">Tantangan Membaca</h2><p class="text-sm text-gray-500 mt-1">Buat misi membaca berhadiah XP untuk memotivasi siswa.</p></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-card p-6 shadow-xl col-span-1 border-purple-100">
                <h3 class="font-bold text-lg text-[#2b3674] mb-4">Buat Misi Baru</h3>
                <form action="{{ route('admin.tantangan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div><label class="text-[10px] font-bold text-gray-500 uppercase">Judul Misi</label><input type="text" name="title" required class="w-full text-xs rounded-xl border-gray-200" placeholder="Cth: Kutu Buku November"></div>
                    <div><label class="text-[10px] font-bold text-gray-500 uppercase">Deskripsi</label><textarea name="description" rows="2" class="w-full text-xs rounded-xl border-gray-200" placeholder="Baca 3 buku di bulan ini!"></textarea></div>
                    <div class="flex gap-2">
                        <div class="w-1/2"><label class="text-[10px] font-bold text-gray-500 uppercase">Target Buku</label><input type="number" name="target_amount" value="1" min="1" class="w-full text-xs rounded-xl border-gray-200"></div>
                        <div class="w-1/2"><label class="text-[10px] font-bold text-purple-500 uppercase">Hadiah XP ✨</label><input type="number" name="xp_reward" value="50" min="10" step="10" class="w-full text-xs rounded-xl border-purple-200 bg-purple-50 text-purple-700 font-bold"></div>
                    </div>
                    <div><label class="text-[10px] font-bold text-gray-500 uppercase">Batas Waktu (Opsional)</label><input type="date" name="end_date" class="w-full text-xs rounded-xl border-gray-200"></div>
                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-xs rounded-xl shadow-lg hover:shadow-purple-500/30 transition">Terbitkan Misi</button>
                </form>
            </div>

            <div class="glass-table overflow-hidden col-span-2">
                <div class="overflow-x-auto p-2">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-bold text-gray-400 uppercase border-b border-gray-100">
                                <th class="py-4 px-6">Misi & Detail</th>
                                <th class="py-4 px-6 text-center">Reward XP</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($challenges as $c)
                            <tr class="border-b border-gray-50 hover:bg-white/50 transition">
                                <td class="py-3 px-6"><p class="font-bold text-[#2b3674] text-sm">{{ $c->title }}</p><p class="text-[10px] text-gray-500">Target: {{ $c->target_amount }} Buku | Tenggat: {{ $c->end_date ? \Carbon\Carbon::parse($c->end_date)->format('d/m/Y') : 'Tanpa Batas' }}</p></td>
                                <td class="py-3 px-6 text-center text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500">+{{ $c->xp_reward }} XP</td>
                                <td class="py-3 px-6 text-center">
                                    <form action="{{ route('admin.tantangan.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Tarik misi ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 text-[10px] font-bold rounded-lg transition"><i data-feather="trash-2" class="w-3.5 h-3.5"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="py-12 text-center text-gray-400 italic">Belum ada misi aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>