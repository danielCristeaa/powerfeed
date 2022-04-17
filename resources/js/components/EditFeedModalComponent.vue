<template>
    <v-dialog width="500" v-model="dialog">
        <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on"><i class="fas fa-edit fa-lg"></i></v-btn>
        </template>
        <v-card>
            <v-card-title class="text-h5 grey lighten-2"> Edit feed </v-card-title>
            <v-card-text>
                <v-form>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field v-model="newName" label="Name"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field v-model="newUrl" label="URL"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-file-input accept=".json" v-model="configFile" label="JSON configuration file"></v-file-input>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field v-model="merchantId" label="Merchant ID"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-select v-model="addColumns" label="Add columns" :items="[1, 2, 3, 4]"></v-select>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" text @click="deleteFeed()">Delete feed</v-btn>
                <v-btn color="secondary" text @click="dialog = false">Close</v-btn>
                <v-btn color="primary" text @click="sendData()">Submit</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
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
          dialog: null,
          oldName: null,
          newName: null,
          oldURL: null,
          newUrl: null,
          oldConfig: null,
          config: null,
          oldConfigFile: null,
          configFile: null,
          oldMerchantId: null,
          merchantId: null,
          addColumns: 0
      }
    },
    watch: {
        dialog(newVal) {
            if(newVal == true) {
                axios
                    .get("/feed/"+this.feedId)
                    .then(response => {
                        this.newName = this.oldName = response.data.data[2]
                        this.newUrl = this.oldURL = response.data.data[3]
                        this.config = this.oldConfig = response.data.data[4]
                        this.configFile = this.oldConfigFile = new File([this.config], response.data.data[5])
                        this.merchantId = this.oldMerchantId = response.data.data[6]
                    })
            }
        },
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

            if(this.configFile) {
                formData.append('fileName', this.configFile.name)
                formData.append('file', this.configFile)
                console.log(this.configFile)
                console.log(this.configFile.name)
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
                        this.$emit('updated-values', {feedId: this.feedId, newName: this.newName})
                        if(self.addColumns > 0)
                            self.$emit('new-columns-added')

                        self.resetAddColumnsDropdown()
                    }
                })
                .catch(error => {
                    //display an alert with an error message
                    console.log(error)
                })

                this.dialog = false
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

                this.dialog = false
        },
        resetAddColumnsDropdown(){
            this.addColumns = 0
        }
    }
}
</script>

<style scoped>

</style>
