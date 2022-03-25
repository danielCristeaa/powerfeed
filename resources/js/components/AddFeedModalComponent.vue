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
            if(this.file) {
                document.querySelector(".custom-file-label").innerHTML = this.file.name
            }
        },
        resetFormData(){
            this.name = null
            this.url = null
            this.file = ''
            this.merchantId = null
            this.$refs.file.value = "";
        },
        sendData() {
            const self = this
            let unfilledInput = null
            if(!this.merchantId)
                unfilledInput = 'merchantID'
            if(!this.file)
                unfilledInput = 'JSON config file'
            if(!this.url)
                unfilledInput = 'URL'
            if(!this.name)
                unfilledInput = 'name'
            if(unfilledInput){
                this.$notify({
                    title: 'Error',
                    text: `Missing ${unfilledInput}!`,
                    type: 'error',
                    duration: 3000,
                })
                return
            }

            let formData = new FormData()
            formData.append('name', this.name)
            formData.append('url', this.url)

            formData.append('fileName', this.file.name)
            formData.append('file', this.file)


            formData.append('merchantId', this.merchantId)

            this.$notify({
                group: 'processing',
                title: 'Processing',
                text: 'Your new feed is being processed. This may take some time.',
                type: 'warn',
                duration: -1,
            })

            axios
                .post("/addFeed", formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                .then(function (response){
                    if(response.data.success){
                        self.$notify({
                            title: 'Success',
                            text: response.data.message,
                            type: 'success',
                            duration: 3000,
                        })
                        self.resetFormData()
                        self.$emit('new-feed')
                    }
                    else {
                        self.$notify({
                            title: 'Error',
                            text: response.data.message,
                            type: 'error',
                            duration: 3000,
                        })
                    }

                    self.$notify({
                        group: 'processing',
                        clean: true
                    })

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
