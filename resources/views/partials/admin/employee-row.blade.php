<tr class="border-b border-slate-100 hover:bg-blue-50/60">
    <td class="px-4 py-3 text-sm text-slate-600">{{ $index }}</td>
    <td class="px-4 py-3 font-semibold text-slate-800">{{ $employee['name'] }}</td>
    <td class="px-4 py-3 text-sm text-slate-600">{{ $employee['division'] }}</td>
    <td class="px-4 py-3">
        <button
            type="button"
            class="rounded-lg border border-blue-600 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-600 hover:text-white"
        >
            Lihat Detail
        </button>
    </td>
</tr>
