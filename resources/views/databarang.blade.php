<table class="table-auto border-collapse border border-gray-400 w-full">
    <thead>
        <tr class="bg-gray-200">
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Nama</th>
            <th class="border px-4 py-2">Harga</th>
        </tr>
    </thead>

    <tbody>
        @foreach($data as $dataku)
            <tr class="text-center">
                <td class="border px-4 py-2">{{ $dataku['id'] }}</td>
                <td class="border px-4 py-2">{{ $dataku['nama'] }}</td>
                <td class="border px-4 py-2">Rp {{ number_format($dataku['harga']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>