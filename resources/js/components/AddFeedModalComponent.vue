<template>
    <v-dialog width="500" v-model="dialog">
        <template v-slot:activator="{ on, attrs }">
            <v-btn class="mb-4" block depressed color="primary" v-bind="attrs" v-on="on"><i class="fas fa-plus fa-lg"></i><b>Add</b></v-btn>
        </template>
        <v-card>
            <v-card-title class="text-h5 grey lighten-2"> Add a Google Merchant feed </v-card-title>

            <v-card-text>
                <v-form>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field v-model="name" label="Name"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field v-model="url" label="URL"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-file-input accept=".json" v-model="file" label="Upload the JSON configuration file"></v-file-input>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field v-model="merchantId" label="Merchant ID"></v-text-field>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="secondary" text @click="dialog = false">Close</v-btn>
                <v-btn color="primary" text @click="sendData()">Submit</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "AddFeedModalComponent",
    data() {
        return {
            dialog: false,
            name: null,
            url: null,
            file: null,
            merchantId: null
        }
    },
    methods:{
        resetFormData(){
            this.name = null
            this.url = null
            this.file = null
            this.merchantId = null
            this.file = null;
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

            this.dialog = false

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
