<template>
    <div id="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ user.first_name }} {{ user.last_name }}</h3>
                            <h3>{{ user.email }}</h3>
                            <div class="custom-file">
                                <input type="file" id="file" ref="file" class="custom-file-input" v-on:change="handleFileUpload()">
                                <label class="custom-file-label" for="file" id="fileNamePlaceholder" v-if="user.configFileName">{{ user.configFileName}}</label>
                                <label class="custom-file-label" for="file" id="fileNamePlaceholder" v-else="user.config">Upload JSON configuration file</label>
                            </div>

                            <a href="#" class="btn btn-primary mt-2" @click="uploadFile">Save</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "UserSettingsComponent",
    data() {
        return {
            file: ''
        }
    },
    props: {
        user: {
            required: true
        }
    },
    methods: {
        uploadFile(){
            let formData = new FormData()
            formData.append('fileName', this.file.name)
            formData.append('file', this.file)
            axios
                .post( '/uploadConfigFile',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )
                .then(function (response){
                })
                .catch(function (error){
                    console.log(error)
                })
        },
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            document.getElementById("fileNamePlaceholder").innerHTML = this.file.name
        }
    }
}
</script>
