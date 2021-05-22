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
                            <input type="text" class="form-control" name="newColumnName" v-on:click.once="setDataValues" v-model="newColumnName">
                            <br>
                            <button type="button" class="btn btn-danger btn-block" data-dismiss="modal" @click="deleteColumn()">Delete column</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" @click="changeName()">Submit</button>
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
        }
    },
    data() {
        return {
            newColumnName: null,
            oldColumnName: null,
            visible: null,
            modal: null,
        }
    },
    mounted() {
        this.modal = document.querySelector("#editColumnModal")
    },
    methods: {
        setDataValues() {
            this.oldColumnName = document.querySelector("input[name='newColumnName']").value
            this.newColumnName = this.oldColumnName
        },
        changeName() {
            const self = this
            if(this.oldColumnName == null && this.newColumnName == null) {
                document.querySelector('.closeEditColumnModalBtn').click()
                return
            }
            axios
                .post("/renameColumn/"+this.feedId, {newName: this.newColumnName, oldName: this.oldColumnName})
                .then(function (response){
                    self.$emit('update-columns')
                })
                .catch(function (error){
                    console.log(error)
                })

            self.modal.style.display = "none"
            self.modal.className = "modal fade"
            self.modal.style.opacity = 0
        },
        hideModal() {
            this.modal.style.display = "none"
            this.modal.className = "modal fade"
            this.modal.style.opacity = 0
        },
        deleteColumn() {
            const self = this
            this.setDataValues()
            axios
                .put("/deleteColumn/"+this.feedId, {column: this.oldColumnName})
                .then(function (response){
                    self.$emit('update-columns')
                })
                .catch(function (error){
                    console.log(error)
                })
            this.hideModal()
        }
    }
}
</script>

<style scoped>

</style>
