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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="sendData()" data-dismiss="modal">Submit</button>
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
                })
        }
    },
    methods: {
        sendData() {
            axios
                .post("/editFeed/"+this.feedId, {name: this.newName, url: this.newUrl})
                .then(function (response){
                    //display and alert with a success message
                    console.log(response)

                })
                .catch(function (error){
                    //display an alert with an error message
                    console.log(error.response.data)
                })

            this.$emit('updated-values', this.newName)
        }
    }
}
</script>

<style scoped>

</style>
