<template>
    <div id="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <h3>{{ user.first_name }} {{ user.last_name }}</h3>
                                <h3>{{ user.email }}</h3>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" id="file" ref="file" class="custom-file-input" v-on:change="handleFileUpload()">
                                        <label class="custom-file-label" for="file" v-if="user.configFileName">{{ user.configFileName}}</label>
                                        <label class="custom-file-label" for="file" v-else>Upload JSON configuration file</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" v-if="user.merchantId" v-model="merchantId">
                                    <input type="text" class="form-control" v-else v-model="merchantId" placeholder="Merchant ID">
                                </div>
                                <a href="#" class="btn btn-primary mt-2" @click="saveChanges">Save</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="user.is_admin == true" class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <h3>Users</h3>
                                <h4 v-for="(user) in company_users">{{ user.first_name }} {{ user.last_name }} - {{ user.email }}<a v-if="user.is_admin == false" style="float:right" @click="deleteUser(user._id)"><i class="fas fa-trash-alt"></i></a></h4>
                                <a href="/showAddUser" class="btn btn-primary mt-2">Add user</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "UserSettingsComponent",
    data() {
        return {
            file: '',
            merchantId: null
        }
    },
    props: {
        user: {
            required: true
        },
        company_users: {
            required: true
        },
    },
    mounted() {
        this.merchantId = this.user['merchantId']
    },
    methods: {
        saveChanges(){
            let formData = new FormData()

            if(this.file)
            {
                formData.append('fileName', this.file.name)
                formData.append('file', this.file)
            }
            if(this.merchantId)
            {
                formData.append('merchantId', this.merchantId)
            }

            axios
                .post( '/uploadConfigFile',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )
                .then(function (response){
                })
                .catch(function (error){
                    console.log(error)
                })
        },
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            document.querySelector(".custom-file-label").innerHTML = this.file.name
        },
        deleteUser(userId) {
            axios
                .post('/deleteUser', {
                    userId: userId
                })
                .then(function (response){
                    window.location = '/settings'
                })
                .catch(function (error){
                    console.log(error.response.data)
                })
        }
    }
}
</script>
