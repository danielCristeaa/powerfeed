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
          newName: null,
          newUrl: null,
          config: null,
          configFileName: null,
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
                    this.newName = response.data[2]
                    this.newUrl = response.data[3]
                    this.config = response.data[4]
                    this.configFileName = response.data[5]
                    this.merchantId = response.data[6]
                })
        }
    },
    methods: {
        sendData() {
            const self = this
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
                .then(function (response){
                    if("error" in response.data){
                        self.$notify({
                            title: 'Error',
                            text: response.data['error'],
                            type: 'error',
                            duration: 3000,
                        })
                    }
                    else {
                        self.$notify({
                            title: 'Success',
                            text: response.data['success'],
                            type: 'success',
                            duration: 3000,
                        })
                        if(self.addColumns > 0)
                            self.$emit('new-columns-added')

                        self.resetAddColumnsDropdown()
                    }
                })
                .catch(function (error){
                    //display an alert with an error message
                    console.log(error)
                })

            this.$emit('updated-values', this.newName)
        },
        deleteFeed() {
            const self = this;
            axios
                .post('/deleteFeed/'+this.feedId)
                .then(function (response){
                    self.$emit('deleted-feed')
                })
                .catch(function (error){
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
