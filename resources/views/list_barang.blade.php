<h2>List Barang</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Harga</th>
    </tr>

    @foreach($data as $dataku)
    <tr>
        <td>{{ $dataku['id'] }}</td>
        <td>{{ $dataku['nama'] }}</td>
        <td>{{ $dataku['harga'] }}</td>
    </tr>
    @endforeach
</table>