<div class="flex items-center justify-center min-h-screen ">
    <div class="w-full max-w-5xl p-6 mx-auto rounded-xl shadow-lg">
        <div class="space-y-4">
            @if($asset->file_type === 'image')
                <img src="{{ $asset->previewUrl }}"
                     alt="{{ $asset->file_name }}"
                     class="w-full h-auto rounded-lg">
            @elseif($asset->file_type === 'video')
                <video controls class="w-full">
                    <source src="{{ $asset->previewUrl }}"
                            type="{{ $asset->mime_type }}">
                    Your browser does not support the video tag.
                </video>
            @elseif($asset->file_type === 'audio')
                <audio controls class="w-full">
                    <source src="{{ $asset->previewUrl }}"
                            type="{{ $asset->mime_type }}">
                    Your browser does not support the audio tag.
                </audio>
            @else
                <div class="p-4 text-center bg-gray-50 rounded-lg">
                    <p class="text-lg font-medium">{{ $asset->file_name }}.{{ $asset->extension }}</p>
                    <p class="text-sm text-gray-500">{{ $asset->mime_type }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
