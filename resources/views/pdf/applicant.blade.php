<!DOCTYPE html>
<html>
<head>
    <title>Applicant PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
        }
        .header h2 {
            margin: 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            background-color: #f2f2f2;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .section p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('path/to/company-logo.png') }}" alt="Company Logo">
        <h2>Company Name</h2>
        <p>Company Address Line 1</p>
        <p>Company Address Line 2</p>
        <p>Phone: Company Phone</p>
        <p>Email: Company Email</p>
    </div>

    <div class="section">
        <h2><strong>1. BUTIRAN PEMOHON</strong></h2>
        <p>Name: {{ $applicant->nama }}</p>
        <p>Email: {{ $applicant->email }}</p>
        <p>Phone: {{ $applicant->telefon_b }}</p>
    </div>

    <div class="section">
        <h2><strong>2. BUTIRAN PENGUSAHA</strong></h2>
        @if($butiranPengusaha)
            <p>Nama Pengusaha: {{ $butiranPengusaha->nama_pengusaha }}</p>
            <p>Kad Pengenalan Pengusaha: {{ $butiranPengusaha->kad_pengenalan_pengusaha }}</p>
            <p>Warganegara Pengusaha: {{ $butiranPengusaha->warganegara_pengusaha }}</p>
            <p>Alamat Rumah Pengusaha: {{ $butiranPengusaha->alamat_rumah_pengusaha }}</p>
            <p>Email Pengusaha: {{ $butiranPengusaha->email_pengusaha }}</p>
            <p>Telefon Pengusaha: {{ $butiranPengusaha->telefon_pengusaha }}</p>
        @else
            <p>No Pengusaha details available.</p>
        @endif
    </div>

    <div class="section">
        <h2><strong>3. BUTIRAN PENGURUS</strong></h2>
        @if($butiranPengurus)
            <p>Nama Pengurus: {{ $butiranPengurus->nama_pengurus }}</p>
            <p>Kad Pengenalan Pengurus: {{ $butiranPengurus->kad_pengenalan_pengurus }}</p>
            <p>Umur Pengurus: {{ $butiranPengurus->umur_pengurus }}</p>
            <p>Kelulusan Akademik Pengurus: {{ $butiranPengurus->kelulusan_akademik_pengurus }}</p>
        @else
            <p>No Pengurus details available.</p>
        @endif
    </div>

    <div class="section">
        <h2><strong>4. BUTIRAN PENYELIA</strong></h2>
        @if($butiranPenyelia)
            <p>Nama Penyelia: {{ $butiranPenyelia->nama_penyelia }}</p>
            <p>Kad Pengenalan Penyelia: {{ $butiranPenyelia->kad_pengenalan_penyelia }}</p>
            <p>Umur Penyelia: {{ $butiranPenyelia->umur_penyelia }}</p>
            <p>Kelulusan Akademik Penyelia: {{ $butiranPenyelia->kelulusan_akademik_penyelia }}</p>
        @else
            <p>No Penyelia details available.</p>
        @endif
    </div>

    <div class="section">
        <h2><strong>5. BUTIRAN TASKA</strong></h2>
        @if($butiranTaska)
            <p>Nama Taska: {{ $butiranTaska->nama_taska }}</p>
            <p>Alamat Taska: {{ $butiranTaska->alamat_taska }}</p>
            <p>Telefon Taska: {{ $butiranTaska->telefon_taska_r }}</p>
            <p>Laman Web Taska: {{ $butiranTaska->laman_web_taska }}</p>
        @else
            <p>No Taska details available.</p>
        @endif
    </div>

    <div class="section">
        <h2><strong>6. MAKLUMAT PEKERJA</strong></h2>
        @if($maklumatPekerja->isNotEmpty())
            @foreach($maklumatPekerja as $pekerja)
                <p>Nama Pekerja: {{ $pekerja->nama_pekerja }}</p>
                <p>Kad Pengenalan Pekerja: {{ $pekerja->kad_pengenalan_pekerja }}</p>
                <p>Umur Pekerja: {{ $pekerja->umur_pekerja }}</p>
                <p>Jantina Pekerja: {{ $pekerja->jantina_pekerja }}</p>
                <p>Kelayakan Pekerja: {{ $pekerja->kelayakan_pekerja }}</p>
                <p>Jawatan Pekerja: {{ $pekerja->jawatan_pekerja }}</p>
                <p>Tarikh Mula Pekerja: {{ $pekerja->tarikh_mula_pekerja }}</p>
                <hr>
            @endforeach
        @else
            <p>No Pekerja details available.</p>
        @endif
    </div>
</body>
</html>