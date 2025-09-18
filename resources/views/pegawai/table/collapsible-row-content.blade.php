<div class="p-4 text-sm text-gray-700">
    <p><strong>Tempat Tanggal Lahir:</strong> {{ $getRecord()->tempat_tanggal_lahir ?? '-' }}</p>
    <p><strong>Jenis Kelamin:</strong> 
        {{ $getRecord()->jenis_kelamin ?? '-' }}
    </p>
    <p><strong>Alamat:</strong> {{ $getRecord()->alamat ?? '-' }}</p>
    <p><strong>Telepon:</strong> {{ $getRecord()->phone ?? '-' }}</p>
    <p><strong>Email:</strong> {{ $getRecord()->user->email ?? '-' }}</p>
    <p><strong>Status:</strong> {{ $getRecord()->status ?? '-' }}</p>
    {{-- <p><strong>Created At:</strong> {{ $getRecord()->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
    <p><strong>Updated At:</strong> {{ $getRecord()->updated_at?->format('d/m/Y H:i') ?? '-' }}</p> --}}
</div>