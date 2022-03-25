<template>
    <div class="modal fade" id="editColumnModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit column</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="hideModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="form-group">
                            <label class="col-form-label">Name</label>
                            <input type="text" class="form-control" name="newColumnName" v-model="newColumnName">
                            <hr>
                            <label class="col-form-label">Replace in cells</label>
                            <input type="text" class="form-control" name="replace" placeholder="Replace" v-model="replaceString">
                            <br>
                            <input type="text" class="form-control" name="with" placeholder="With" v-model="replaceWith">
                            <hr>
                            <button type="button" class="btn btn-danger btn-block" data-dismiss="modal" @click="deleteColumn()">Delete column</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" @click="updateFeed()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EditColumnModal",
    props: {
        feedId: {
            required: true
        },
        openEditColumnModal: {
            required: true
        },
        modal: {
            required: true
        }
    },
    data() {
        return {
            newColumnName: null,
            oldColumnName: null,
            visible: null,
            replaceString: '',
            replaceWith: '',
        }
    },
    watch: {
        openEditColumnModal: function() {
            this.oldColumnName = document.querySelector("input[name='newColumnName']").value
            this.newColumnName = this.oldColumnName
        }
    },
    methods: {
        updateFeed() {
            const self = this
            axios
                .put("/editColumn/"+this.feedId, {
                    replace: encodeURI(this.replaceString),
                    with: encodeURI(this.replaceWith),
                    columnName: this.oldColumnName,
                    newName: this.newColumnName,
                }, )
                .then(response => {
                    if(response.data.success) {
                        self.$emit('update-columns')
                        self.$notify({
                            title: 'Success',
                            text: response.data.message,
                            type: 'success',
                            duration: 3000,
                        })
                    }
                    else {
                        self.$notify({
                            title: 'Error',
                            text: response.data.message,
                            type: 'error',
                            duration: 3000,
                        })
                    }
                })
                .catch(error => {
                    console.log(error.message)
                })

            this.hideModal()
        },
        hideModal() {
            this.modal.style.display = "none"
            this.modal.className = "modal fade"
            this.modal.style.opacity = 0
            this.replaceString = ""
            this.replaceWith = ""
        },
        deleteColumn() {
            const self = this
            axios
                .put("/deleteColumn/"+this.feedId, {column: this.oldColumnName})
                .then(response => {
                    if(response.data.success) {
                        self.$emit('update-columns')
                        self.$notify({
                            title: 'Success',
                            text: response.data.message,
                            type: 'success',
                            duration: 3000,
                        })
                    }
                    else {
                        self.$notify({
                            title: 'Error',
                            text: response.data.message,
                            type: 'error',
                            duration: 3000,
                        })
                    }
                })
                .catch(error => {
                    console.log(error)
                })
            this.hideModal()
        }
    }
}
</script>

<style scoped>

</style>
