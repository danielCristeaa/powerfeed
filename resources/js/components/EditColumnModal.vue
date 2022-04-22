<template>
    <v-dialog v-model="dialog" width="500" @click:outside="hideDialog()">
        <template v-slot:activator="{ on, attrs }">
            <v-btn icon color="orange" x-small v-bind="attrs" v-on="on">
                <v-icon>mdi-cog</v-icon>
            </v-btn>
        </template>
        <v-card>
            <v-card-title class="text-h5 grey lighten-2">Edit column</v-card-title>
            <v-card-text>
                <v-form>
                    <v-row>
                         <v-col cols="12" md="12">
                             <v-text-field v-model="newColumnName" label="Column name"></v-text-field>
                         </v-col>
                    </v-row>
                    <v-row>
                         <v-col cols="12" md="12">
                             <v-text-field v-model="replaceString" label="Replace"></v-text-field>
                         </v-col>
                    </v-row>
                    <v-row>
                         <v-col cols="12" md="12">
                             <v-text-field v-model="replaceWith" label="With"></v-text-field>
                         </v-col>
                    </v-row>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="secondary" text @click="hideDialog()">Close</v-btn>
                <v-btn color="red" text @click="deleteColumn()">Delete column</v-btn>
                <v-btn color="primary" text @click="updateFeed()">Submit</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "EditColumnModal",
    props: {
        feedId: {
            required: true
        },
        columnName: {
            required: true
        }
    },
    data() {
        return {
            dialog: false,
            newColumnName: null,
            oldColumnName: null,
            visible: null,
            replaceString: '',
            replaceWith: '',
        }
    },
    watch: {
    },
    methods: {
        updateFeed() {
            const self = this
            axios
                .put("/editColumn/"+this.feedId, {
                    replace: encodeURI(this.replaceString),
                    with: encodeURI(this.replaceWith),
                    columnName: this.columnName,
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

            this.hideDialog()
        },
        hideDialog() {
            this.dialog = false
            this.replaceString = ""
            this.replaceWith = ""
        },
        deleteColumn() {
            const self = this
            axios
                .put("/deleteColumn/"+this.feedId, {column: this.columnName})
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
            this.hideDialog()
        }
    }
}
</script>

<style scoped>

</style>
