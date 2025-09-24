<textarea {{ $attributes->merge(['class' => 'tinymce']) }}>
    {{ $slot ?? '' }}
</textarea>

@push('scripts')
<script>
    const image_upload_handler_callback = (blobInfo, progress) => new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/upload-image');

        xhr.upload.onprogress = (e) => {
            progress(e.loaded / e.total * 100);
        };

        xhr.onload = () => {
            if (xhr.status !== 200) {
                reject('HTTP Error: ' + xhr.status);
                return;
            }

            const json = JSON.parse(xhr.responseText);

            if (!json || typeof json.filename !== 'string') {
                reject('Invalid response: ' + xhr.responseText);
                return;
            }

            resolve('/storage/uploads/' + json.filename);
        };

        xhr.onerror = () => {
            reject('Image upload failed. Code: ' + xhr.status);
        };

        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.content);
        }

        xhr.send(formData);
    });

    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
        toolbar: 'undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl',
        height: 300,
        license_key: 'gpl',
        automatic_uploads: true,
        images_upload_url: '/upload-image',
        images_upload_handler: image_upload_handler_callback,
        entity_encoding: 'raw',
    });
</script>
@endpush
