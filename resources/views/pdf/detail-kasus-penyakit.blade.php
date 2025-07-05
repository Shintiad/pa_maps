<!DOCTYPE html>
<html>
<head>
    <title>Data Detail Kasus Penyakit (Berdasarkan Desa)</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 5px;
        }
        th {
            background-color: #f2f2f2;
        }
        body {
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Data Detail Kasus Penyakit (Berdasarkan Desa)</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun</th>
                <th>Kecamatan</th>
                <th>Desa</th>
                <th>Nama Penyakit</th>
                <th>Jumlah Terjangkit</th>
                <th>Jumlah Meninggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item['no'] }}</td>
                <td>{{ $item['tahun'] }}</td>
                <td>{{ $item['kecamatan'] }}</td>
                <td>{{ $item['desa'] }}</td>
                <td>{{ $item['penyakit'] }}</td>
                <td>{{ $item['terjangkit'] }}</td>
                <td>{{ $item['meninggal'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>