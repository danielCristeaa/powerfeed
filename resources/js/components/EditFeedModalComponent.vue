<template>
    <div class="modal fade" id="editFeedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Feed</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="form-group">
                            <label class="col-form-label">Name</label>
                            <input type="text" class="form-control" name="name" v-model="newName">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">URL</label>
                            <input type="text" class="form-control" name="url" v-model="newUrl">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">JSON config file</label>
                            <div class="custom-file">
                                <input type="file" id="file" ref="file" class="custom-file-input" v-on:change="handleFileUpload">
                                <label class="custom-file-label" for="file" v-if="configFileName">{{ configFileName}}</label>
                                <label class="custom-file-label" for="file" v-else>Upload JSON configuration file</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Merchant ID</label>
                            <input type="text" class="form-control" v-if="merchantId" v-model="merchantId">
                            <input type="text" class="form-control" v-else v-model="merchantId" placeholder="Merchant ID">
                        </div>
                        <div class="form-group">
                            <select class="custom-select" aria-label="Add columns" v-model="addColumns">
                                <option selected value="0">Add columns at the end of the feed</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="sendData()" data-dismiss="modal">Submit</button>
                            <button type="button" class="btn btn-danger" @click="deleteFeed()" data-dismiss="modal">Delete feed</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EditFeedModalComponent",
    props: {
        feedId: {
            required: true
        }
    },
    data() {
      return {
          oldName: null,
          newName: null,
          oldURL: null,
          newUrl: null,
          oldConfig: null,
          config: null,
          oldConfigFileName: null,
          configFileName: null,
          oldMerchantId: null,
          merchantId: null,
          addColumns: 0
      }
    },
    watch: {
        feedId(id) {
            this.feedId = id;
            axios
                .get("/feed/"+this.feedId)
                .then(response => {
                    this.newName = this.oldName = response.data.data[2]
                    this.newUrl = this.oldURL = response.data.data[3]
                    this.config = this.oldConfig = response.data.data[4]
                    this.configFileName = this.oldConfigFileName = response.data.data[5]
                    this.merchantId = this.oldMerchantId = response.data.data[6]
                })
        }
    },
    methods: {
        sendData() {
            const self = this
            let unfilledInput = null
            if(!this.merchantId) {
                unfilledInput = 'merchantID'
                this.merchantId = this.oldMerchantId
            }
            if(!this.config) {
                unfilledInput = 'JSON config file'
                this.config = this.oldConfig
            }
            if(!this.newUrl) {
                unfilledInput = 'URL'
                this.newUrl = this.oldURL
            }
            if(!this.newName) {
                unfilledInput = 'name'
                this.newName = this.oldName
            }
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
            formData.append('name', this.newName)
            formData.append('url', this.newUrl)
            formData.append('merchantId', this.merchantId)
            formData.append('addColumns', this.addColumns)

            if(this.file) {
                formData.append('fileName', this.file.name)
                formData.append('file', this.file)
            }

            axios
                .post("/editFeed/"+this.feedId,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )
                .then(response => {
                    if(response.data.success == false){
                        self.$notify({
                            title: 'Error',
                            text: response.data.message,
                            type: 'error',
                            duration: 3000,
                        })
                    }
                    else {
                        self.$notify({
                            title: 'Success',
                            text: response.data.message,
                            type: 'success',
                            duration: 3000,
                        })
                        this.$emit('updated-values', this.newName)
                        if(self.addColumns > 0)
                            self.$emit('new-columns-added')

                        self.resetAddColumnsDropdown()
                    }
                })
                .catch(error => {
                    //display an alert with an error message
                    console.log(error)
                })
        },
        deleteFeed() {
            const self = this;
            axios
                .post('/deleteFeed/'+this.feedId)
                .then(response => {
                    if(response.data.success == false){
                        self.$notify({
                            title: 'Error',
                            text: response.data.message,
                            type: 'error',
                            duration: 3000,
                        })
                    }
                    else {
                        self.$notify({
                            title: 'Success',
                            text: response.data.message,
                            type: 'success',
                            duration: 3000,
                        })
                        self.$emit('deleted-feed')
                    }
                })
                .catch(error => {
                    console.log(error.response.data)
                })
        },
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            document.querySelector(".custom-file-label").innerHTML = this.file.name
        },
        resetAddColumnsDropdown(){
            this.addColumns = 0
        }
    }
}
</script>

<style scoped>

</style>
