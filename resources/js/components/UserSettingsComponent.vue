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
    methods: {
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
