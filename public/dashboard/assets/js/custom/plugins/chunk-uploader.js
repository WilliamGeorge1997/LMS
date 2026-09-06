"use strict";

window.ChunkUploader = {
    init: function (config) {
        var r = new Resumable({
            target: config.url,
            chunkSize: 2 * 1024 * 1024, // 2MB
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            testChunks: false
        });

        if (!r.support) {
            console.error('ResumableJS is not supported.');
            return null;
        }

        r.assignDrop($(config.dropZone)[0]);
        r.assignBrowse($(config.browseBtn)[0]);

        r.on('fileAdded', function(file) {
            if (config.onFileAdded) config.onFileAdded(file);
            r.upload();
        });

        r.on('fileProgress', function (file) {
            if (config.onProgress) config.onProgress(file, Math.floor(file.progress() * 100));
        });
        
        r.on('fileSuccess', function(file, message) {
            try {
                var response = JSON.parse(message);
                if (config.onSuccess) config.onSuccess(file, response);
            } catch(e) {
                if (config.onError) config.onError(file, message);
            }
        });

        r.on('fileError', function(file, message) {
            if (config.onError) config.onError(file, message);
        });

        return r;
    }
};
