<template>
    <div id="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ user.first_name }} {{ user.last_name }}</h3>
                            <h3>{{ user.email }}</h3>
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
