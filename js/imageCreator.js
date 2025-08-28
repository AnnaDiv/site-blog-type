document.addEventListener('DOMContentLoaded', () => {

    if (!imageInput || !userID) return;

    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('user_id', userID);

        const xhr = new XMLHttpRequest();

        // Show loading UI
        spinner.style.display = 'flex';
        progressBarWrapper.style.display = 'block';

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                progressBar.style.width = `${percent}%`;
            }
        });

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                spinner.style.display = 'none';
                progressBarWrapper.style.display = 'none';
                progressBar.style.width = '0%';

                if (xhr.status === 200) {
                    let data;
                    try {
                        data = JSON.parse(xhr.responseText);
                    } catch {
                        alert('Invalid server response');
                        return;
                    }

                    if (data.success && data.image_url) {
                        previewImage.src = data.image_url;
                    } else {
                        alert(data.error || 'Unknown error occurred');
                        imageInput.value = '';
                    }
                } else {
                    alert(`Upload failed: ${xhr.status}`);
                    imageInput.value = '';
                }
            }
        };

        xhr.open('POST', 'src/APIs/imageCreator.api.php', true);
        xhr.send(formData);
    });
});