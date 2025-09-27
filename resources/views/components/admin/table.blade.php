<div class="overflow-x-auto rounded shadow bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-3 py-2 text-left font-semibold">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
