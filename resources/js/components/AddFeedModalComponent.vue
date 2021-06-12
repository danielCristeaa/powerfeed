<template>
    <div class="modal fade" id="addFeedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add a feed</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Name" v-model="name">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="url" placeholder="URL" v-model="url">
                        </div>
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" id="file" ref="file" class="custom-file-input" v-on:change="handleFileUpload()">
                                <label class="custom-file-label" for="file" v-if="file">{{ file.name }}</label>
                                <label class="custom-file-label" for="file" v-else>Upload JSON configuration file</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Merchant ID" v-model="merchantId">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" @click="sendData()" data-dismiss="modal">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AddFeedModalComponent",
    data() {
        return {
            name: null,
            url: null,
            file: '',
            merchantId: null
        }
    },
    methods:{
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            document.querySelector(".custom-file-label").innerHTML = this.file.name
        },
        sendData() {
            const self = this
            let formData = new FormData()
            formData.append('name', this.name)
            formData.append('url', this.url)
            formData.append('fileName', this.file.name)
            formData.append('file', this.file)
            formData.append('merchantId', this.merchantId)
            axios
                .post("/addFeed", formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                .then(function (response){
                    self.$emit('new-feed')
                })
                .catch(function (error){
                    console.log(error)
                })
        },
    }
}
</script>

<style scoped>

</style>
