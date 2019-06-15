# vue-filepond server laravel

Installation:
```bash
composer require itsdp/filepond-server
```

Add a `disks` to config/filesystems.php
```php
'upload' => [
    'driver' => 'local',
    'root' => public_path(),
],
```

Filepond vue component:
```html
<template>
  <div id="app">

    <file-pond
        name="file[]"
        ref="pond"
        label-idle="Drop files here or <span class='filepond--label-action'>Browse</span>"
        allow-multiple="true"
        accepted-file-types="image/jpeg, image/png, image/jpg"
        server="/filepond/api/process"
        v-bind:files="myFiles"
        v-on:init="handleFilePondInit"/>

  </div>
</template>

<script>
// Import Vue FilePond
import vueFilePond from 'vue-filepond';

// Import FilePond styles
import 'filepond/dist/filepond.min.css';

// Import FilePond plugins
// Please note that you need to install these plugins separately

// Import image preview plugin styles
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';

// Create component
const FilePond = vueFilePond(
        FilePondPluginFileValidateType,
        FilePondPluginImagePreview
    );

export default {
    // name: 'app',
    data: function() {
        return { myFiles: [] };
    },
    methods: {

        handleFilePondInit: function() {
            console.log('FilePond has initialized');

            // FilePond instance methods are available on `this.$refs.pond`this.$refs.pond
            this.$refs.pond
        }
    },
    components: {
        FilePond
    }
};
</script>
```
